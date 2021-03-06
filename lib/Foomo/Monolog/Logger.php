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
 * @author frederik <frederik@zitrusmedia.de>
 */
class Logger extends AbstractLogger
{

	/**
	 * logger channel name
	 */
	const NAME = 'default';

	/**
	 * @deprecated
	 *
	 * @return string
	 */
	public static function getTraceId()
	{
		trigger_error('Use \Foomo\Monolog\Processor\SessionProcessor::getTraceId() instead of ' . __METHOD__, E_USER_DEPRECATED);
		return \Foomo\Monolog\Processor\SessionProcessor::getTraceId();
	}

}