# PHP String Domain
String domains can be used to determinate areas (domains) using simple php strings.  
They are divided by dots (.)

~~~~php
$domain = "ch.tasoft.application";
~~~~
This means the domain application of tasoft of ch.  
The util provides several functions to work with domains.

This util becomes very powerful with the membership functions.  
It allows to check, if a domain belongs to another domain.

~~~~php
use TASoft\StrDom\Domain;

var_dump( Domain::isMemberOfDomain() )
~~~~