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

	/**
	 * channel configuration
	 *
	 * @var array
	 */
	public $channels = [
		'default' => [
			'handlers' => [
				[
					'class' => '\\Monolog\\Handler\\StreamHandler',
					'path' => 'php://stderr',
					'level' => \Monolog\Logger::ERROR,
					'bubble' => false,
					'enabled' => true
				]
			]
		],
		'performance' => [
			'handlers' => [
				[
					'class' => '\\Monolog\\Handler\\StreamHandler',
					'path' => 'php://stderr',
					'level' => \Monolog\Logger::ERROR,
					'bubble' => false,
					'enabled' => true
				]
			],
			'processors' => [
				[
					'class' => '\\Monolog\\Processor\\WebProcessor',
					'enabled' => true
				]
			]
		],
		'app' => [
			'handlers' => [
				[
					'class' => '\\Monolog\\Handler\\StreamHandler',
					'stream' => 'php://stderr',
					'level' => \Monolog\Logger::ERROR,
					'bubble' => false,
					'enabled' => true
				],
				[
					'class' => '\\Monolog\\Handler\\StreamHandler',
					'stream' => '/var/www/craemer/var/test/logs/test1.log',
					'level' => \Monolog\Logger::INFO,
					'bubble' => true,
					'enabled' => false
				],
				[
					'class' => '\\Monolog\\Handler\\RotatingFileHandler',
					'filename' => '/var/www/craemer/var/test/logs',
					'maxFiles' => 0,
					'level' => \Monolog\Logger::INFO,
					'bubble' => true,
					'enabled' => false
				],
				[
					'class' => '\\Monolog\\Handler\\MongoDBHandler',
					'server' => "mongodb://localhost:27017",
					'database' => 'logsDb',
					'collection' => 'logs',
					'level' => \Monolog\Logger::INFO,
					'bubble' => true,
					'enabled' => false
				],
				[
					'class' => '\\Monolog\\Handler\\FoomoMailHandler',
					'from' => 'test@test.com',
					'to' => 'test@test.com',
					'reply-to' => 'test@test.com',
					'subject' => 'test.subject',
					'level' => \Monolog\Logger::INFO,
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
				# pre processor
				$processors = self::getProcessors($channelConfig['processors']);
				foreach ($processors as $processor) {
					$logger->pushProcessor($processor);
				}
				# handler
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
	 * @param array $handlersConfig
	 *
	 * @return array
	 */
	private static function getHandlers($handlersConfig)
	{
		$handlers = [];
		foreach ($handlersConfig as $handlerData) {
			if($handlerData['enabled'] && class_exists($handlerData['class'])) {
				switch ($handlerData['class']) {
					case '\\Monolog\\Handler\\MongoDBHandler':
						$handlers[] = new MongoDBHandler(new \MongoClient($handlerData['server']), $handlerData['database'], $handlerData['collection'], $handlerData['level'], $handlerData['bubble']);
						break;
					default:
						$handlers[] = self::instanceByReflection($handlerData);
				}
			}
		}
		return $handlers;
	}

	/**
	 * @param array $handlersConfig
	 *
	 * @return array
	 */
	private static function getProcessors($handlersConfig)
	{
		$processors = [];
		foreach ($handlersConfig as $handlerData) {
			if($handlerData['enabled'] && class_exists($handlerData['class'])) {
				$processors[] = self::instanceByReflection($handlerData);
			}
		}
		return $processors;
	}

	/**
	 * @param array $data
	 *
	 * @return object instance
	 */
	private static function instanceByReflection($data)
	{
		$reflection = new \ReflectionClass($data['class']);
		$constructor = $reflection->getConstructor();
		$parameters = [];
		$parameterReflection = $constructor->getParameters();
		foreach($parameterReflection as $parameter) {
			$parameterName = $parameter->getName();
			if(array_key_exists($parameterName, $data)) {
				$parameters[] = $data[$parameterName];
			} else if(!$parameter->isOptional()) {
				trigger_error('invalid monolog configuration: non-optional parameter ' . $parameterName . ' for ' . $data['class'] . ' is missing', E_USER_ERROR);
			}
		}
		return $reflection->newInstanceArgs($parameters);
	}
}