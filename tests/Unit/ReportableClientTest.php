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

class ReportableClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \CappasitySDK\Client|\PHPUnit_Framework_MockObject_MockObject
     */
    private $clientMock;

    /**
     * @var \Raven_Client|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ravenClientMock;

    public function setUp()
    {
        $this->clientMock = $this->getMockBuilder(\CappasitySDK\Client::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getEmbedCode',
                'registerSyncJob',
                'getPullJobList',
                'ackPullJobList',
                'getPullJobResult',
            ])
            ->getMock();

        $this->ravenClientMock = $this->getMockBuilder(\Raven_Client::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'captureException',
            ])
            ->getMock();
    }

    /**
     * @expectedException \Exception
     */
    public function testRegisterSyncJob()
    {
        $client = new \CappasitySDK\ReportableClient($this->clientMock, $this->ravenClientMock);

        $mockedException = new \Exception();

        $this->clientMock
            ->expects($this->once())
            ->method('registerSyncJob')
            ->willThrowException($mockedException);

        $this->ravenClientMock
            ->expects($this->once())
            ->method('captureException')
            ->with($mockedException);

        $client->registerSyncJob(\CappasitySDK\Client\Model\Request\Process\JobsRegisterSyncPost::fromData(
            [
                [
                    'id' => 'inner-product-id',
                    'aliases' => ['Bear'],
                    'capp' => 'stub-file-id',
                ]
            ],
            'pull'
        ));
    }

    /**
     * @expectedException \Exception
     */
    public function testGetPullJobList()
    {
        $client = new \CappasitySDK\ReportableClient($this->clientMock, $this->ravenClientMock);

        $mockedException = new \Exception();

        $this->clientMock
            ->expects($this->once())
            ->method('getPullJobList')
            ->willThrowException($mockedException);

        $this->ravenClientMock
            ->expects($this->once())
            ->method('captureException')
            ->with($mockedException);

        $client->getPullJobList(\CappasitySDK\Client\Model\Request\Process\JobsPullListGet::fromData(null, null));
    }

    /**
     * @expectedException \Exception
     */
    public function testAckPullJobList()
    {
        $client = new \CappasitySDK\ReportableClient($this->clientMock, $this->ravenClientMock);

        $mockedException = new \Exception();

        $this->clientMock
            ->expects($this->once())
            ->method('ackPullJobList')
            ->willThrowException($mockedException);

        $this->ravenClientMock
            ->expects($this->once())
            ->method('captureException')
            ->with($mockedException);

        $client->ackPullJobList(\CappasitySDK\Client\Model\Request\Process\JobsPullAckPost::fromData(['a9673347-8f2e-4caa-83e9-4139d7473c2f:A1']));
    }

    /**
     * @expectedException \Exception
     */
    public function testGetPullJobResult()
    {
        $client = new \CappasitySDK\ReportableClient($this->clientMock, $this->ravenClientMock);

        $mockedException = new \Exception();

        $this->clientMock
            ->expects($this->once())
            ->method('getPullJobResult')
            ->willThrowException($mockedException);

        $this->ravenClientMock
            ->expects($this->once())
            ->method('captureException')
            ->with($mockedException);

        $client->getPullJobResult(\CappasitySDK\Client\Model\Request\Process\JobsPullResultGet::fromData('a9673347-8f2e-4caa-83e9-4139d7473c2f:A1'));
    }
}
