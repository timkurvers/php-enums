PHP Enums Library
=================

Copyright (c) 2011 Tim Kurvers <http://www.moonsphere.net>

This library provides enum functionality similar to the implementation  
found in Java. It differs from existing libraries by offering one-shot  
enum constructors through static initialization, enum iteration as well  
as equality support and value, ordinal and binary lookups.  

**Required PHP version: 5.3**


Introduction
------------

If you want to get started right away, zoom right to 'Getting Started'.  

Enumerations can be used to define a fixed set of constants. As [this](http://download.oracle.com/javase/1,5.0/docs/guide/language/enums.html)  
Java-article points out, in most languages people will resort to using  
integer constants losing useful features in the process.

A real-world PHP example: in your web application you might be using  
a set of opcodes to communicate with upstream services:
	
	define('OPCODE_VOID', 0);
    define('OPCODE_LOGIN', 1);
    define('OPCODE_QUERY', 2);
    define('OPCODE_LOGOUT', 3);

We may call this an enumeration as it is a set of constants related to  
each other indicated by their prefix. Their usage may look like this:  

    function send($opcode, $data=null) { } 

One would pass in a constant (currently ranging from 0 to 4) and some  
additional data, and the send() method will do its job.

However, there are a set of disadvantages to this approach:

* No Type-Safety - One could pass in '4' which is not a valid opcode
* No Namespace - Collisions may occur when using global constants
* Uninformative - Debugging $opcode will result in a 'useless' number
* No Behaviour - No behaviour can be attached to the constant
* No Data - No data can be attached to the constant

By using enumerations one can overcome all these disadvantages and  
in the process benefit from added functionality:

    class Opcode extends Enum {
        
        const VOID = 0;
        const LOGIN = 1;
        const QUERY = 2;
        const LOGOUT = 3;
        
    }
    
And its basic usage:

    function send(Opcode $opcode, $data=null) { }
    
When '4' is passed in, PHP will throw a type-mismatch exception. The  
enumeration library magically creates Opcode instances for each specified  
constant. As for how this works and how to get the most out of the library  
start with the **API example** in */examples/api.php*.


Getting Started
---------------

The examples found in the library attempt to visualize a real-world  
situation. Additionally, they are independent and can each be used as  
a starting point for your own project.

* **API**: Introduction to the PHP Enums Library
* **Binary**: Combining enums using masks & flags
* **Constructors**: Enhancing enums by attaching data and behaviour 
* **Iteration**: Working with a list of defined enums
* **Sets**: Creating and using unique collections of enums


Library Directory Structure
---------------------------

> /examples/  
> /lib/  
> /LICENSE  
> /README  
