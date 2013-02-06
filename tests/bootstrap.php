<?php

$loader = @include __DIR__.'/../vendor/autoload.php';

if (!$loader) {
    die(<<<'EOT'
You must set up the project dependencies, run the following command:
composer install

EOT
    );
}

\Guzzle\Tests\GuzzleTestCase::setMockBasePath(__DIR__ . DIRECTORY_SEPARATOR . 'mock');
\Guzzle\Tests\GuzzleTestCase::setServiceBuilder(\Guzzle\Service\Builder\ServiceBuilder::factory(array(
    'services' => array(
        'postcode_api' => array(
            'class'  => 'FH\PostcodeAPIClient\FHPostcodeAPIClient',
            'params' => array(
                'api_key' => 'my-personal-api-key'
            )
        )
    )
)));
