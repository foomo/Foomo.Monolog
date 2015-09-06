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

namespace Foomo\Monolog\Config;

use Foomo\Monolog\Module;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  frederik <frederik@zitrusmedia.de>
 */
class Channel extends \Foomo\Config\AbstractConfig
{
	const NAME = 'Foomo.Monolog.channel';

	/**
	 * @var array
	 */
	public $handlers = [];

	/**
	 * @var array
	 */
	public $processors = [];

	public function __get($property)
	{
		if($property == 'handlers' && empty($this->handlers)) {
			return $this->getDefaultHandlerConfig();
		}
		if(property_exists($this, $property)) {
			return $this->$property;
		}
		trigger_error('access to an undefined property ' .$property, E_USER_WARNING);
	}

	/**
	 * get the configuration array
	 *
	 * @internal
	 * @return array
	 */
	public function getValue()
	{
		$ret = parent::getValue();
		if(empty($this->handlers)) {
			$ret['handlers'] = $this->getDefaultHandlerConfig();
		}
		return $ret;
	}

	/**
	 * return a default handler config with some dynamic values
	 * @return array
	 */
	private function getDefaultHandlerConfig()
	{
		$handlers = [
			[
				'class'       => '\\Monolog\\Handler\\ErrorLogHandler',
				'messageType' => 0,
				'level'       => '\\Monolog\\Logger::ERROR',
				'bubble'      => false,
				'enabled'     => true,
				'formatter'   => [
					'class' => '\\Foomo\\Monolog\\Formatter\\StacktraceableLineFormatter',
					'includeStacktraces' => true
				],
				'processors'  => [
					[
						'class' => '\\Monolog\\Processor\\WebProcessor'
					]
				]
			],
			[
				'class'   => '\\Monolog\\Handler\\StreamHandler',
				'stream'  => Module::getLogDir() . DIRECTORY_SEPARATOR . 'debug.log',
				'level'   => '\\Monolog\\Logger::DEBUG',
				'bubble'  => true,
				'enabled' => true,
				'processors'  => [
					[
						'class' => '\\Foomo\\Monolog\\Processor\\SessionProcessor'
					]
				]
			]
		];
		return $handlers;
	}

}