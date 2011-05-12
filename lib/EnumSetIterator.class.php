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
 * Allows iteration over an EnumSet
 */
class EnumSetIterator implements Iterator {
	
	/**
	 * Holds a reference to the set being iterated
	 */
	protected $_set = null;
	
	/**
	 * Current iterator position in the set
	 */
	protected $_pos = 0;
	
	/**
	 * Constructs a new iterator for given set
	 */
	public function __construct(EnumSet $set) {
		$this->_set = $set;
	}
	
	/**
	 * Destructs this iterator
	 */
	public function __destruct() {
		$this->_set = null;
	}
	
	/**
	 * Rewinds the position to the beginning of the set
	 */
	public function rewind() {
		$this->_pos = 0;
	}
	
	/**
	 * Fast-forwards the position to the end of the set
	 */
	public function end() {
		$this->_pos = $this->_set->count() - 1;
	}
	
	/**
	 * Advances the position
	 */
	public function next() {
		$this->_pos++;
	}
	
	/**
	 * Retracts the position
	 */
	public function prev() {
		$this->_pos--;
	}
	
	/**
	 * Seeks to given position
	 */
	public function seek($pos) {
		$this->_pos = $pos;
	}
	
	/**
	 * Whether the set has an enum at the current position
	 */
	public function valid() {
		return ($this->_set && 0 <= $this->_pos && $this->_pos < $this->_set->count());
	}
	
	/**
	 * Returns the current position
	 */
	public function key() {
		return $this->_pos;
	}
	
	/**
	 * Returns the enum at the current position (if any)
	 */
	public function current() {
		return $this->_set->getEnumAt($this->_pos);
	}
	
}
