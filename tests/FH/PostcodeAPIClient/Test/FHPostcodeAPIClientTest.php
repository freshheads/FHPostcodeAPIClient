<?php

namespace FH\PostcodeAPIClient\Test;

use Guzzle\Tests\GuzzleTestCase;

class FHPostcodeAPIClientTest extends GuzzleTestCase
{
    /**
     * @var \FH\PostcodeAPIClient\FHPostcodeAPIClient
     */
    protected $client;

    public function setUp()
    {
        $this->client = $this->getServiceBuilder()->get('postcode_api');
    }

    public function testCreateClient()
    {
        $this->assertInstanceOf('FH\PostcodeAPIClient\FHPostcodeAPIClient', $this->client);
    }

    public function testFindPostalCode()
    {
        $postalCode = '5041EB';

        $this->setMockResponse($this->client, 'FindPostalCodeResponse');

        $command = $this->client->getCommand('find_postal_code', array('postal_code' => $postalCode));
        $result = $command->execute();

        $this->assertEquals($result['postcode'], $postalCode);
        $this->assertFalse(array_key_exists('house_number', $result));
    }

    public function testFindPostalCodeWithHouseNumber()
    {
        $postalCode = '5041EB';
        $houseNumber = 21;

        $this->setMockResponse($this->client, 'FindPostalCodeHouseNumberResponse');

        $command = $this->client->getCommand('find_postal_code', array('postal_code' => $postalCode, 'house_number' => $houseNumber));
        $result = $command->execute();

        $this->assertEquals($result['postcode'], $postalCode);
        $this->assertEquals($result['house_number'], $houseNumber);
    }

    public function testFindPostalCodeP4()
    {
        $this->setMockResponse($this->client, 'FindPostalCodeP4Response');

        $command = $this->client->getCommand('find_postal_code_p4', array('postal_code' => 5041));
        $result = $command->execute();

        $this->assertTrue(isset($result['town']));
    }

    /**
     * @expectedException \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function testFindPostalCodeNotFound()
    {
        $this->setMockResponse($this->client, 'FindPostalCodeNotFoundResponse');

        $command = $this->client->getCommand('find_postal_code', array('postal_code' => '1234AB'));
        $command->execute();
    }
}
