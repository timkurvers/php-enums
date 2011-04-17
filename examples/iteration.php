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

error_reporting(-1);

require('../lib/bootstrap.php');

/**
 * This example shows how to iterate over defined enumerations
 */

// Each country has a unique entry calling code 
class CountryEntryCode extends Enum {
	
	const NETHERLANDS       = 31;
	const NORWAY            = 47;
	const UNITED_KINGDOM    = 44;
	
}

// As well as an optional exit code (note that enum members can have non-unique values!)
class CountryExitCode extends Enum {

	const NETHERLANDS       = 00;
	const NORWAY            = 00;
	
}

// To generate a list of all country entry codes we have defined, use every enum-type's enums() method
$list = CountryEntryCode::enums();

// This list is an array of a name (say, NETHERLANDS) and the actual enum instance 
foreach($list as $name=>$enum) {
	echo $enum;
}

// To generate a list of all enums we have used (globally), use Enum::enums()
// Note, Enum::enums() only lists enums used previously. CountryExitCode has not been used in our script yet, so do so prior to generating the list
CountryExitCode::NETHERLANDS();
$all = Enum::enums();

// This list is an array of an enum-type (say, CountryEntryCode) and the list seen above
foreach($all as $class=>$list) {
	foreach($list as $name=>$enum) {
		echo $enum;	
	}	
}

