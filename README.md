FHPostcodeAPIClient
===================

[![Build Status](https://travis-ci.org/freshheads/FHPostcodeAPIClient.png?branch=master)](https://travis-ci.org/freshheads/FHPostcodeAPIClient)

FHPostcodeAPIClient is the official PHP client library for the PostcodeAPI.nu web service.
This library is developed by [Freshheads](http://www.freshheads.com) and will be maintained in sync with the web service itself.

* More information:  http://www.postcodeapi.nu
* API documentation: http://api.postcodeapi.nu/docs

Requirements
------------

FHPostcodeAPIClient works with PHP 5.3.2 or later.
This library is dependent on the awesome [Guzzle](http://guzzlephp.org/) HTTP client library.

Installation
------------

FHPostcodeAPIClient can easily be installed using [Composer](http://getcomposer.org/). Add the following requirement and repository to composer.json:

```yaml
// composer.json
{
    // ...
    "require": {
        // ...
        "freshheads/postcode-api-client": "1.0.*"
    },
    // ...
}
```

Run Composer to install all require dependencies:

```bash
composer update
```

Usage
-----

Instantiate the client and replace the API key with your personal credentials:

```php
use Guzzle\Service\Builder\ServiceBuilder;

$builder = ServiceBuilder::factory(array(
    'services' => array(
        'postcode_api' => array(
            'class'  => 'FH\PostcodeAPIClient\FHPostcodeAPIClient',
            'params' => array(
                'api_key' => 'your-personal-api-key'
            )
        )
    )
));

$client = $builder->get('postcode_api');
```

Check the [Guzzle documentation](http://guzzlephp.org/docs.html) for more details on the ServiceBuilder class.

Now you can execute the find_postal_code command on the client:

```php
// Query the web service for postal code only
$command = $client->getCommand('find_postal_code', array('postal_code' => '5041EB'));
$result = $command->execute();

// Results in:
//
//   array(5) {
//     'street'    => string(14) "Wilhelminapark"
//     'postcode'  => string(6) "5041EB"
//     'town'      => string(7) "Tilburg"
//     'latitude'  => double(51.5663166667)
//     'longitude' => double(5.0771925)
//   }

// To get even more accurate results, query the web service for postal code and house number
$command = $client->getCommand('find_postal_code', array('postal_code' => '5041EB', 'house_number' => '19'));
$result = $command->execute();

// Results in:
//
//   array(6) {
//     'street'       => string(14) "Wilhelminapark"
//     'house_number' => string(2) "19"
//     'postcode'     => string(6) "5041EB"
//     'town'         => string(7) "Tilburg"
//     'latitude'     => double(51.5665)
//     'longitude'    => double(5.07708)
//   }
```

Roadmap
-------

A few things to be done in the future:

* Add model class responses.
* Simplify usage without the need for the service builder.
* Add more code examples.
* Improve installation docs with non-Composer ways.
