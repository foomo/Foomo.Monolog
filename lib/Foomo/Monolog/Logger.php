<?php

namespace Foomo\Monolog;

class Logger {

	private static $traceId;

	public static function getTraceId()
	{
		if(is_null(self::$traceId)) {
			$sessionId = \Foomo\Session::getSessionIdIfEnabled();
			if(is_null($sessionId)) {
				$sessionId = uniqid() . time();
			}
			self::$traceId = sha1($sessionId);
		}
		return self::$traceId;
	}

	public static function log($message, $level)
	{
		//@todo implement me
	}

	public static function applicationLog($message, $level)
	{
		//@todo implement me
//		$this->application->addRecord($message, $level, self::getTraceId());
	}

}