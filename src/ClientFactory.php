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

namespace CappasitySDK;

class ClientFactory
{
    const GUZZLE_TRANSPORT = 'guzzle';

    private static $defaultOptions = [
        'apiToken' => null,
        'transport' => [
            'type' => self::GUZZLE_TRANSPORT,
            'httpClientConfig' => [
                'defaults' => [
                    'exceptions' => false,
                ]
            ],
            'options' => [],
        ],
        'sendReports' => false,
        'config' => []
    ];

    public static function getClientInstance(array $options)
    {
        $resolvedOptions = array_replace_recursive(self::$defaultOptions, $options);
        $transportOptions = $resolvedOptions['transport']['options'];
        $httpClientConfig = $resolvedOptions['transport']['httpClientConfig'];
        $httpClient = new \GuzzleHttp\Client($httpClientConfig);
        $apiToken = $resolvedOptions['apiToken'];

        if ($resolvedOptions['transport']['type'] === self::GUZZLE_TRANSPORT) {
            $transport = new \CappasitySDK\Transport\Guzzle($httpClient, $transportOptions);
        } else {
            throw new \LogicException(sprintf('Unhandled transport type %s', $resolvedOptions['transport']));
        }

        $validator = ValidatorWrapper::setUpInstance();
        $responseAdapter = new ResponseAdapter();
        $clientConfig = $resolvedOptions['config'];

        $client = new Client($transport, $apiToken, $validator, $responseAdapter, $clientConfig);

        if ($resolvedOptions['sendReports'] === true) {
            $client = ReportableClient::createWithClient($client);
        }

        return $client;
    }
}
