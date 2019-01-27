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

use CappasitySDK\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    const STUB_API_TOKEN = 'api.token.stub';
    const STUB_SKU = 'Bear';
    const STUB_FILE_ID = '38020fdf-5e11-411c-9116-1610339d59cf';
    const STUB_IFRAME = '<iframe allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" width="100%" height="600px" frameborder="0" style="border:0;" onmousewheel="" src="https://api.cappasity.com/api/player/9473eb1e-3fa6-4e75-aa34-6c4e01f10ff5/embedded?autorun=0&closebutton=1&logo=1&autorotate=0&autorotatetime=10&autorotatedelay=2&autorotatedir=1&hidefullscreen=1&hideautorotateopt=1&hidesettingsbtn=0"></iframe>';

    /**
     * @var \CappasitySDK\TransportInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transportMock;

    /**
     * @var string
     */
    private $apiToken;

    /**
     * @var \CappasitySDK\ValidatorWrapper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $validatorMock;

    /**
     * @var \CappasitySDK\ResponseAdapter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $responseAdapterMock;

    /**
     * @var array
     */
    private $config;

    protected function setUp()
    {
        parent::setUp();

        $this->transportMock = $this->getMockBuilder(\CappasitySDK\Transport\Guzzle::class)
            ->disableOriginalConstructor()
            ->setMethods(['makeRequest'])
            ->getMock();

        $this->apiToken = self::STUB_API_TOKEN;

        $this->validatorMock = $this->getMockBuilder(\CappasitySDK\ValidatorWrapper::class)
            ->disableOriginalConstructor()
            ->setMethods(['assert', 'buildByType'])
            ->getMock();

        $this->responseAdapterMock = $this->getMockBuilder(\CappasitySDK\ResponseAdapter::class)
            ->disableOriginalConstructor()
            ->setMethods(['transform'])
            ->getMock();

        $this->config = [
            'baseUrl' => \CappasitySDK\Client::BASE_URL_API_CAPPASITY
        ];
    }

    public function testRegisterPullSyncJob()
    {
        $client = $this->makeClient();
        $requestParams = Client\Model\Request\Process\JobsRegisterSyncPost::fromData(
            [
                [
                    'id' => 'inner-product-id',
                    'aliases' => ['Bear'],
                    'capp' => self::STUB_FILE_ID,
                ],
            ],
            'pull'
        );
        $mockedResponseData = [
            'data' => [
                'id' => '169da70c-9eda-4e80-b45d-efe0475810f6:1',
                'type' => 'sync',
                'attributes' => [
                    'type' => 'pull',
                    'gzip' => true,
                ],
            ],
        ];
        $mockedTransportResponse = $this->makeTransportResponseContainer(200, $mockedResponseData);
        $mockedClientResponse = $this->makeClientResponseContainer(
            $mockedTransportResponse,
            Client\Model\Response\Process\JobsRegisterSyncPost::class
        );

        $this->expectValidationPerformed(
            $requestParams,
            Client\Validator\Type\Request\Process\JobsRegisterSyncPost::class
        );
        $this->expectRequestMade(
            [
                'POST',
                'https://api.cappasity.com/api/cp/jobs/register/sync',
                [
                    'headers' => [
                        'authorization' => "Bearer {$this->apiToken}",
                    ],
                    'data' => [
                        'data' => [
                            [
                                'id' => 'inner-product-id',
                                'type' => 'product',
                                'attributes' => [
                                    'aliases' => ['Bear'],
                                    'capp' => self::STUB_FILE_ID,
                                ]
                            ],
                        ],
                        'meta' => [
                            'type' => 'pull',
                        ],
                    ],
                    'timeout' => 5,
                ]
            ],
            $mockedTransportResponse
        );
        $this->expectResponseTransformed(
            [$mockedTransportResponse, Client\Model\Response\Process\JobsRegisterSyncPost::class],
            $mockedClientResponse
        );

        $actualResponse = $client->registerSyncJob($requestParams);

        $this->assertEquals(200, $actualResponse->getStatusCode());

        /** @var Client\Model\Response\Process\JobsRegisterSyncPost $actualResponseData */
        $actualResponseData = $actualResponse->getBodyData();

        $this->assertEquals('169da70c-9eda-4e80-b45d-efe0475810f6:1', $actualResponseData->getData()->getId());
        $this->assertEquals('sync', $actualResponseData->getData()->getType());
        $this->assertEquals('pull', $actualResponseData->getData()->getAttributes()->getType());
        $this->assertEquals(true, $actualResponseData->getData()->getAttributes()->getGzip());
    }

    public function testRegisterPushSyncJob()
    {
        $client = $this->makeClient();
        $requestParams = Client\Model\Request\Process\JobsRegisterSyncPost::fromData(
            [
                [
                    'id' => 'inner-product-id',
                    'aliases' => ['Bear'],
                    'capp' => self::STUB_FILE_ID,
                ],
            ],
            'push:http',
            'http://somewhere.com/over/the/rainbow'
        );
        $mockedResponseData = [
            'data' => [
                'id' => '169da70c-9eda-4e80-b45d-efe0475810f6:1',
                'type' => 'sync',
                'attributes' => [
                    'type' => 'push:http',
                    'gzip' => true,
                ],
            ],
        ];
        $mockedTransportResponse = $this->makeTransportResponseContainer(200, $mockedResponseData);
        $mockedClientResponse = $this->makeClientResponseContainer(
            $mockedTransportResponse,
            Client\Model\Response\Process\JobsRegisterSyncPost::class
        );

        $this->expectValidationPerformed(
            $requestParams,
            Client\Validator\Type\Request\Process\JobsRegisterSyncPost::class
        );
        $this->expectRequestMade(
            [
                'POST',
                'https://api.cappasity.com/api/cp/jobs/register/sync',
                [
                    'headers' => [
                        'authorization' => "Bearer {$this->apiToken}",
                    ],
                    'data' => [
                        'data' => [
                            [
                                'id' => 'inner-product-id',
                                'type' => 'product',
                                'attributes' => [
                                    'aliases' => ['Bear'],
                                    'capp' => self::STUB_FILE_ID,
                                ]
                            ],
                        ],
                        'meta' => [
                            'type' => 'push:http',
                            'callback' => 'http://somewhere.com/over/the/rainbow',
                        ],
                    ],
                    'timeout' => 5,
                ]
            ],
            $mockedTransportResponse
        );
        $this->expectResponseTransformed(
            [$mockedTransportResponse, Client\Model\Response\Process\JobsRegisterSyncPost::class],
            $mockedClientResponse
        );

        $actualResponse = $client->registerSyncJob($requestParams);

        $this->assertEquals(200, $actualResponse->getStatusCode());

        /** @var Client\Model\Response\Process\JobsRegisterSyncPost $actualResponseData */
        $actualResponseData = $actualResponse->getBodyData();

        $this->assertEquals('169da70c-9eda-4e80-b45d-efe0475810f6:1', $actualResponseData->getData()->getId());
        $this->assertEquals('sync', $actualResponseData->getData()->getType());
        $this->assertEquals('push:http', $actualResponseData->getData()->getAttributes()->getType());
        $this->assertEquals(true, $actualResponseData->getData()->getAttributes()->getGzip());
    }

    public function testGetPullJobsList()
    {
        $client = new Client(
            $this->transportMock,
            $this->apiToken,
            $this->validatorMock,
            $this->responseAdapterMock,
            $this->config
        );
        $requestParams = Client\Model\Request\Process\JobsPullListGet::fromData(null, null);
        $mockedResponseData = [
            'meta' => [
                'cursor' => 1536334268895,
                'limit' => 10,
            ],
            'data' => [
                [
                    'id' => '169da70c-9eda-4e80-b45d-efe0475810f6:1',
                    'type' => 'sync',
                    'attributes' => [
                        'status' => 'success',
                    ],
                ],
            ],
        ];
        $mockedTransportResponse = $this->makeTransportResponseContainer(200, $mockedResponseData);
        $mockedClientResponse = $this->makeClientResponseContainer(
            $mockedTransportResponse,
            Client\Model\Response\Process\JobsPullListGet::class
        );
        $this->expectValidationPerformed(
            $requestParams,
            Client\Validator\Type\Request\Process\JobsPullListGet::class
        );
        $this->expectRequestMade(
            [
                'GET',
                'https://api.cappasity.com/api/cp/jobs/pull/list',
                [
                    'headers' => [
                        'authorization' => "Bearer {$this->apiToken}",
                    ],
                    'query' => [],
                    'timeout' => 5,
                ]
            ],
            $mockedTransportResponse
        );
        $this->expectResponseTransformed(
            [$mockedTransportResponse, Client\Model\Response\Process\JobsPullListGet::class],
            $mockedClientResponse
        );

        $actualResponse = $client->getPullJobList($requestParams);

        $this->assertEquals(200, $actualResponse->getStatusCode());

        /** @var Client\Model\Response\Process\JobsPullListGet $actualResponseData */
        $actualResponseData = $actualResponse->getBodyData();

        $this->assertEquals(1536334268895, $actualResponseData->getMeta()->getCursor());
        $this->assertEquals(10, $actualResponseData->getMeta()->getLimit());
        $this->assertEquals('169da70c-9eda-4e80-b45d-efe0475810f6:1', $actualResponseData->getData()[0]->getId());
        $this->assertEquals('sync', $actualResponseData->getData()[0]->getType());
        $this->assertEquals('success', $actualResponseData->getData()[0]->getAttributes()->getStatus());
    }

    public function testGetPullJobsListWithLimitAndCursor()
    {
        $client = $this->makeClient();
        $requestParams = Client\Model\Request\Process\JobsPullListGet::fromData(30, 1536334268895);
        $mockedResponseData = [
            'meta' => [
                'cursor' => 1536334268895,
                'limit' => 30,
            ],
            'data' => [
                [
                    'id' => '169da70c-9eda-4e80-b45d-efe0475810f6:1',
                    'type' => 'sync',
                    'attributes' => [
                        'status' => 'success',
                    ],
                ],
            ],
        ];
        $mockedTransportResponse = $this->makeTransportResponseContainer(200, $mockedResponseData);
        $mockedClientResponse = $this->makeClientResponseContainer(
            $mockedTransportResponse,
            Client\Model\Response\Process\JobsPullListGet::class
        );
        $this->expectValidationPerformed(
            $requestParams,
            Client\Validator\Type\Request\Process\JobsPullListGet::class
        );
        $this->expectRequestMade(
            [
                'GET',
                'https://api.cappasity.com/api/cp/jobs/pull/list',
                [
                    'headers' => [
                        'authorization' => "Bearer {$this->apiToken}",
                    ],
                    'query' => [
                        'limit' => 30,
                        'cursor' => 1536334268895,
                    ],
                    'timeout' => 5,
                ]
            ],
            $mockedTransportResponse
        );
        $this->expectResponseTransformed(
            [$mockedTransportResponse, Client\Model\Response\Process\JobsPullListGet::class],
            $mockedClientResponse
        );

        $actualResponse = $client->getPullJobList($requestParams);

        $this->assertEquals(200, $actualResponse->getStatusCode());

        /** @var Client\Model\Response\Process\JobsPullListGet $actualResponseData */
        $actualResponseData = $actualResponse->getBodyData();

        $this->assertEquals(1536334268895, $actualResponseData->getMeta()->getCursor());
        $this->assertEquals(30, $actualResponseData->getMeta()->getLimit());
        $this->assertEquals('169da70c-9eda-4e80-b45d-efe0475810f6:1', $actualResponseData->getData()[0]->getId());
        $this->assertEquals('sync', $actualResponseData->getData()[0]->getType());
        $this->assertEquals('success', $actualResponseData->getData()[0]->getAttributes()->getStatus());
    }

    public function testAckPullJobList()
    {
        $client = $this->makeClient();
        $requestParams = Client\Model\Request\Process\JobsPullAckPost::fromData(['a9673347-8f2e-4caa-83e9-4139d7473c2f:A1']);
        $mockedResponseData = [
            'data' => [
                'ack' => 1,
            ],
        ];
        $mockedTransportResponse = $this->makeTransportResponseContainer(200, $mockedResponseData);
        $mockedClientResponse = $this->makeClientResponseContainer(
            $mockedTransportResponse,
            Client\Model\Response\Process\JobsPullAckPost::class
        );
        $this->expectValidationPerformed(
            $requestParams,
            Client\Validator\Type\Request\Process\JobsPullAckPost::class
        );
        $this->expectRequestMade(
            [
                'POST',
                'https://api.cappasity.com/api/cp/jobs/pull/ack',
                [
                    'headers' => [
                        'authorization' => "Bearer {$this->apiToken}",
                    ],
                    'data' => [
                        'data' => [
                            [
                                'id' => 'a9673347-8f2e-4caa-83e9-4139d7473c2f:A1',
                                'type' => 'sync'
                            ],
                        ],
                    ],
                    'timeout' => 5,
                ],
            ],
            $mockedTransportResponse
        );
        $this->expectResponseTransformed(
            [$mockedTransportResponse, Client\Model\Response\Process\JobsPullAckPost::class],
            $mockedClientResponse
        );

        $actualResponse = $client->ackPullJobList($requestParams);

        $this->assertEquals(200, $actualResponse->getStatusCode());
        /** @var Client\Model\Response\Process\JobsPullAckPost $actualResponseData */
        $actualResponseData = $actualResponse->getBodyData();

        $this->assertEquals(1, $actualResponseData->getData()->getAck());
    }

    public function testGetPullJobResult()
    {
        $client = $this->makeClient();
        $requestParams = Client\Model\Request\Process\JobsPullResultGet::fromData('a9673347-8f2e-4caa-83e9-4139d7473c2f:A1');
        $mockedResponseData = [
            'meta' => [
                'jobId' => 'a9673347-8f2e-4caa-83e9-4139d7473c2f:A1',
                'jobType' => 'sync',
            ],
            'data' => [
                [
                    'id' => '123',
                    'uploadId' => '9473eb1e-3fa6-4e75-aa34-6c4e01f10ff5',
                    'sku' => 'Bear',
                ],
                [
                    'id' => '124',
                    'uploadId' => false,
                    'capp' => '9473eb1e-3fa6-4e75-aa34-6c4e01fabd64'
                ]
            ],
        ];
        $mockedTransportResponse = $this->makeTransportResponseContainer(200, $mockedResponseData);
        $mockedClientResponse = $this->makeClientResponseContainer(
            $mockedTransportResponse,
            Client\Model\Response\Process\JobsPullResultGet::class
        );
        $this->expectValidationPerformed(
            $requestParams,
            Client\Validator\Type\Request\Process\JobsPullResultGet::class
        );
        $this->expectRequestMade(
            [
                'GET',
                'https://api.cappasity.com/api/cp/jobs/pull/result',
                [
                    'headers' => [
                        'authorization' => "Bearer {$this->apiToken}",
                    ],
                    'query' => [
                        'id' => 'a9673347-8f2e-4caa-83e9-4139d7473c2f:A1',
                    ],
                    'timeout' => 5
                ]
            ],
            $mockedTransportResponse
        );
        $this->expectResponseTransformed(
            [$mockedTransportResponse, Client\Model\Response\Process\JobsPullResultGet::class],
            $mockedClientResponse
        );

        $actualResponse = $client->getPullJobResult($requestParams);

        $this->assertEquals(200, $actualResponse->getStatusCode());

        /** @var Client\Model\Response\Process\JobsPullResultGet $actualResponseData */
        $actualResponseData = $actualResponse->getBodyData();

        $this->assertEquals('a9673347-8f2e-4caa-83e9-4139d7473c2f:A1', $actualResponseData->getMeta()->getJobId());
        $this->assertArrayHasKey(0, $actualResponseData->getData());
        $this->assertArrayHasKey(1, $actualResponseData->getData());
        $responseDataItem1 = $actualResponseData->getData()[0];
        $this->assertEquals('123', $responseDataItem1->getId());
        $this->assertEquals('9473eb1e-3fa6-4e75-aa34-6c4e01f10ff5', $responseDataItem1->getUploadId());
        $this->assertEquals('Bear', $responseDataItem1->getSku());
        $this->assertEquals(null, $responseDataItem1->getCapp());
        $responseDataItem2 = $actualResponseData->getData()[1];
        $this->assertEquals('124', $responseDataItem2->getId());
        $this->assertEquals(false, $responseDataItem2->getUploadId());
        $this->assertEquals(null, $responseDataItem2->getSku());
        $this->assertEquals('9473eb1e-3fa6-4e75-aa34-6c4e01fabd64', $responseDataItem2->getCapp());
    }

    /**
     * @expectedException \CappasitySDK\Client\Exception\RequestException
     */
    public function testGetPullJobResultNoResultsYet()
    {
        $client = $this->makeClient();
        $requestParams = Client\Model\Request\Process\JobsPullResultGet::fromData('a9673347-8f2e-4caa-83e9-4139d7473c2f:A1');
        $mockedResponseData = [
            'meta' => [
                'jobId' => 'a9673347-8f2e-4caa-83e9-4139d7473c2f:A1',
                'jobType' => 'sync',
            ],
            'data' => [
                [
                    'id' => '123',
                    'uploadId' => '9473eb1e-3fa6-4e75-aa34-6c4e01f10ff5',
                    'sku' => 'Bear',
                ],
                [
                    'id' => '124',
                    'uploadId' => false,
                    'capp' => '9473eb1e-3fa6-4e75-aa34-6c4e01fabd64'
                ]
            ],
        ];
        $mockedTransportResponse = $this->makeTransportResponseContainer(404, $mockedResponseData);
        $this->expectValidationPerformed(
            $requestParams,
            Client\Validator\Type\Request\Process\JobsPullResultGet::class
        );

        $expectedException = new \CappasitySDK\Transport\Exception\RequestException(
            'Server responded with an error [404: Not Found]: job data missing'
        );
        $expectedException->setResponse($mockedTransportResponse);
        $this->expectRequestMadeAndFailed(
            [
                'GET',
                'https://api.cappasity.com/api/cp/jobs/pull/result',
                [
                    'headers' => [
                        'authorization' => "Bearer {$this->apiToken}",
                    ],
                    'query' => [
                        'id' => 'a9673347-8f2e-4caa-83e9-4139d7473c2f:A1',
                    ],
                    'timeout' => 5
                ]
            ],
            $expectedException
        );

        $client->getPullJobResult($requestParams);
    }

    /**
     * @return Client
     */
    private function makeClient()
    {
        return new Client(
            $this->transportMock,
            $this->apiToken,
            $this->validatorMock,
            $this->responseAdapterMock,
            $this->config
        );
    }

    /**
     * @param $requestParams
     * @param $validatorType
     */
    private function expectValidationPerformed($requestParams, $validatorType)
    {
        $typeValidatorMock = $this->getMockBuilder(\Respect\Validation\Validator::class)->getMock();

        $this->validatorMock
            ->expects($this->once())
            ->method('buildByType')
            ->with($validatorType)
            ->willReturn($typeValidatorMock);

        $this->validatorMock
            ->expects($this->once())
            ->method('assert')
            ->with($requestParams, $typeValidatorMock)
            ->willReturn(true);
    }

    /**
     * @param array $makeRequestArguments
     * @param \CappasitySDK\Transport\ResponseContainer $willReturnResponse
     */
    private function expectRequestMade(
        array $makeRequestArguments,
        \CappasitySDK\Transport\ResponseContainer $willReturnResponse
    ) {
        $this->transportMock
            ->expects($this->once())
            ->method('makeRequest')
            ->with(...$makeRequestArguments)
            ->willReturn($willReturnResponse);
    }

    /**
     * @param array $makeRequestArguments
     * @param \CappasitySDK\Transport\Exception\RequestException $willThrowException
     */
    private function expectRequestMadeAndFailed(
        array $makeRequestArguments,
        \CappasitySDK\Transport\Exception\RequestException $willThrowException
    ) {
        $this->transportMock
            ->expects($this->once())
            ->method('makeRequest')
            ->with(...$makeRequestArguments)
            ->willThrowException($willThrowException);
    }

    /**
     * @param array $transformArguments
     * @param $modelResponseContainer
     */
    private function expectResponseTransformed(array $transformArguments, $modelResponseContainer) {
        $this->responseAdapterMock
            ->expects($this->once())
            ->method('transform')
            ->with(...$transformArguments)
            ->willReturn($modelResponseContainer);
    }

    /**
     * @param $code
     * @param array $data
     * @param array $headers
     * @return \CappasitySDK\Transport\ResponseContainer
     */
    private function makeTransportResponseContainer($code, array $data, $headers = [])
    {
        $mockedResponseBody = \GuzzleHttp\Stream\Stream::factory(json_encode($data));
        $mockedOriginalResponse = new \GuzzleHttp\Message\Response($code, $headers, $mockedResponseBody);

        return new \CappasitySDK\Transport\ResponseContainer($code, $headers, $data, $mockedOriginalResponse);
    }

    /**
     * @param \CappasitySDK\Transport\ResponseContainer $transportResponseContainer
     * @param $className
     * @return Client\Model\Response\Container
     */
    private function makeClientResponseContainer(\CappasitySDK\Transport\ResponseContainer $transportResponseContainer, $className)
    {
        if (!method_exists($className, 'fromResponse')) {
            throw new \LogicException('Class found by classname does not have `fromResponse` method');
        }

        return new \CappasitySDK\Client\Model\Response\Container(
            200,
            [],
            $className::fromResponse($transportResponseContainer->getData()),
            $transportResponseContainer->getOriginalResponse()
        );
    }
}
