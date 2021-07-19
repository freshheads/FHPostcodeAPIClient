<?php

namespace FH\PostcodeAPI\Test;

use FH\PostcodeAPI\Client;
use FH\PostcodeAPI\Exception\InvalidApiKeyException;
use FH\PostcodeAPI\Exception\ServerErrorException;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Psr7\Response;
use Http\Mock\Client as MockClient;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @author Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 */
final class ClientTest extends TestCase
{
    private const POSTCODE_PATTERN = '/^[\d]{4}[\w]{2}$/i';
    private const FRESHHEADS_POSTCODE = '5041EB';
    private const FRESHHEADS_NUMBER = 21;
    private const FRESHHEADS_CITY = 'Tilburg';
    private const FRESHHEADS_LONGITUDE = 5.07717893166;
    private const FRESHHEADS_LATITUDE = 51.566414786;
    private const FRESHHEADS_ADDRESS_ID = '0855200000061001';

    public function testRequestExceptionIsThrownWhenUsingAnInvalidApiKey(): void
    {
        $this->expectException(InvalidApiKeyException::class);

        $client = $this->createClient(
            $this->loadMockResponse('failed_list_with_invalid_api_key')
        );

        $client->getAddresses();
    }

    public function testListResourceReturnsAllAddressesWhenNoParamsAreSupplied(): void
    {
        $client = $this->createClient(
            $this->loadMockResponse('successful_list_without_filtering')
        );

        $response = $client->getAddresses();

        $this->applyAssertsToMakeSureAddressesArrayIsAvailableInResponse($response);

        $addresses = $response->_embedded->addresses;

        Assert::assertGreaterThan(0, count($addresses), 'Expecting that there are always addresses available');

        $this->applyAddressFieldAreSetAndOfTheCorrectTypeAssertions($addresses[0]);
    }

    public function testListResourceReturnsExpectedAddressWhenPostcodeAndNumberAreSupplied(): void
    {
        $client = $this->createClient(
            $this->loadMockResponse('successful_list_freshheads_postcode_and_number')
        );

        $response = $client->getAddresses(self::FRESHHEADS_POSTCODE, self::FRESHHEADS_NUMBER);

        $this->applyAssertsToMakeSureAddressesArrayIsAvailableInResponse($response);

        $addresses = $response->_embedded->addresses;

        Assert::assertGreaterThan(0, count($addresses), 'Expecting that there are always addresses available when no filters are applied');

        $firstAddress = $addresses[0];

        $this->applyAddressFieldAreSetAndOfTheCorrectTypeAssertions($firstAddress);
        $this->applyIsFreshheadsAddressAssertions($firstAddress);
    }

    public function testExpectedAddressInformationIsReturnedFromDetailResource(): void
    {
        $client = $this->createClient(
            $this->loadMockResponse('successful_detail_freshheads')
        );

        $address = $client->getAddress(self::FRESHHEADS_ADDRESS_ID);

        $this->applyAddressFieldAreSetAndOfTheCorrectTypeAssertions($address);
        $this->applyIsFreshheadsAddressAssertions($address);
    }

    public function testClientThrowsExceptionWhenInvalidInputIsSupplied(): void
    {
        $this->expectException(ServerErrorException::class);

        $client = $this->createClient(
            $this->loadMockResponse('failed_list_with_invalid_postalcode_and_number')
        );

        $client->getAddresses('invalid_postcode', 'invalid_number');
    }

    private function loadMockResponse(string $name): Response
    {
        return Message::parseResponse(file_get_contents(__DIR__ . "/../../Mock/{$name}"));
    }

    private function applyIsFreshheadsAddressAssertions(stdClass $address): void
    {
        Assert::assertSame(strtoupper($address->postcode), self::FRESHHEADS_POSTCODE, 'Incoming postcode did not match the expected postcode');
        Assert::assertSame((string)$address->number, (string)self::FRESHHEADS_NUMBER, 'Incoming number did not match the expected number');
        Assert::assertSame($address->city->label, self::FRESHHEADS_CITY, 'Incoming city did not match the expected city');

        // use number_format number rounding to allow for minor changes between expected and actual value
        $wgs84 = $address->geo->center->wgs84;

        Assert::assertSame(
            number_format($wgs84->coordinates[0], 5),
            number_format(self::FRESHHEADS_LONGITUDE, 5),
            'Incoming longitude did not match the expected value'
        );

        Assert::assertSame(
            number_format($wgs84->coordinates[1], 5),
            number_format(self::FRESHHEADS_LATITUDE, 5),
            'Incoming latitude did not match the expected value'
        );
    }

    private function applyAssertsToMakeSureAddressesArrayIsAvailableInResponse(stdClass $response): void
    {
        Assert::assertTrue(isset($response->_embedded->addresses));
        Assert::assertIsArray($response->_embedded->addresses);
    }

    private function applyAddressFieldAreSetAndOfTheCorrectTypeAssertions(stdClass $address): void
    {
        // only test the availability of the most import fields and their values

        Assert::assertTrue(isset($address->street), 'Incoming address did not have a street field');
        Assert::assertIsString($address->street, 'Incoming address did not have a street value of type string');

        Assert::assertTrue(isset($address->city->label), 'Incoming address did not have a city.label field');
        Assert::assertIsString($address->city->label, 'Incoming address did not have a city.label value of type string');

        Assert::assertTrue(isset($address->postcode), 'Incoming address did not have a postcode field');
        Assert::assertTrue(preg_match(self::POSTCODE_PATTERN, $address->postcode) === 1, 'Incoming address did not have a postcode value that matches the pattern: ' . self::POSTCODE_PATTERN);

        Assert::assertTrue(isset($address->number), 'Incoming address did not have a number field');
        Assert::assertTrue(is_string($address->number) || is_numeric($address->number), 'Incoming address did not have a number field with type string');

        $wgs84 = $address->geo->center->wgs84;

        Assert::assertTrue(isset($wgs84->coordinates[0]), 'Incoming address did not have a longitude field');
        Assert::assertIsFloat($wgs84->coordinates[0], 'Incoming address did not have a longitude value of type float');

        Assert::assertTrue(isset($wgs84->coordinates[1]), 'Incoming address did not have a latitude field');
        Assert::assertIsFloat($wgs84->coordinates[1], 'Incoming address did not have a latitude value of type float');
    }

    private function createClient(Response $mockedResponse): Client
    {
        $httpClient = new MockClient();
        $httpClient->addResponse($mockedResponse);

        return new Client($httpClient);
    }
}
