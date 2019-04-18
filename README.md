# PHP String Domain
String domains can be used to determinate areas (domains) using simple php strings.  
They are divided by dots (.)

~~~~php
$domain = "ch.tasoft.application";
~~~~
This means the domain application of tasoft of ch.  
The util provides several functions to work with domains.

This util becomes very powerful with the membership functions.  
It allows to check, if a domain matches to another domain.

~~~~php
use TASoft\StrDom\Domain;

// Children
Domain::matchesDomainQuery("ch.tasoft.application", "ch.tasoft.application");   // true
Domain::matchesDomainQuery("ch.tasoft.application", "ch.tasoft.*");             // true
Domain::matchesDomainQuery("ch.tasoft.application", "ch.*");                    // false
Domain::matchesDomainQuery("ch.tasoft.application", "ch.*.*");                  // true
Domain::matchesDomainQuery("ch.tasoft.application", "ch.*.app");                // false
Domain::matchesDomainQuery("ch.tasoft.application", "ch.*.app*");               // true

// Subdomains
Domain::matchesDomainQuery("ch.tasoft.application", "ch.");                     // true

// Empty query is always false.
Domain::matchesDomainQuery("ch.tasoft.application", "");                        // false
~~~~