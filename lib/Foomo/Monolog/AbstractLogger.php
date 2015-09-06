<?php

/*
 * This file is part of the foomo Opensource Framework.
 *
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Foomo\Monolog;

use Foomo\Modules\Manager;
use Monolog\Logger;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author frederik <frederik@zitrusmedia.de>
 */
abstract class AbstractLogger extends Logger {

	/**
	 * logger channel name
	 */
	const NAME = 'default';

	/**
	 * @var Logger[]
	 */
	protected static $instances;

	/**
	 * construct a logger
	 *
	 * @param string $channelName
	 * @param string $moduleName
	 *
	 */
	public function __construct($channelName = self::NAME, $moduleName = Module::NAME)
	{
		$name = static::getModuleChannelName($channelName, $moduleName);
		parent::__construct($name);
		$this->init($channelName, $moduleName);
	}

	/**
	 * @param string $channelName
	 * @param string $moduleName
	 *
	 * @return Logger
	 */
	public static function getLogger($channelName = self::NAME, $moduleName = Module::NAME)
	{
		$name = static::getModuleChannelName($channelName, $moduleName);
		if(is_null(static::$instances[$name])) {
			static::$instances[$name] = new static($channelName, $moduleName);
		}
		return static::$instances[$name];
	}

	/**
	 * @param string $channelName
	 * @param string $moduleName
	 *
	 * @return string
	 */
	private static function getModuleChannelName($channelName, $moduleName)
	{
		return $moduleName . '.' . ucfirst($channelName);
	}

	protected function getConfigDomainName($channelName)
	{
		return 'monolog-' . $channelName;
	}

	/**
	 * @param string $channelName
	 * @param string $moduleName
	 *
	 * @return Config\Channel
	 */
	protected function getLoggerConfig($channelName, $moduleName)
	{
		if(
			Manager::isEnabled($moduleName) &&
			\Foomo\Config::confExists($moduleName, \Foomo\Monolog\Config\Channel::NAME, $this->getConfigDomainName($channelName))
		) {
			return \Foomo\Config::getConf($moduleName, \Foomo\Monolog\Config\Channel::NAME, $this->getConfigDomainName($channelName));
		} else {
			return Module::getDefaultChannelConfig();
		}
	}

	/**
	 * @param string $channelName
	 * @param string $moduleName
	 *
	 * @return Logger $this
	 */
	protected function init($channelName, $moduleName)
	{
		# load config
		$config = $this->getLoggerConfig($channelName, $moduleName);

		# add processors
		foreach(array_reverse($config->processors) as $processorConfig) {
			$processor = Utils\Channel::getProcessor($processorConfig);
			if($processor) {
				$this->pushProcessor($processor);
			}
		}

		# add handlers
		foreach(array_reverse($config->handlers) as $handlerConfig) {
			$handler = Utils\Channel::getHandler($handlerConfig);
			if($handler) {
				$this->pushHandler($handler);
			}
		}

		# return logger
		return $this;
	}
}