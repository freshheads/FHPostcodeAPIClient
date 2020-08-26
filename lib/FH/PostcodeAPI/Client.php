<?php

namespace FH\PostcodeAPI;

use FH\PostcodeAPI\Exception\CouldNotParseResponseException;
use FH\PostcodeAPI\Exception\InvalidApiKeyException;
use FH\PostcodeAPI\Exception\ServerErrorException;
use GuzzleHttp\Psr7\Request;
use Http\Client\Exception\RequestException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function sprintf;

/**
 * Client library for postcodeapi.nu 2.0 web service.
 *
 * @author Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 * @author Evert Harmeling <evert@freshheads.com>
 */
class Client
{
    public const POSTCODES_SORT_DISTANCE = 'distance';

    /**
     * @var string|null
     */
    private $url = 'https://api.postcodeapi.nu';

    /**
     * @var string
     */
    private $version = 'v2';

    /**
     * @var ClientInterface
     */
    private $httpClient;


    public function __construct(ClientInterface $httpClient, string $url = null)
    {
        if (null !== $url) {
            $this->url = $url;
        }

        $this->httpClient = $httpClient;
    }

    /**
     * @param string|null $postcode
     * @param string|null $number
     * @param int $from
     *
     * @return \stdClass
     */
    public function getAddresses($postcode = null, $number = null, $from = 0)
    {
        return $this->get('/addresses/', [
            'postcode' => $postcode,
            'number' => $number,
            'from' => $from
        ]);
    }

    /**
     * @param string $id
     *
     * @return \stdClass
     */
    public function getAddress($id)
    {
        return $this->get(sprintf('/addresses/%s', $id));
    }

    /**
     * @param string $postcode
     *
     * @return \stdClass
     */
    public function getPostcodeDataByPostcode($postcode)
    {
        return $this->get('/postcodes/' . $postcode);
    }

    /**
     * @param string $latitude
     * @param string $longitude
     * @param string $sort
     *
     * @return \stdClass
     */
    public function getPostcodesByCoordinates($latitude, $longitude, $sort = self::POSTCODES_SORT_DISTANCE)
    {
        return $this->get('/postcodes/', [
            'coords' => [
                'latitude' => $latitude,
                'longitude' => $longitude
            ],
            'sort' => $sort
        ]);
    }

    /**
     * @return \stdClass
     *
     * @throws RequestException
     */
    private function get(string $path, array $params = [])
    {
        $request = $this->createHttpGetRequest($this->buildUrl($path), $params);

        $response = $this->httpClient->sendRequest($request);

        return $this->parseResponse($response, $request);
    }

    private function buildUrl(?string $path): string
    {
        return sprintf('%s/%s%s', $this->url, $this->version, $path);
    }

    private function createHttpGetRequest(string $url, array $params = []): Request
    {
        $url .= (count($params) > 0 ? '?' . http_build_query($params, null, '&', PHP_QUERY_RFC3986) : '');

        return new Request('GET', $url);
    }

    /**
     * @return \stdClass
     *
     * @throws CouldNotParseResponseException
     * @throws ServerErrorException
     * @throws InvalidApiKeyException
     */
    private function parseResponse(ResponseInterface $response, RequestInterface $request)
    {
        $result = json_decode((string) $response->getBody()->getContents(), false);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CouldNotParseResponseException('Could not parse response', $response);
        }

        if (property_exists($result, 'error')) {
            switch ($result->error) {
                case 'API key is invalid.':
                    throw new InvalidApiKeyException('API key is invalid');
                case 'An unknown server error occured.':
                    throw ServerErrorException::fromRequest($request);
            }
        }

        return $result;
    }
}
