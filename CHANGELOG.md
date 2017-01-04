# CHANGELOG

## 3.0.0

* Dropped direct usage of Guzzle and now make use of [HTTPPlug](http://httplug.io/), see README.
* Added `getPostcodes()` call, to get postcodes based on provided `latitude, longitude`. Note that this call is only available with a premium account.

## 2.0.0

* Rewrite to support postcodeapi.nu version 2

## 1.0.2 (2015-06-11)

*	Added call for type = P4 support

## 1.0.1 (2013-08-06)

* Fixed compatibility issue with Guzzle createRequest() method
* Fixed Composer autoloading issue
