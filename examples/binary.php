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

error_reporting(-1);

require('../lib/bootstrap.php');

/**
 * This example shows how to work with binary flags generated by the enum library
 */

// When buying a new car, customers may order several additional features
class CarFeature extends Enum {

	const CONVERTIBLE       = 'Turn your car into a convertible!';
	const CENTRAL_LOCKING   = 'Automatically lock all other doors, with this central locking system';
	const SPOILERS          = 'Fan of the Fast and the Furious? Pimp your car with spoilers';
	const ALARM             = 'Protects your valuables as well as the car itself';
	const SEAT_HEATING      = 'Nice and comfy heated seats for those cold days';

}

// For every enum member a unique binary flag is generated
// This is what the CarFeature binary flags look like in hex as well as binary form
CarFeature::CONVERTIBLE()->getBinary();     // 0x00000001 - 00000000 00000000 00000000 00000001
CarFeature::CENTRAL_LOCKING()->getBinary(); // 0x00000002 - 00000000 00000000 00000000 00000010
CarFeature::SPOILERS()->getBinary();        // 0x00000004 - 00000000 00000000 00000000 00000100
CarFeature::ALARM()->getBinary();           // 0x00000008 - 00000000 00000000 00000000 00001000
CarFeature::SEAT_HEATING()->getBinary();    // 0x00000010 - 00000000 00000000 00000000 00010000

// As seen above, with 32-bit integers, this results in 32 unique flags. Collisions may occur depending on the amount of members and/or PHP's integer size (PHP_INT_SIZE)
// Looking up a single CarFeature with the byBinary() method as follows
CarFeature::byBinary(4); // Since SPOILERS is the only member with the third bit set (see above) this results in CarFeature::SPOILERS
CarFeature::byBinary(CarFeature::SPOILERS()->getBinary()); // idem

// When a customer orders several features, it's convenient to group these together; Use the bitwise OR-operator
$order = CarFeature::CONVERTIBLE()->getBinary() | CarFeature::SEAT_HEATING()->getBinary();
// Order now contains a combination of the CONVERTIBLE and SEAT_HEATING binary flags
echo $order; // Results in 17 (0x00000011 in hex or 00000000 00000000 00000000 00010001 in binary)

// To figure out which features the customer wanted, use the byBinary() method yet again
CarFeature::byBinary($order); // Will result in an array with both CarFeature::CONVERTIBLE and CarFeature::SEAT_HEATING

// Depending on the binary flag passed in, the byBinary() method has three possible return types: a single enum member, an array of members or null.
CarFeature::byBinary(0); // There is no binary flag matching 0, so this will result in null

// To force the method to always return an array (even empty), set the singular parameter to false
CarFeature::byBinary(0, false); // Will result in an empty array instead

// Ensure the list is always an array, loop through it and print each feature
foreach(CarFeature::byBinary($order, false) as $feature) {
	echo $feature;
}
