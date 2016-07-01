FHPostcodeAPIClient
===================

[![Build Status](https://travis-ci.org/freshheads/FHPostcodeAPIClient.png?branch=master)](https://travis-ci.org/freshheads/FHPostcodeAPIClient)

FHPostcodeAPIClient is a PHP client library for the PostcodeAPI.nu web service. This library is developed 
by [Freshheads](http://www.freshheads.com) and will be maintained in sync with the web service itself.

**Links:**

* [More information](http://www.postcodeapi.nu)
* [API documentation](http://www.postcodeapi.nu/docs)

Requirements
------------

FHPostcodeAPIClient works with PHP 5.4.0 or up. This library is dependent on the awesome [Guzzle](http://guzzlephp.org/) HTTP client library. 

Installation
------------

FHPostcodeAPIClient can easily be installed using [Composer](http://getcomposer.org/):

```bash
composer require freshheads/postcode-api-client
```

Usage
-----

Instantiate the client and replace the API key with your personal credentials:

```php
// Use the composer autoloader to load dependencies
require_once 'vendor/autoload.php';

// initiate client
$apiKey = 'replace_with_your_own_api_key';
$client = new \FH\PostcodeAPI\Client(new \GuzzleHttp\Client(), $apiKey);

// call endpoints
$response = $client->getAddresses('5041EB', 21);
$response = $client->getAddress('0855200000061001');
```

Postcodeapi.nu Version 1
------------------------

Version 1 of PostcodeAPI will be available until 29-02-2016. You can still connect to this API via version 1.x of this client library.
Version 1.x can be installed via composer:

```bash
composer require freshheads/postcode-api-client:^1.0
```

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/freshheads/fhpostcodeapiclient/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
