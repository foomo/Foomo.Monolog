<?php

namespace Foomo\Monolog;

use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\FoomoMailHandler;
use Monolog\Handler\MongoDBHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;


class DomainConfig extends \Foomo\Config\AbstractConfig
{
	const NAME = 'Foomo.Monolog.config';
	public $channels = [

		'app' => [
			'handlers' => [
				'StreamHandler' => [
					'path' => '/var/www/craemer/var/test/logs/test1.log',
					'level' => 'INFO',
					'bubble' => true,
					'enabled' => false
				],
				'RotatingFileHandler' => [
					'path' => '/var/www/craemer/var/test/logs',
					'max_files' => 0,
					'level' => 'INFO',
					'bubble' => true,
					'enabled' => false
				],
				'ErrorLogHandler' => [
					'level' => 'INFO',
					'bubble' => true,
					'enabled' => false
				],
				'MongoDBHandler' => [
					'server' => "mongodb://localhost:27017",
					'database' => 'logsDb',
					'collection' => 'logs',
					'level' => 'INFO',
					'bubble' => true,
					'enabled' => false
				],
				'FoomoMailHandler' => [
					'from' => 'test@test.com',
					'to' => 'test@test.com',
					'reply-to' => 'test@test.com',
					'subject' => 'test.subject',
					'level' => 'INFO',
					'bubble' => true,
					'enabled' => false
				]
			]
		]
	];

	/**
	 * get channel named default or if not exists, the first defined
	 * @return string
	 */
	public function getDefaultChannel() {
		$ret = false;
		foreach ($this->channels as $confChannel => $channelConfig) {
			if (empty($ret)) {
				$ret = $confChannel;
			} else if ($confChannel == 'default') {
				$ret = $confChannel;
			}
		}
		return $ret;
	}

	/**
	 * @param string $channel
	 * @return \Monolog\Logger
	 */
	public function getLogger($channel)
	{
		$logger = new \Monolog\Logger($channel);
		foreach ($this->channels as $confChannel => $channelConfig) {
			if ($channel == $confChannel) {
				$handlers = self::getHandlers($channelConfig['handlers']);
				foreach ($handlers as $handler) {
					$logger->pushHandler($handler);
				}
				return $logger;
			}
		}
		return $logger;
	}

	/**
	 * @param array $handlersConfig handlerName => [handler_params]
	 * @return array
	 */
	private static function getHandlers($handlersConfig)
	{
		$ret = [];

		$handlersConfig = self::setDefaults($handlersConfig);

		foreach ($handlersConfig as $handlerName => $handlerData) {
			switch ($handlerName) {
				case 'StreamHandler':
					if ($handlerData['enabled']) {
						$handler = new StreamHandler($handlerData['path'], $handlerData['level'], $handlerData['bubble']);
						$ret[] = $handler;
					}
					break;
				case 'RotatingFileHandler':
					if ($handlerData['enabled']) {
						$handler = new RotatingFileHandler($handlerData['path'], $handlerData['max_files'], $handlerData['level'], $handlerData['bubble']);
						$ret[] = $handler;
					}
					break;
				case 'ErrorLogHandler':
					if ($handlerData['enabled']) {
						$handler = new ErrorLogHandler(0, $handlerData['level'], $handlerData['bubble']);
						$ret[] = $handler;
					}
					break;
				case 'MongoDBHandler':
					if ($handlerData['enabled']) {
						$handler = new MongoDBHandler(new \MongoClient($handlerData['server']), $handlerData['database'], $handlerData['collection'], $handlerData['level'], $handlerData['bubble']);
						$ret[] = $handler;
					}
					break;
				case 'FoomoMailHandler':
					if ($handlerData['enabled']) {
						$handler = new FoomoMailHandler($handlerData['to'], $handlerData['subject'], $handlerData['from'], $handlerData['reply-to'], $handlerData['level'], $handlerData['bubble']);
						$ret[] = $handler;
					}
					break;
				default:
					trigger_error('unknown handler ' . $handlerName, E_USER_WARNING);
			}
		}
		return $ret;
	}

	/**
	 * @param array $handlersConfig handlerName => [handler_params]
	 * @return array
	 */
	private static function setDefaults($handlersConfig)
	{
		$ret = [];
		foreach ($handlersConfig as $handlerName => $handlerData) {
			switch ($handlerName) {
				case 'StreamHandler':
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'path', $defaultValue = Module::getLogDir() . DIRECTORY_SEPARATOR . 'default.log');
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'level', $defaultValue = \Monolog\Logger::DEBUG);
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'bubble', $defaultValue = true);
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'enabled', $defaultValue = false);
					$ret[$handlerName] = $handlerData;
					break;
				case 'RotatingFileHandler':
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'path', $defaultValue = Module::getLogDir() . DIRECTORY_SEPARATOR . 'default-rotating.log');
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'level', $defaultValue = \Monolog\Logger::DEBUG);
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'bubble', $defaultValue = true);
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'max_files', $defaultValue = 0);
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'enabled', $defaultValue = false);
					$ret[$handlerName] = $handlerData;
					break;
				case 'ErrorLogHandler':
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'level', $defaultValue = \Monolog\Logger::DEBUG);
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'bubble', $defaultValue = true);
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'enabled', $defaultValue = false);
					$ret[$handlerName] = $handlerData;
					break;
				case 'MongoDBHandler':
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'server', $defaultValue = 'mongodb://localhost:27017');
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'database', $defaultValue = 'logsDb');
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'collection', $defaultValue = 'logs');
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'level', $defaultValue = \Monolog\Logger::DEBUG);
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'bubble', $defaultValue = true);
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'enabled', $defaultValue = false);
					$ret[$handlerName] = $handlerData;
					break;
				case 'FoomoMailHandler':
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'from', $defaultValue = 'from@test.com');
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'to', $defaultValue = 'to@test.com');
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'reply-to', ($defaultValue = $handlerData['from'] ? $handlerData['from'] : 'reply-to@test.com'));
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'subject', $defaultValue = 'subject');
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'level', $defaultValue = \Monolog\Logger::DEBUG);
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'bubble', $defaultValue = true);
					$handlerData = self::setDefaultIfNoVal($handlerData, $propertyName = 'enabled', $defaultValue = false);
					$ret[$handlerName] = $handlerData;
					break;
				default:
					trigger_error('unknown handler ' . $handlerName, E_USER_WARNING);
			}
		}
		return $ret;
	}

	/**
	 * @param array $handlerData
	 * @param string $propertyName
	 * @param mixed $defaultValue
	 * @return array
	 */
	private static function setDefaultIfNoVal($handlerData, $propertyName, $defaultValue)
	{
		if (!isset($handlerData[$propertyName])) {
			$handlerData[$propertyName] = $defaultValue;
		}
		return $handlerData;
	}
}

