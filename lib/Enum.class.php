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

/**
 * Denotes the abstract Enum base-class to be used for all enum-types
 */
abstract class Enum {
	
	/**
	 * Holds all enums defined for this enum-type paired by name/instance
	 */
	protected static $___pool = array();
	
	/**
	 * Holds all values for this enum-type paired by name/value
	 */
	protected static $___values = null;
	
	/**
	 * Whether this enum-type is currently in its initialization phase
	 */
	protected static $___init = false;
	
	/**
	 * Name of this enum
	 */
	private $___name = null;
	
	/**
	 * Ordinal (offset) value of this enum
	 */
	private $___ordinal = null;
	
	/**
	 * Value of this enum
	 */
	protected $___value = null;
	
	/**
	 * Constructs an enum with given name and value, calculates the ordinal value and pools the enum and its value by name
	 */
	final private function __construct($name, $value=null) {
		$this->___name = $name;
		$this->___value = $value;
		$this->___ordinal = count(static::$___pool);
		
		// Ensure the enum-type has this new enum in its pool
		static::$___pool[$name] = $this;
		
		// Ensure that if a value is changed internally it is reflected in the value pool
		static::$___values[$name] =& $this->___value;
	}
	
	/**
	 * Prevents cloning of this enum
	 */
	final private function __clone() { }
	
	/**
	 * When invoked conveniently returns this enum's value
	 */
	protected function __invoke() {
		return $this->___value;
	}
	
	/**
	 * Human-readable presentation of this enum
	 */
	public function __toString() {
		return '['.get_class($this).'::'.$this->___name.'; Value: '.(($this->___value === null) ? 'null' : $this->___value).'; Ordinal: '.$this->___ordinal.'; Binary: 0x'.dechex($this->getBinary()).']';
	}
	
	/**
	 * Whether this enum equals the given value (compares enum instances, names (string) and values)
	 */
	final public function equals($value) {
		if($value === $this) {
			return true;
		}else if(is_string($value) && $value === $this->___name) {
			return true;
		}else if($this->___value !== null && $value === $this->___value) {
			return true;
		}
		return false;
	}
	
	/**
	 * Returns the name of this enum
	 */
	final public function getName() {
		return $this->___name;
	}
	
	/**
	 * Returns the value of this enum
	 */
	public function getValue() {
		return $this->___value;
	}
	
	/**
	 * Returns the ordinal (offset) of this enum
	 */
	final public function getOrdinal() {
		return $this->___ordinal;
	}
	
	/**
	 * Returns the binary flag of this enum
	 */
	final public function getBinary() {
		return 1 << $this->___ordinal;
	}
	
	/**
	 * Placeholder static initializer per enum-type
	 */
	protected static function __() { }
	
	/**
	 * Static initializer; Initializes the enum and value pools and constructs all enums (defined as class constants or called through the static initializer above) 
	 */
	final public static function ___static($cls) {
		
		// Ensure this enum-type has a unique enum pool to itself (de-references Enum's), and additionally reference this enum-type's pool in Enum's 
		$pool = array();
		static::$___pool =& $pool;
		self::$___pool[$cls] =& $pool;
		
		// Ensure a unique value pool, too
		$values = array();
		static::$___values =& $values;
		
		// As well as a unique initialization phase
		$init = true;
		static::$___init =& $init;
		
		// For each constant, set up an enum
		$reflect = new ReflectionClass($cls);
		$constants = $reflect->getConstants();
		foreach($constants as $name=>$value) {
			static::___setup($name, $value);
		}
		
		// Call the static initializer for this enum-type and leave the initialization phase
		static::__();
		$init = false;
		
		// Number of required arguments on the enum constructor or null if there is none
		$args = ($reflect->hasMethod(EnumConstants::CONSTRUCTOR)) ? $reflect->getMethod(EnumConstants::CONSTRUCTOR)->getNumberOfRequiredParameters() : null;
		
		// Loop through all enums and call the aforementioned constructor with previously stored arguments (@see Enum::___setup)
		foreach(static::$___pool as $enum) {
			if(count($enum->___args) < (int)$args) {
				trigger_error('Missing argument '.(count($enum->___args) + 1).' for '.get_class($enum).'::___construct() while initializing enum '.get_class($enum).'::'.$enum->getName(), E_USER_WARNING);
				$enum->___args = array_pad($enum->___args, $args, null);
			}
			if($args !== null) {
				call_user_func_array(array($enum, EnumConstants::CONSTRUCTOR), $enum->___args);
			}
			unset($enum->___args);
		}
	}
	
