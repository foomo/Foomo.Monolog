<?php

namespace Foomo\Monolog;

class Logger
{

	private static $traceId;

	public static function getTraceId()
	{
		if (is_null(self::$traceId)) {
			$sessionId = \Foomo\Session::getSessionIdIfEnabled();
			if (is_null($sessionId)) {
				$sessionId = uniqid() . time();
			}
			self::$traceId = sha1($sessionId);
		}
		return self::$traceId;
	}

	/**
	 * log
	 *
	 * @param string $message
	 * @param int $level something like \Monolog\Logger::DEBUG...
	 * @param string $channel
	 * @param mixed $context
	 *
	 * @return bool
	 */
	public static function log($message, $level = \Monolog\Logger::DEBUG, $channel = 'app', $context = [])
	{
		$logger = \Foomo\Monolog\Module::getLogger($channel);
		return $logger->addRecord($level, $message, $context);
	}

	/**
	 * log with traceID
	 *
	 * @param string $message
	 * @param string $level \Monolog\Logger::DEBUG...
	 * @param string $channel
	 */
	public static function applicationLog($message, $level, $channel = 'app')
	{
		self::log($message, $level, $channel, self::getTraceId());
	}

	/**
	 * get the default channel name
	 * @return string default channel name
	 */
	public static function getDefaultChannel() {
		return \Foomo\Monolog\Module::getModuleConfig()->getDefaultChannel();
	}

}