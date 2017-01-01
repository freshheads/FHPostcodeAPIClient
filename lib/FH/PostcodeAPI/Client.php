<?php

namespace FH\PostcodeAPI;

use FH\PostcodeAPI\Exception\CouldNotParseResponseException;
use GuzzleHttp\Client as HTTPClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;

/**
 * Client library for postcodeapi.nu 2.0 web service.
 *
 * @author Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 */
class Client
{
    /** @var string */
    const BASE_URI = 'https://postcode-api.apiwise.nl';

    /** @var float */
    const TIMEOUT = 5.0;

    /** @var string */
    private $apiKey;

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
        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient;
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
     * @throws GuzzleException
     */
    private function get($path, array $queryParams = array())
    {
        $url = self::BASE_URI . $path;
        $options = [
            'headers' => ['X-Api-Key' => $this->apiKey],
            'timeout' => self::TIMEOUT,
            'query' => $queryParams
        ];

        $response = $this->httpClient->request('GET', $url, $options);

        return $this->parseResponse($response);
    }

    /**
     * @param Response $response
     *
     * @return \StdClass
     *
     * @throws CouldNotParseResponseException
     */
    private function parseResponse(Response $response)
    {
        $out = json_decode((string) $response->getBody());

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CouldNotParseResponseException('Could not parse resonse', $response);
        }

        return $out;
    }
}
