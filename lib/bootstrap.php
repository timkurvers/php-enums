<?php
/**
 * PHP Enums Library
 * Copyright (c) 2011 Tim Kurvers <http://www.moonsphere.net>
 * 
 * This library provides enum functionality similar to the implementation
 * found in Java. It differs from existing libraries by offering one-shot
 * enum constructors through static initialization, enum iteration as well
 * as equality support and value, ordinal and binary lookups.
 * 
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 * 
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License version 3 license (the "GPLv3"), in which
 * case the provisions of the GPLv3 are applicable instead of the above.
 * 
 * @author	Tim Kurvers <tim@moonsphere.net>
 */

if(!defined('PHP_ENUMS_DIR')) {
	define('PHP_ENUMS_DIR', dirname(__FILE__));
}
if(!defined('PHP_ENUMS_DS')) {
	define('PHP_ENUMS_DS', DIRECTORY_SEPARATOR);
}

require(PHP_ENUMS_DIR.PHP_ENUMS_DS.'Enum.class.php');
require(PHP_ENUMS_DIR.PHP_ENUMS_DS.'EnumSet.class.php');
require(PHP_ENUMS_DIR.PHP_ENUMS_DS.'EnumSetIterator.class.php');
require(PHP_ENUMS_DIR.PHP_ENUMS_DS.'EnumException.class.php');
