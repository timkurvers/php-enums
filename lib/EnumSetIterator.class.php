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
