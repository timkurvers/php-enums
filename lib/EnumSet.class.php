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
 * The contents of this file are subject to the MIT License, under which 
 * this library is licensed. See the LICENSE file for the full license.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY 
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * @author	Tim Kurvers <tim@moonsphere.net>
 */

/**
 * Enumeration set holding enums of a certain type
 */
class EnumSet implements IteratorAggregate, Countable {
	
	/**
	 * Type of enums stored in this set
	 */
	protected $_type = null;
	
	/**
	 * Actual collection of enums in this set
	 */
	protected $_set = array();
	
	/**
	 * Binary mask for this entire set, calculated by EnumSet::getBinary()
	 */
	protected $_binary = null;
	
	/**
	 * Constructs a new enumeration set for given class
	 */
	protected function __construct($class) {
		$this->_type = $class;
	}
	
	/**
	 * Human-readable representation of this enumeration set
	 */
	public function __toString() {
		return '[EnumSet Size: '.$this->count().'; Binary: 0x'.str_pad(strtoupper(dechex($this->getBinary())), PHP_INT_SIZE * 2, '0', STR_PAD_LEFT).'; Set: '.implode(', ', $this->_set).']';
	}
	
	/**
	 * Returns the amount of enums contained in this set
	 */
	public function count() {
		return count($this->_set);
	}
	
	/**
	 * Returns the enum at given offset (if any)
	 */
	public function getEnumAt($offset) {
		if(isset($this->_set[$offset])) {
			return $this->_set[$offset];
		}
		return null;
	}
	
	/**
	 * Returns the type of the enums contained in this set
	 */
	public function getType() {
		return $this->_type;
	}
	
	/**
	 * Generates a new iterator to iterate over the enums in this set
	 */
	public function getIterator() {
		return new EnumSetIterator($this);
	}
	
	/**
	 * Returns the binary mask of this entire set
	 */
	public function getBinary() {
		if($this->_binary === null) {
			$this->_binary = 0;
			foreach($this->_set as $enum) {
				$this->_binary |= $enum->getBinary();
			}
		}
		return $this->_binary;
	}
	
	/**
	 * Adds given enums - optionally nested using arrays - to this set, provided they are of this set's type 
	 */
	public function add() {
		$args = func_get_args();
		if($args) {
			foreach($args as $arg) {
				if(is_array($arg)) {
					call_user_func_array(array($this, __METHOD__), $arg);
				}else if($arg instanceof $this->_type) {
					$this->_add($arg);
				}else if($arg instanceof EnumSet && $arg->getType() === $this->_type) {
					foreach($arg as $enum) {
						$this->_add($enum);
					}
				}
			}
		}
		return $this;
	}
	
	/**
	 * Adds given enum to this set without type-checking
	 */
	protected function _add(Enum $enum) {
		$this->_set[] = $enum;
		$this->_binary = null;
	}
	
	/**
	 * Removes given enums - optionally nested using arrays - from this set, provided they exist
	 */
	public function remove() {
		$args = func_get_args();		
		if($args) {
			foreach($args as $arg) {
				if(is_array($arg)) {
					call_user_func_array(array($this, __METHOD__), $arg);
				}else if($arg instanceof $this->_type) {
					$this->_remove($arg);
				}else if($arg instanceof EnumSet && $arg->getType() === $this->_type) {
					foreach($arg as $enum) {
						$this->_remove($enum);
					}
				}
			}
		}
		return $this;
	}
	
	/**
	 * Removes given enum from this set
	 */
	protected function _remove(Enum $enum) {
		$key = array_search($enum, $this->_set, true);
		if($key !== false) {
			array_splice($this->_set, $key, 1);
			$this->_binary = null;
		}
	}
	
	/**
	 * Clears this set
	 */
	public function clear() {
		$this->_set = array();
		$this->_binary = null;
		return $this;
	}
	
	/**
	 * Retains ONLY the enums contained in given set, the rest is removed
	 */
	public function retain(EnumSet $set) {
		$remove = array();
		foreach($this->_set as $enum) {
			if(!$set->contains($enum)) {
				$remove[] = $enum;
			}
		}
		foreach($remove as $enum) {
			$this->_remove($enum);
		}
		return $this;
	}
	
	/**
	 * Whether this set contains given enums
	 */
	public function contains() {
		$args = func_get_args();
		if($args) {
			foreach($args as $arg) {
				if(!$arg instanceof Enum || !$this->_contains($arg)) {
					return false;
				}
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Whether this set contains given enum
	 */
	protected function _contains(Enum $enum) {
		return in_array($enum, $this->_set, true);
	}
	
	/**
	 * Whether this set is empty
	 */
	public function isEmpty() {
		return empty($this->_set);
	}
	
	/**
	 * Whether given class is a valid enumeration type
	 */
	public static function isValidType($class) {
		return (class_exists($class) && is_subclass_of($class, 'Enum'));
	}
	
	/**
	 * Creates a new set for given class
	 */
	public static function create($class, $method=__METHOD__) {
		if(!static::isValidType($class)) {
			throw new EnumException('Provided type "'.$class.'" to '.$method.'() is not a valid enum-type');
			return null;
		}
		return new static($class);
	}
	
	/**
	 * Creates a new set for given class containing all defined enums
	 */
	public static function all($class) {
		$set = static::create($class, __METHOD__);
		if($set) {
			$set->add($class::enums());
		}
		return $set;
	}
	
	/**
	 * Creates a new set of given enums (the initial enum determines the type of the set)
	 */
	public static function of(Enum $first) {
		$set = static::create(get_class($first), __METHOD__);
		if($set) {
			$args = func_get_args();
			$set->add($args);
		}
		return $set;
	}
	
	/**
	 * Creates a new set complementing the given set, thus, containing all enums _not_ present in the given one
	 */
	public static function complement(EnumSet $set) {
		$comp = clone $set;
		$comp->clear();
		$class = $set->getType();
		foreach($class::enums() as $enum) {
			if(!$set->_contains($enum)) {
				$comp->_add($enum);
			}
		}
		return $comp;
	}
	
	/**
	 * Creates a new set ranging from first to second given enum (regardless of order)
	 */
	public static function range(Enum $from, Enum $to) {
		$class = get_class($from);
		$set = static::create($class, __METHOD__);
		if($set) {
			$seek = null;
			foreach($class::enums() as $enum) {
				if($seek === null && ($enum === $from || $enum === $to)) {
					$seek = ($enum === $from) ? $to : $from;
				}
				if($seek !== null) {
					$set->_add($enum);
					if($seek === $enum) {
						break;
					}
				}
			}
		}
		return $set;
	}
	
	/**
	 * Creates a new set for given class containing all enums defined by the given binary mask
	 */
	public static function byBinary($class, $binary) {
		$set = static::create($class, __METHOD__);
		if($set) {
			$enums = $class::byBinary($binary, false);
			$set->add($enums);
		}
		return $set;
	}
	
}
