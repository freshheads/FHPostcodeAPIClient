<?php

namespace FH\PostcodeAPI;

use FH\PostcodeAPI\Exception\CouldNotParseResponseException;
use GuzzleHttp\Client as HTTPClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\ResponseInterface;

/**
 * Client library for postcodeapi.nu 2.0 web service.
 *
 * @author Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 */
class Client
{
    /** @var string */
    const BASE_URI = 'https://postcode-api.apiwise.nl';

    /**
     * @var HTTPClient
     */
    private $httpClient;

    /**
     * @param ClientInterface $httpClient
     * @param string $apiKey Required API key for authenticating client
     */
    public function __construct(ClientInterface $httpClient, $apiKey)
    {
        $this->httpClient = $this->prepareClient($httpClient, $apiKey);
    }

    /**
     * @param ClientInterface $client
     * @param string $apiKey
     *
     * @return HTTPClient
     */
    private function prepareClient(ClientInterface $client, $apiKey)
    {
        if ($client->getDefaultOption('timeout') === null) {
            $client->setDefaultOption('timeout', 5.0);
        }

        $client->setDefaultOption('headers/X-Api-Key', $apiKey);

        return $client;
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
    public function getAddress($id)
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
        $url = self::BASE_URI . $path;

        $request = $this->createHttpRequest('GET', $url, $queryParams);

        $response = $this->httpClient->send($request);

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
        $out = json_decode((string) $response->getBody());

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

        return $this->httpClient->createRequest($method, $path);
    }
}
