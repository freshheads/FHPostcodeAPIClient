FHPostcodeAPIClient
===================

[![Build Status](https://travis-ci.org/freshheads/FHPostcodeAPIClient.png?branch=master)](https://travis-ci.org/freshheads/FHPostcodeAPIClient)

FHPostcodeAPIClient is a PHP client library for the PostcodeAPI.nu web service. This library is developed 
by [Freshheads](https://www.freshheads.com) and will be maintained in sync with the web service itself.

**Links:**

* [More information](https://www.postcodeapi.nu)
* [API documentation](https://swaggerhub.com/api/apiwise/postcode-api)

Requirements
------------

FHPostcodeAPIClient works with PHP 5.4.0 or up. This library is dependent on the awesome [Guzzle](http://guzzlephp.org/) HTTP client library. 

Installation
------------

FHPostcodeAPIClient can easily be installed using [Composer](https://getcomposer.org/):

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

// Note that this call is only available with a premium account
$response = $client->getPostcodes('51.566405', '5.077171');
```

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/freshheads/fhpostcodeapiclient/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
