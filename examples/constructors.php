<?php
/**
 * PHP Enums Library
 * Copyright (c) 2011 Tim Kurvers <http://www.moonsphere.net>
 *
 * This library provides enum functionality similar to the implementation
 * found in Java. It differs from existing libraries by offering one-shot
 * enum constructors through static initialization, enum iteration as well
 * as equality support and value, ordinal and binary lookups.

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

error_reporting(-1);

require('../lib/bootstrap.php');

/**
 * This example shows how to initialize your enums using the static initializer and construct them with your very own enum constructor
 */

// Creating an enumeration for a selection of planets in our solar system with their position as the value
class Planet extends Enum {

	const MERCURY   = 1;
	const VENUS     = 2;
	const EARTH     = 3;
	const MARS      = 4;
	const PLUTO     = 9;

	// However, there is no data currently attached to these enums. To do so, we may use the static initializer __()
	// This static initializer is called only once per enum-type (so only once for Planet) and NEEDS to be defined as both static and public
	public static function __() {

		// Within this static initializer, we can attach data by doing a 'constructor' call (without the new keyword)
		// Let's attach the inhabitant's name and a related object/service that is identical to the planet's name
		Planet::MERCURY('Mercurian', 'Chemical element');
		Planet::VENUS('Venusian', 'Roman goddess of love');
		Planet::EARTH('Terrestrial', 'Soil we walk on');

		// Note how static and/or self can also be used as they point to Planet. This ensures a robust enum definition withstanding any future name changes
		static::MARS('Martian', 'Well-known candy bar');
		self::PLUTO('Plutonian', 'Fictional Disney character');

	}

	// Define the properties that our Planet enum will hold
	public $inhabitant = null;
	public $related = null;

	// Next step is to actually do something useful with the calls in the static initializer. To do so, we may use the enum constructor ___construct()
	// The enum constructor has an EXTRA underscore to avoid collision with the PHP constructor. The calls made above need to match this constructor's definition
	protected function ___construct($inhabitant, $related) {
		$this->inhabitant = $inhabitant;
		$this->related = $related;
	}

}

// Attempting to attach data/behaviour outside of the Planet enum will throw an exception
// Planet::MARS('Malicious Inhabitant', 'Roman god of war');

// Now that VENUS has data attached we can use her public properties
$venus = Planet::VENUS();
echo 'Venus is also a '.$venus->related; // Venus is also a Roman goddess of love

// Creating enums on the fly without using constants is a useful feature to avoid repetition
// However, since there is no direct value - as there is no constant - you are responsible for assigning one if you plan on using byValue()
class Country extends Enum {

	public static function __() {

		self::NETHERLANDS('NLD');
		self::NORWAY('NOR');
		self::IRELAND('IRL');

	}

	// Let's assign the NATO country code as the value ($___value is inherited by the base-class Enum)
	protected function ___construct($code) {
		$this->___value = $code;
	}

	// Enums are not limited to data, they can provide behaviour, too
	public function isIsland() {
		switch($this) {
			case self::NETHERLANDS():
			case self::NORWAY():
				return false;
			break;
			case self::IRELAND():
				return true;
			break;
		}
	}

}

// Verifying the value assignment for these countries
echo Country::byValue('NOR'); // Country::NORWAY enum
echo Country::byValue('IRL')->isIsland(); // true

// Using the Country enums above, we can now define a bunch of airliners with their originating countries. Yes! Using enums in an enum.
class Airliner extends Enum {

	public static function __() {

		self::RYANAIR('Ryanair', 'RYR', Country::IRELAND());
		self::KLM('KLM Royal Dutch Airlines', 'KLM', Country::NETHERLANDS());
		self::NORWEGIAN('Norwegian Air Shuttle', 'NAX', Country::NORWAY());

	}

	public $name = null;
	public $code = null;
	public $country = null;

	// As seen in the definitions above, we are requesting the name of the airliner, its ICAO code and its originating country
	protected function ___construct($name, $code, Country $country) {
		$this->name = $name;
		$this->code = $code;
		$this->country = $country;
	}

}

echo Airliner::RYANAIR()->country->isIsland(); // true