	/**
	 * Handles all enum calls and re-routes them for construction or fetch (depending on the initialization phase of the enum-type)
	 */
	final public static function __callStatic($name, array $args) {
		if(static::$___values === null && $cls = get_called_class()) {
			if($cls === __CLASS__) {
				return null;
			}
			static::___static($cls);
		}
		// When initializing, set up the enum; Otherwise, attempt to just fetch it
		if(static::$___init) {
			return static::___setup($name, null, $args);
		}else{
			$enum = static::get($name);
			if(!$enum) {
				throw new EnumException('Enum '.get_called_class().'::'.$name.' could not be found');
				return null;
			}
			return $enum;
		}
	}
	
	/**
	 * Sets up enum by given name, value and constructor arguments
	 */
	final protected static function ___setup($name, $value=null, array $args=array()) {
		if(!isset(static::$___pool[$name])) {
			$enum = new static($name, $value);
		}else{
			$enum = static::$___pool[$name];
		}
		// Ensure arguments can not be attached multiple times
		if(isset($enum->___args) && $enum->___args && $args) {
			throw new EnumException('Cannot re-construct '.get_class($enum).'::'.$enum->getName().', arguments previously provided in '.get_class($enum).'::__()');
			return null;
		}
		if($args || !isset($enum->___args)) {
			$enum->___args = $args;
		}
		return $enum;
	}
	
	/**
	 * Fetches enum by given name (if any)
	 */
	final public static function get($name) {
		if(static::$___values === null && $cls = get_called_class()) {
			if($cls === __CLASS__) {
				return null;
			}
			static::___static($cls);
		}
		if(isset(static::$___pool[$name])) {
			return static::$___pool[$name];
		}
		return null;
	}
	
	/**
	 * Fetches enum by given value (if any)
	 */
	public static function byValue($value) {
		if(static::$___values === null && $cls = get_called_class()) {
			if($cls === __CLASS__) {
				return null;
			}
			static::___static($cls);
		}
		
		$name = array_search($value, static::$___values, true);
		if($name !== false) {
			return static::$___pool[$name];
		}
		return null;
	}
	
	/**
	 * Fetches enum by given ordinal offset (if any)
	 */
	public static function byOrdinal($ordinal) {
		if(static::$___values === null && $cls = get_called_class()) {
			if($cls === __CLASS__) {
				return null;
			}
			static::___static($cls);
		}
		
		$slice = array_slice(static::$___pool, $ordinal, 1);
		if($slice) {
			return current($slice);
		}
		return null;
	}
	
	/**
	 * Fetches enum by given binary flag (if any); Multiple enums can be returned as an array when using bitflags
	 */
	public static function byBinary($binary, $singular=true) {
		if(static::$___values === null && $cls = get_called_class()) {
			if($cls === __CLASS__) {
				return ($singular) ? null : array();
			}
			static::___static($cls);
		}
		
		$enums = array();
		
		$count = count(static::$___pool);
		for($i=0; $i<$count; ++$i) {
			if($binary & (1 << $i)) {
				$enums[] = current(array_slice(static::$___pool, $i, 1));
			}
		}
		
		$count = count($enums);
		if($singular && $count <= 1) {
			return ($enums) ? current($enums) : null;
		}else{
			return $enums;
		}
	}
	
	/**
	 * Returns the list of all defined enums for this enum-type; Alternatively, Enum::enums() returns a map of all enum-types and their defined enums
	 */
	final public static function enums() {
		if(static::$___values === null && $cls = get_called_class()) {
			if($cls === __CLASS__) {
				return self::$___pool;
			}
			static::___static($cls);
		}
		return static::$___pool;
	}
	
}

/**
 * Helper interface with constants required by this library
 */
interface EnumConstants {
	
	/**
	 * Enum constructor method name (differs from the regular PHP constructor by an additional underscore)
	 */
	const CONSTRUCTOR = '___construct';
	
}
