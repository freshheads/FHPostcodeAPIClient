<?php

namespace FH\PostcodeAPI;

use FH\PostcodeAPI\Exception\CouldNotParseResponseException;
use FH\PostcodeAPI\Exception\InvalidApiKeyException;
use FH\PostcodeAPI\Exception\InvalidUrlException;
use FH\PostcodeAPI\Exception\ServerErrorException;
use GuzzleHttp\Psr7\Request;
use Http\Client\Exception;
use Http\Client\HttpClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use stdClass;

/**
 * Client library for postcodeapi.nu 2.0 web service.
 *
 * @author Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 * @author Evert Harmeling <evert@freshheads.com>
 */
class Client
{
    const POSTCODES_SORT_DISTANCE = 'distance';

    /**
     * @var null|string
     */
    private $url = 'https://api.postcodeapi.nu';

    /**
     * @var string
     */
    private $version = 'v2';

    /**
     * @var HttpClient
     */
    private $httpClient;


    public function __construct(HttpClient $httpClient, $url = null)
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
     * @return stdClass
     * @throws CouldNotParseResponseException
     * @throws Exception
     * @throws InvalidApiKeyException
     * @throws ServerErrorException
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
     * @return stdClass
     * @throws CouldNotParseResponseException
     * @throws Exception
     * @throws InvalidApiKeyException
     * @throws ServerErrorException
     */
    public function getAddress($id)
    {
        return $this->get(sprintf('/addresses/%s', $id));
    }

    /**
     * @param string $postcode
     *
     * @return stdClass
     * @throws CouldNotParseResponseException
     * @throws Exception
     * @throws InvalidApiKeyException
     * @throws ServerErrorException
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
     * @return stdClass
     * @throws CouldNotParseResponseException
     * @throws Exception
     * @throws InvalidApiKeyException
     * @throws ServerErrorException
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
     * Sends request to API using url and returns parsed response.
     * Useful for pagination link.
     *
     * @param $url
     * @return stdClass
     * @throws CouldNotParseResponseException
     * @throws Exception
     * @throws InvalidApiKeyException
     * @throws ServerErrorException
     * @throws InvalidUrlException
     */
    public function request($url)
    {
        $this->validateUrl($url);
        $request = $this->createHttpGetRequest($url);
        $response = $this->httpClient->sendRequest($request);

        return $this->parseResponse($response, $request);
    }

    /**
     * @param string $path
     * @param array $params
     *
     * @return stdClass
     *
     * @throws CouldNotParseResponseException
     * @throws InvalidApiKeyException
     * @throws ServerErrorException
     * @throws Exception
     */
    private function get($path, array $params = [])
    {
        $request = $this->createHttpGetRequest($this->buildUrl($path), $params);
        $response = $this->httpClient->sendRequest($request);

        return $this->parseResponse($response, $request);
    }

    /**
     * @param string $path
     * @return string
     */
    private function buildUrl($path)
    {
        return sprintf('%s/%s%s', $this->url, $this->version, $path);
    }

    /**
     * @param string $url
     * @param array $params
     * @return Request
     */
    private function createHttpGetRequest($url, array $params = [])
    {
        $url .= (count($params) > 0 ? '?' . http_build_query($params, null, '&', PHP_QUERY_RFC3986) : '');

        return new Request('GET', $url);
    }

    /**
     * @param ResponseInterface $response
     *
     * @param RequestInterface $request
     * @return stdClass
     *
     * @throws CouldNotParseResponseException
     * @throws InvalidApiKeyException
     * @throws ServerErrorException
     */
    private function parseResponse(ResponseInterface $response, RequestInterface $request)
    {
        $result = json_decode((string) $response->getBody()->getContents());

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CouldNotParseResponseException('Could not parse response', $response);
        }

        if (property_exists($result, 'error')) {
            switch ($result->error) {
                case 'API key is invalid.':
                    throw new InvalidApiKeyException();
                case 'An unknown server error occured.':
                    throw ServerErrorException::fromRequest($request);
            }
        }

        return $result;
    }

    /**
     * @param string $url
     * @throws InvalidUrlException
     */
    private function validateUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidUrlException($url);
        }

        $urlComponentsToCompare = [PHP_URL_HOST, PHP_URL_SCHEME];

        foreach ($urlComponentsToCompare as $urlComponent) {
            if (parse_url($url, $urlComponent) !== parse_url($this->url, $urlComponent)) {
                throw new InvalidUrlException($url);
            }
        }
    }
}
