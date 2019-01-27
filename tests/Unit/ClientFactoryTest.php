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

namespace CappasitySDK\Tests\Unit;

class ClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetClientInstance()
    {
        $client = \CappasitySDK\ClientFactory::getClientInstance([
            'apiToken' => 'api.token.stub',
            'config' => [
                'baseUrl' => \CappasitySDK\Client::BASE_URL_API_CAPPASITY
            ]
        ]);

        $this->assertEquals(\CappasitySDK\Client::class, get_class($client));
        $this->assertEquals(\CappasitySDK\Client::BASE_URL_API_CAPPASITY,  $client->getConfig()['baseUrl']);
        $this->assertEquals('api.token.stub', $client->getApiToken());
    }

    public function testGetClientInstanceWithReportingTurnedOn()
    {
        $client = \CappasitySDK\ClientFactory::getClientInstance([
            'apiToken' => 'api.token.stub',
            'sendReports' => true,
        ]);

        $this->assertEquals(\CappasitySDK\ReportableClient::class, get_class($client));
    }
}
