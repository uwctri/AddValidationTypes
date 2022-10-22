# AddValidationTypes - Redcap External Module

## What does it do?

This module allows you to add and remove custom validation types via the Control Center's "Field Validation Types" page. The new validation types act just like all other native validation types; that means they are hard stops, work on data loads, and can be disable (but still usable via a data dictionary upload).

## Installing

You can install the module from the REDCap EM repo or drop it directly in your modules folder (i.e. `redcap/modules/add_validation_types_v1.0.0`).

## Setting up a new validation type

Validation types require a display name, a unique internal name, and two regular expressions that can be used to perform validation on fields. Sites like [Regexr](https://regexr.com/) can be used to design and easily test both JS and PCRE (PHP) regex. As of 2018 their are not many differences between JS and PCRE regex, but their are many [sites](https://gist.github.com/CMCDragonkai/6c933f4a7d713ef712145c5eb94a1816) that explain, in painful detail, any discrepancies. Unless you are using exotic features, you will likely be able to simply test and verify your one pattern works as expected for both PHP and JS.

## Considered

* Allow users to add regex via an action tag. If this is added it would mean that the project would need to be enabled on individual projects.
* Additional Regex Repo ideas
  * Integer Range List
  * Various barcode standards
  * File path
