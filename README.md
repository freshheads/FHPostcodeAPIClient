FHPostcodeAPIClient
===================

[![Build Status](https://travis-ci.org/freshheads/FHPostcodeAPIClient.png?branch=master)](https://travis-ci.org/freshheads/FHPostcodeAPIClient)

FHPostcodeAPIClient is a PHP client library for the PostcodeAPI.nu web service. This library is developed 
by [Freshheads](http://www.freshheads.com) and will be maintained in sync with the web service itself.

**Links:**

* [More information](http://www.postcodeapi.nu)
* [API documentation](http://api.postcodeapi.nu/docs)

Requirements
------------

FHPostcodeAPIClient works with PHP 5.4.0 or up. This library is dependent on the awesome [Guzzle](http://guzzlephp.org/) HTTP client library. Guzzle 5 
version is used instead of the new Guzzle 6, as Guzzle 6 requires the php version to be higher than 5.5.0.

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
// initiate client
$apiKey = 'replace_with_your_own_api_key';
$client = new FHPostcodeAPIClient(new \GuzzleHttp\Client(), $apiKey);

// call endpoints
$response = $client->getAddresses('5041EB', 21);
$response = $client->getAddress('0855200000061001');
```

Roadmap
-------

A few things to be done in the future:

* Add model class responses

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/freshheads/fhpostcodeapiclient/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
