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
 * This example shows how to use the EnumSet as a collection of enums
 */

// Let's define a Day enumeration with all days of the week
class Day extends Enum {

	const MONDAY    = 'monday';
	const TUESDAY   = 'tuesday';
	const WEDNESDAY = 'wednesday';
	const THURSDAY  = 'thursday';
	const FRIDAY    = 'friday';
	const SATURDAY  = 'saturday';
	const SUNDAY    = 'sunday';

}

// To create an empty set use the create() method with the name of the enum-type you want to create the set for
// Let's create a jogging schedule, indicating on which days one will be out for a jog in the park
$schedule = EnumSet::create('Day');

// EnumSet provides a set of instance methods to add and remove enums
// Adds given day(s) to collection
$schedule->add(Day::MONDAY(), Day::FRIDAY(), Day::SATURDAY());

// Removes given day(s) from collection
$schedule->remove(Day::FRIDAY()); // Schedule now contains MONDAY and SATURDAY

// Unfortunately, the park is only open on monday and sunday
$park = EnumSet::create('Day')->add(Day::MONDAY())->add(Day::SUNDAY());

// Using the retain() method we can force our schedule to conform to the park's opening days
// Since SATURDAY - contained in schedule - is not a valid enum in the park, it is removed
$schedule->retain($park); // Schedule now only contains MONDAY

// Querying the collection for information
count($schedule); // 1 (only MONDAY is left)
$schedule->isEmpty(); // false
$schedule->contains(Day::MONDAY()); // true

// Clearing the collection of any enums
$schedule->clear();

// Instead of starting out with an empty collection, EnumSet comes with a couple of convenient initialization methods
// The of() static method will create a new collection and initialize it with the given enums
$weekend = EnumSet::of(Day::SATURDAY(), Day::SUNDAY());

// Since we already have the weekend definition above, we can complement it, ending up with a collection of week days
// This effectively generates a collection with all enums NOT present in the given set
$weekdays = EnumSet::complement($weekend);

// An alternative way of generating the collection of week days is through a range using the range() static method
$weekdays = EnumSet::range(Day::MONDAY(), Day::FRIDAY());
$weekdays = EnumSet::range(Day::FRIDAY(), Day::MONDAY()); // The order is insignificant

// Lastly, it's possibly to fetch all enums by using the all() static method
$all = EnumSet::all('Day');

// EnumSet too, supports binary flag/mask support
$weekdays->getBinary(); // Results in 31 (0x0000001F in hex or 00000000 00000000 00000000 00011111 in binary)

// And alternatively, constructing an EnumSet using a binary mask
$weekend = EnumSet::byBinary('Day', 96);

// Iterating over a set
foreach($weekdays as $enum) {
	echo $enum;
}
