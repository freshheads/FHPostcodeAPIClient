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
    const POSTCODES_SORT_DISTANCE = 'distance';

    /**
     * @var string
     */
    private $uriScheme  = 'https://';

    /**
     * @var null|string
     */
    private $domain     = 'postcode-api.apiwise.nl';

    /**
     * @var string
     */
    private $version    = 'v2';

    /**
     * @var HTTPClient
     */
    private $httpClient;


    public function __construct(ClientInterface $httpClient, $domain = null)
    {
        if (null !== $domain) {
            $this->domain = $domain;
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
     * @param string $path
     * @param array $queryParams
     *
     * @return \stdClass
     *
     * @throws RequestException
     */
    private function get($path, array $queryParams = array())
    {
        $request = $this->createHttpRequest('GET', sprintf('%s%s/%s%s', $this->uriScheme, $this->domain, $this->version, $path),
            $queryParams
        );

        $response = $this->httpClient->send($request);

        return $this->parseResponse($response);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return \stdClass
     *
     * @throws CouldNotParseResponseException
     */
    private function parseResponse(ResponseInterface $response)
    {
        $out = json_decode((string) $response->getBody());

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CouldNotParseResponseException('Could not parse response', $response);
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
