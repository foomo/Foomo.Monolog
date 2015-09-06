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

namespace Foomo\Monolog\Formatter;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  frederik <frederik@zitrusmedia.de>
 */
class StacktraceableLineFormatter extends \Monolog\Formatter\LineFormatter {

	/**
     * @param bool   $includeStacktraces Whether to print an exception's stacktrace
     * @param string $format                The format of the message
     * @param string $dateFormat            The format of the timestamp: one supported by DateTime::format
     * @param bool   $allowInlineLineBreaks Whether to allow inline line breaks in log entries
     */
    public function __construct($includeStacktraces = true, $format = null, $dateFormat = null, $allowInlineLineBreaks = false)
	{
		parent::__construct($format, $dateFormat, $allowInlineLineBreaks);
		$this->includeStacktraces($includeStacktraces);
	}

}