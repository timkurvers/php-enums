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
 * This example shows basic usage of the PHP Enums Library API
 */

// To use Topping as an enumeration with members, simply extend Enum (found in /lib/Enum.class.php)
class Topping extends Enum {

	const CHOCOLATE     = 'chocolate';
	const STRAWBERRY    = 'strawberry';
	const BLACKBERRY    = 'blackberry';

}

// Defining class constants (such as CHOCOLATE, STRAWBERRY and BLACKBERRY) automatically constructs enums for these constants with associated values
// To use these enums call them directly
Topping::CHOCOLATE(); // Topping::CHOCOLATE instance

// Or use the static get() method
Topping::get('CHOCOLATE'); // exact same Topping::CHOCOLATE instance

// Calling a non-defined enum will result in an exception
// Topping::MISSING(); // EnumException thrown

// However, using the get() method gracefully continues
Topping::get('MISSING'); // null

// Every enum has a value, ordinal offset and binary flag
// Values may overlap, ordinal offset will always be unique
$strawberry = Topping::STRAWBERRY();
$strawberry->getValue();   // 'strawberry'
$strawberry->getOrdinal(); // 1 (2nd constant in the zero-based list)
$strawberry->getBinary();  // 2 (generating a unique flag for this enum, the 2nd bit is set, which results in 2)

// Searching for a specific enum using these properties; All these calls fetch the Topping::BLACKBERRY enum
Topping::byValue('blackberry'); // Topping::BLACKBERRY instance
Topping::byOrdinal(2);   // exact same instance
Topping::byBinary(0x04); // idem

// Since Topping is an enum-type - and therefore a class, we can use it in type-hints
function order(Topping $topping) {
	if($topping == Topping::BLACKBERRY()) {
		return 'Unfortunately, we are all out of '.$topping->getValue().' topping!';
	}
	return 'Here you go, an icecream with '.$topping->getValue().' topping!';
}

// Attempt to order an icecream with different toppings
order(Topping::STRAWBERRY());
order(Topping::BLACKBERRY());
// order('test'); // Error, 'test' is no Topping instance

// Comparing enum instances by using the comparison operators
Topping::STRAWBERRY() == Topping::STRAWBERRY(); // true
Topping::STRAWBERRY() == Topping::BLACKBERRY(); // false

// Additionally, each enum has an equals() method to compare with various values
$chocolate = Topping::CHOCOLATE();

// When comparing with strings, it assumes those strings to be member names (such as CHOCOLATE and STRAWBERRY)
$chocolate->equals('CHOCOLATE');  // true
$chocolate->equals('STRAWBERRY'); // false

// When comparing with any value (including strings), it assumes those to be the registered value for the enum (such as 'chocolate' and 'blackberry' in this case)
$chocolate->equals('chocolate');  // true
$chocolate->equals('strawberry'); // false
