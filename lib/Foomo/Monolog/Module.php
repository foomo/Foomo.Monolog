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

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
class Module extends \Foomo\Modules\ModuleBase
{
	//---------------------------------------------------------------------------------------------
	// ~ Constants
	//---------------------------------------------------------------------------------------------

	const NAME = 'Foomo.Monolog';
	const VERSION = '0.1.0';

	//---------------------------------------------------------------------------------------------
	// ~ Overriden static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * Your module needs to be set up, before being used - this is the place to do it
	 */
	public static function initializeModule()
	{

	}

	/**
	 * Get a plain text description of what this module does
	 *
	 * @return string
	 */
	public static function getDescription()
	{
		return 'foomo monolog wrapper';
	}

	/**
	 * get all the module resources
	 *
	 * @return \Foomo\Modules\Resource[]
	 */
	public static function getResources()
	{
		return array(
			\Foomo\Modules\Resource\Module::getResource('Foomo', '0.3.*'),
			\Foomo\Modules\Resource\ComposerPackage::getResource('monolog/monolog', '*.*.*'), //Monolog bundle
			\Foomo\Modules\Resource\Config::getResource(self::NAME, 'Foomo.Monolog.config')
		);
	}


	/**
	 * @var array
	 */
	private static $loggers = [];

	/**
	 * get logger singleton
	 * @param $channel
	 * @return \Monolog\Logger
	 */

	public static function getLogger($channel)
	{
		if (!isset(self::$loggers[$channel])) {
			$config = \Foomo\Config::getConf(self::NAME, \Foomo\Monolog\DomainConfig::NAME);
			self::$loggers[$channel] = $config->getLogger($channel);
		}
		return self::$loggers[$channel];
	}
}