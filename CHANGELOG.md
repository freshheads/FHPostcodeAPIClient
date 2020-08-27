# CHANGELOG

## 4.0.1

### Added

* `psr/http-client` to support direct use of `Guzzle 7` by using the `ClientInterface`. 
Change is BC because `Http\Client\HttpClient` implements the same interface. 

## 4.0.0

### Added

* `getPostcodeDataByPostcode` function for `/postcodes/{postalCode}` endpoint.
* Bumped PHPUnit version

### Removed

* Support for PHP <7.1

## 3.0.0

### Added

* Added `getPostcodes()` call, to get postcodes based on provided `latitude, longitude`. Note that this call is only available with a premium account.

### Removed

* Support direct usage of Guzzle, now handled via [HTTPPlug](http://httplug.io/), see README.

## 2.0.0

* Rewrite to support postcodeapi.nu version 2

## 1.0.2 (2015-06-11)

* Added call for type = P4 support

## 1.0.1 (2013-08-06)

* Fixed compatibility issue with Guzzle createRequest() method
* Fixed Composer autoloading issue
