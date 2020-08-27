FHPostcodeAPIClient
===================

[![Build Status](https://travis-ci.org/freshheads/FHPostcodeAPIClient.png?branch=master)](https://travis-ci.org/freshheads/FHPostcodeAPIClient)

FHPostcodeAPIClient is a PHP client library for the PostcodeAPI.nu web service. This library is developed
by [Freshheads](https://www.freshheads.com) and will be maintained in sync with the web service itself.

**Links:**

* [More information](https://www.postcodeapi.nu)
* [API documentation](https://swaggerhub.com/api/apiwise/postcode-api)

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
// In this example we made use of the Guzzle as HTTPClient.
$client = new \FH\PostcodeAPI\Client(
    new GuzzleHttp\Client([
        'headers' => [
            'X-Api-Key' => $apiKey
        ]
    ])
);

// call endpoints
$response = $client->getAddresses('5041EB', 21);
$response = $client->getAddress('0855200000061001');
$response = $client->getPostcode('5041EB');

// Note that this call is only available with a premium account
$response = $client->getPostcodes('51.566405', '5.077171');
```

Within a Symfony project
----------------------

We recommend to use [Guzzle](https://github.com/guzzle/guzzle), to be able to use Guzzle in combination with the PostcodeApiClient.
Following definition is used with an implementation of `Guzzle 7`.

```yaml
_defaults:
        autowire: true
        autoconfigure: true

    project.http.client.postal_code:
        class: GuzzleHttp\Client
        bind:
            $config: { headers: { X-Api-Key: '%postcode_api_nu.key%' } }

    FH\PostcodeAPI\Client:
        $httpClient: '@project.http.client.postal_code'
```
 
You should now be able use the `FH\PostcodeAPI\Client` service to make requests to the PostcodeAPI.

#### Guzzle 6 
To make use of `Guzzle 6`, you should also make use of the
[Guzzle6Adapter](https://github.com/php-http/guzzle6-adapter). By running the following command you automatically install Guzzle as well.

```bash
composer require php-http/guzzle6-adapter
```

And add the following service definitions (usable in Symfony ^3.4):
```yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    project.http.guzzle.client:
        class: GuzzleHttp\Client
        arguments:
            - { headers: { X-Api-Key: 'replace_with_your_own_api_key_or_variable' } }
    
    project.http.adapter.guzzle.client:
        class: Http\Adapter\Guzzle6\Client
        arguments:
            $client: '@project.http.guzzle.client'
    
    FH\PostcodeAPI\Client:
        $httpClient: '@project.http.adapter.guzzle.client'
```
