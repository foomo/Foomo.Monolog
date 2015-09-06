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

namespace Foomo\Monolog\Utils;

use Monolog\Handler\AbstractHandler;
use Monolog\Handler\MongoDBHandler;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author frederik <frederik@zitrusmedia.de>
 */
class Channel {

	/**
	 * @param array $processorConfig
	 *
	 * @return bool|object
	 */
	public static function getProcessor($processorConfig)
	{
		if($processorConfig['enabled'] && class_exists($processorConfig['class'])) {
			return self::instanceByReflection($processorConfig);
		}
		return false;
	}

	/**
	 * @param array $handlerConfig
	 *
	 * @return AbstractHandler|bool
	 */
	public static function getHandler($handlerConfig)
	{
		if($handlerConfig['enabled'] && class_exists($handlerConfig['class'])) {

			# formatter
			if(isset($handlerConfig['formatter']) && is_array($handlerConfig['formatter']) && array_key_exists('class', $handlerConfig['formatter'])) {
				$formatter = self::instanceByReflection($handlerConfig['formatter']);
				unset($handlerConfig['formatter']);
			} else {
				$formatter = new \Monolog\Formatter\LineFormatter();
			}

			# processors
			$processors = [];
			if(isset($handlerConfig['processors']) && is_array($handlerConfig['processors'])) {
				foreach($handlerConfig['processors'] as $processorConfig) {
					if(is_array($processorConfig) && array_key_exists('class', $processorConfig)) {
						$processors[] = self::instanceByReflection($processorConfig);
					}
				}
				unset($handlerConfig['processors']);
			}


			# convert log level from string to int
			if(isset($handlerConfig['level']) && is_string($handlerConfig['level'])) {
				$handlerConfig['level'] = constant($handlerConfig['level']);
			}
			if(is_null($handlerConfig['level']) || !is_numeric($handlerConfig['level'])) {
				$handlerConfig['level'] = \Monolog\Logger::DEBUG;
			}

			switch ($handlerConfig['class']) {
				case '\\Monolog\\Handler\\MongoDBHandler':
					$handler = new MongoDBHandler(new \MongoClient($handlerConfig['server']), $handlerConfig['database'], $handlerConfig['collection'], $handlerConfig['level'], $handlerConfig['bubble']);
					break;
				default:
					$handler = self::instanceByReflection($handlerConfig);
			}

			$handler->setFormatter($formatter);
			foreach($processors as $processor) {
				$handler->pushProcessor($processor);
			}
			return $handler;
		}
		return false;
	}

	/**
	 * @param array $data
	 *
	 * @return object instance
	 */
	private static function instanceByReflection($data)
	{
		$parameters = [];
		$reflection = new \ReflectionClass($data['class']);
		$constructor = $reflection->getConstructor();
		if($constructor) {
			$parameterReflection = $constructor->getParameters();
			foreach($parameterReflection as $parameter) {
				$parameterName = $parameter->getName();
				if(array_key_exists($parameterName, $data)) {
					$parameters[] = $data[$parameterName];
				} else if(!$parameter->isOptional()) {
					trigger_error('invalid monolog configuration: non-optional parameter ' . $parameterName . ' for ' . $data['class'] . ' is missing', E_USER_ERROR);
				}
			}
		}
		return $reflection->newInstanceArgs($parameters);
	}

}