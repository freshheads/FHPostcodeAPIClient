<?php

namespace FH\PostcodeAPIClient;

use FH\PostcodeAPIClient\Exception\CouldNotParseResponseException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\ResponseInterface;

/**
 * Client library for postcodeapi.nu 2.0 web service.
 *
 * @author Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 */
final class FHPostcodeAPIClient
{
    /** @var string */
    const BASE_URI = 'https://postcode-api.apiwise.nl';

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var float
     */
    private $timeout;

    /**
     * @param string $apiKey        Required API key for authenticating client
     * @param float $timeout        Timeout in seconds
     */
    public function __construct($apiKey, $timeout = 3.0)
    {
        $this->apiKey = $apiKey;
        $this->timeout = $timeout;
    }

    /**
     * @param string|null $postcode
     * @param string|null $number
     * @param int $from
     *
     * @return \StdClass
     */
    public function getAddresses($postcode = null, $number = null, $from = 0)
    {
        return $this->get('/v2/addresses/', [
            'postcode' => $postcode,
            'number' => $number,
            'from' => $from
        ]);
    }

    /**
     * @param string $id
     *
     * @return \StdClass
     */
    public function getAddresss($id)
    {
        return $this->get("/v2/addresses/{$id}");
    }

    /**
     * @param string $path
     * @param array $queryParams
     *
     * @return \StdClass
     *
     * @throws RequestException
     */
    private function get($path, array $queryParams = array())
    {
        $request = $this->createHttpRequest('GET', $path, $queryParams);

        $response = $this->getHttpClient()->send($request);

        return $this->parseResponse($response);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return \StdClass
     *
     * @throws CouldNotParseResponseException
     */
    private function parseResponse(ResponseInterface $response)
    {
        $out = json_decode((string) $response->getBody(), false, 512, JSON_BIGINT_AS_STRING);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CouldNotParseResponseException('Could not parse resonse', $response);
        }

        return $out;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $queryParams
     *
     * @return Request
     */
    private function createHttpRequest($method, $path, array $queryParams = array())
    {
        $path = $path . (count($queryParams) > 0 ? '?' . http_build_query($queryParams) : '');

        return $this->getHttpClient()->createRequest($method, $path, [
            'headers' => [
                'X-Api-Key' => $this->apiKey
            ]
        ]);
    }

    /**
     * @return Client
     */
    private function getHttpClient()
    {
        if ($this->httpClient instanceof Client) {
            return $this->httpClient;
        }

        $this->httpClient = new Client([
            'base_url' => self::BASE_URI,
            'timeout' => $this->timeout,
            'headers' => [
                'X-Api-Key' => $this->apiKey
            ]
        ]);

        return $this->httpClient;
    }
}
