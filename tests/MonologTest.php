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

use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author jan <jan@bestbytes.de>
 */
class MonologTest extends TestCase {

	public function testLog()
	{
		// create a log channel
		$log = new \Monolog\Logger('UserEvent');
		$log->pushHandler(new \Monolog\Handler\StreamHandler(Module::getLogDir() . DIRECTORY_SEPARATOR . 'mono.log', \Monolog\Logger::INFO));

		$errorLogHandler = new \Monolog\Handler\ErrorLogHandler(
			\Monolog\Handler\ErrorLogHandler::OPERATING_SYSTEM, \Monolog\Logger::ERROR
		);
		$errorLogHandler->pushProcessor(new IntrospectionProcessor());
		$errorLogHandler->pushProcessor(new MemoryUsageProcessor());
		$log->pushHandler($errorLogHandler);

		// add records to the log
		$log->addWarning('Foo');
		$log->addError('Bar');

		$log->addInfo('added product with sku {sku} to cart', array('sku' => '12345.foo'));
	}

}