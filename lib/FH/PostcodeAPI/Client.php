<?php

namespace FH\PostcodeAPI;

use FH\PostcodeAPI\Exception\CouldNotParseResponseException;
use GuzzleHttp\Client as HTTPClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

/**
 * Client library for postcodeapi.nu 2.0 web service.
 *
 * @author Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 */
class Client
{
    const BASE_URI = 'https://postcode-api.apiwise.nl';

    /**
     * @var HTTPClient
     */
    private $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
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
            throw new CouldNotParseResponseException('Could not parse repsonse', $response);
        }

        return $out;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $queryParams
     *
     * @return Request
     */
    private function createHttpRequest($method, $url, array $queryParams = array())
    {
        $url = $url . (count($queryParams) > 0 ? '?' . http_build_query($queryParams) : '');

        return new Request($method, $url);
    }
}
