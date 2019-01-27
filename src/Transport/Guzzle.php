<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licensed only to registered users of the Cappasity platform.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Cappasity Inc <info@cappasity.com>
 * @copyright 2019 Cappasity Inc.
 */

namespace CappasitySDK\Transport;

use GuzzleHttp;
use CappasitySDK;

class Guzzle implements CappasitySDK\TransportInterface
{
    /**
     * @var GuzzleHttp\ClientInterface
     */
    private $httpClient;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private static $defaultConfig = [
        'timeout' => 5,
    ];

    public function __construct(GuzzleHttp\ClientInterface $httpClient, array $config = [])
    {
        $this->httpClient = $httpClient;
        $this->config = array_replace_recursive(self::$defaultConfig, $config);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $options
     *
     * @return ResponseContainer
     * @throws Exception\RequestException
     */
    public function makeRequest($method, $url, array $options = [])
    {
        $request = $this->httpClient->createRequest($method, $url, $this->resolveOptions($options));

        try {
            $response = $this->httpClient->send($request);
        } catch (GuzzleHttp\Exception\RequestException $e) {
            throw $this->getWrappedException($e);
        }

        if ($response->getStatusCode() >= 400) {
            throw static::createExceptionFromErrorResponse($request, $response);
        }

        return $this->transformResponse($response);
    }

    /**
     * @param \GuzzleHttp\Message\RequestInterface $request
     * @param \GuzzleHttp\Message\ResponseInterface $response
     *
     * @return Exception\RequestException
     */
    public static function createExceptionFromErrorResponse(
        \GuzzleHttp\Message\RequestInterface $request,
        \GuzzleHttp\Message\ResponseInterface $response
    ) {
        if ($response->getStatusCode() < 400) {
            $className = static::class;
            throw new \LogicException("Attempted to create instance of {$className} from a non error response");
        }

        $e = new Exception\RequestException(static::makeErrorMessage($response), $response->getStatusCode());
        $e->setRequest($request);
        $e->setResponse($response);

        return $e;
    }

    /**
     * @return Guzzle
     */
    public static function createDefault()
    {
        return new self(static::createDefaultHttpClient());
    }

    /**
     * @param array $config
     *
     * @return Guzzle
     */
    public static function createWithConfig(array $config)
    {
        return new self(static::createDefaultHttpClient(), $config);
    }

    /**
     * Possible formats are:
     * I.
     * {
     *   "statusCode": 404,
     *   "error": "Not Found",
     *   "message": "job data missing",
     *   "name": "HttpStatusError"
     * }
     *
     * II.
     * {
     *   "meta": {
     *     "id": "d715ee2b-aea5-4f78-94ee-c7ec3bfaad40"
     *   },
     *   "errors": [
     *     {
     *       "status": "HttpStatusError",
     *       "code": 404,
     *       "title": "could not find associated data",
     *       "detail": {}
     *     }
     *   ]
     * }
     *
     * @param GuzzleHttp\Message\ResponseInterface $response
     * @return string
     *
     * @throws Exception\UnexpectedResponseFormatException
     */
    private static function makeErrorMessage(GuzzleHttp\Message\ResponseInterface $response)
    {
        try {
            $parsedResponse = $response->json();
        } catch (\RuntimeException $e) {
            throw new Exception\UnexpectedResponseFormatException('Can not parse response as JSON');
        }

        $hasValidProcessErrorStructure = array_key_exists('statusCode', $parsedResponse)
            && array_key_exists('error', $parsedResponse)
            && array_key_exists('message', $parsedResponse)
            && array_key_exists('name', $parsedResponse);

        if ($hasValidProcessErrorStructure) {
            $message = $parsedResponse['message'];
            $error = $parsedResponse['error'];
            $statusCode = $parsedResponse['statusCode'];

            return "Server responded with an error [{$statusCode}: {$error}]: {$message}";
        }

        $hasValidFilesErrorStructure = !$hasValidProcessErrorStructure
            && array_key_exists('errors', $parsedResponse)
            && is_array($parsedResponse['errors'])
            && array_reduce($parsedResponse['errors'], function ($isValid, array $errorItem) {
                $isValid = $isValid
                    && array_key_exists('title', $errorItem)
                    && array_key_exists('detail', $errorItem);

                return $isValid;
            }, true);

        if (!$hasValidFilesErrorStructure) {
            $message = json_encode($parsedResponse);
            throw new Exception\UnexpectedResponseFormatException(
                "Unknown error response structure received: ${message}"
            );
        }

        $errorTitlesAndDetails = array_map(
            function (array $error) {
                $details = $error['detail'] ? json_encode($error['detail']) : 'none';
                return "{$error['title']} (detail: {$details})";
            },
            $parsedResponse['errors']
        );
        $message = join('; ', $errorTitlesAndDetails);

        return "Server responded with an error [{$response->getStatusCode()}]: {$message}";
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function resolveOptions(array $options)
    {
        $resolvedOptions = [];
        if (array_key_exists('query', $options)) {
            $resolvedOptions['query'] = $options['query'];
        }
        if (array_key_exists('data', $options)) {
            $resolvedOptions['json'] = $options['data'];
        }
        if (array_key_exists('headers', $options)) {
            $resolvedOptions['headers'] = $options['headers'];
        }
        if (array_key_exists('timeout', $options)) {
            $resolvedOptions['timeout'] = $options['timeout'];
        }

        return $resolvedOptions;
    }

    /**
     * @param GuzzleHttp\Message\ResponseInterface $response
     * @return ResponseContainer
     */
    private function transformResponse(GuzzleHttp\Message\ResponseInterface $response)
    {
        $headers = array_map(
            function ($headerName) use ($response) { return $response->getHeaderAsArray($headerName); },
            array_keys($response->getHeaders())
        );

        return new ResponseContainer(
            $response->getStatusCode(),
            $headers,
            $response->json(),
            $response
        );
    }

    /**
     * @param GuzzleHttp\Exception\RequestException $original
     * @return Exception\RequestException
     */
    private function getWrappedException(\GuzzleHttp\Exception\RequestException $original)
    {
        $e = new Exception\RequestException($original->getMessage(), $original->getCode(), $original->getPrevious());
        $e->setRequest($original->getRequest());
        $e->setResponse($original->getResponse());

        return $e;
    }

    /**
     * @return GuzzleHttp\Client
     */
    private static function createDefaultHttpClient()
    {
        return new GuzzleHttp\Client();
    }
}
