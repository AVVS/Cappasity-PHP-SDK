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

        $this->transportMock = $this->getMockBuilder(\CappasitySDK\Transport\Guzzle6::class)
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

    public function testGetUsersMe()
    {
        $client = $this->makeClient();
        $requestParams = new Client\Model\Request\Users\MeGet();

        $mockedResponseData = [
            'meta' => [
                'id' => '1e81be6f-2b8e-447d-813e-213b8e55069b',
            ],
            'data' => [
                'type' => 'user',
                'id' => '6397132946607702016',
                'attributes' => [
                    'firstName' => 'Alice',
                    'lastName' => 'Davis',
                    'org' => false,
                    'id' => '6397132946607702016',
                    'username' => 'alice@gmail.com',
                    'created' => 1525195347454,
                    'alias' => 'alice',
                    'plan' => 'free',
                    'agreement' => 'free',
                    'nextCycle' => 1551460973548,
                    'models' => 20,
                    'modelPrice' => 10,
                    'subscriptionPrice' => '0',
                    'subscriptionInterval' => 'month',
                    'mfa' => false,
                ],
                'links' => [
                    'self' => 'https://api.cappasity.com/api/users/6397132946607702016',
                    'user' => 'https://3d.cappasity.com/u/alice',
                ],
            ],
        ];
        $mockedTransportResponse = $this->makeTransportResponseContainer(200, $mockedResponseData);
        $mockedClientResponse = $this->makeClientResponseContainer(
            $mockedTransportResponse,
            Client\Model\Response\Users\MeGet::class
        );
        $this->expectResponseTransformed(
            [$mockedTransportResponse, Client\Model\Response\Users\MeGet::class],
            $mockedClientResponse
        );

        $this->expectValidationPerformed(
            $requestParams,
            Client\Validator\Type\Request\Users\MeGet::class
        );

        $this->expectRequestMade(
            [
                'GET',
                'https://api.cappasity.com/api/users/me',
                [
                    'headers' => [
                        'authorization' => "Bearer {$this->apiToken}",
                    ],
                    'timeout' => 5
                ]
            ],
            $mockedTransportResponse
        );

        $actualResponse = $client->getUser($requestParams);
        $this->assertInstanceOf(Client\Model\Response\Container::class, $actualResponse);
        $this->assertInstanceOf(Client\Model\Response\Users\MeGet::class, $actualResponse->getBodyData());
        /** @var Client\Model\Response\Users\MeGet $actualResponseData */
        $actualResponseData = $actualResponse->getBodyData();

        $this->assertEquals('1e81be6f-2b8e-447d-813e-213b8e55069b', $actualResponseData->getMeta()->getId());
        $this->assertEquals('6397132946607702016', $actualResponseData->getData()->getId());
        $this->assertEquals('user', $actualResponseData->getData()->getType());
        $attributes = $actualResponseData->getData()->getAttributes();
        $this->assertEquals('Alice', $attributes->getFirstName());
        $this->assertEquals('Davis', $attributes->getLastName());
        $this->assertEquals(false, $attributes->getOrg());
        $this->assertEquals('6397132946607702016', $attributes->getId());
        $this->assertEquals('alice@gmail.com', $attributes->getUsername());
        $this->assertEquals(1525195347454, $attributes->getCreated());
        $this->assertEquals('alice', $attributes->getAlias());
        $this->assertEquals('free', $attributes->getPlan());
        $this->assertEquals('free', $attributes->getAgreement());
        $this->assertEquals(1551460973548, $attributes->getNextCycle());
        $this->assertEquals(20, $attributes->getModels());
        $this->assertEquals(10, $attributes->getModelsPrice());
        $this->assertEquals('0', $attributes->getSubscriptionPrice());
        $this->assertEquals('month', $attributes->getSubscriptionInterval());
        $this->assertEquals(false, $attributes->getMfa());
        $this->assertEquals(
            'https://api.cappasity.com/api/users/6397132946607702016',
            $actualResponseData->getData()->getLinks()->getSelf()
        );
        $this->assertEquals(
            'https://3d.cappasity.com/u/alice',
            $actualResponseData->getData()->getLinks()->getUser()
        );
    }

    public function testGetFilesInfo()
    {
        $client = $this->makeClient();
        $requestParams = Client\Model\Request\Files\InfoGet::fromData(
            'alice',
            'dd596de4-ae2b-4d66-a023-242ca7d86b51'
        );

        $mockedResponseData = [
            'meta' => [
                'id' => '0b29e0dd-0e93-4acb-ab64-dc1a9ca20f03',
            ],
            'data' => [
                'type' => 'file',
                'id' => 'dd596de4-ae2b-4d66-a023-242ca7d86b51',
                'attributes' => [
                    'alias' => 'pinkclutch',
                    'backgroundColor' => '#FFFFFF',
                    'backgroundImage' => '',
                    'bucket' => 'cdn.cappasity.com',
                    'c_ver' => '4.1.0',
                    'contentLength' => 10874630,
                    'files' => [
                        [
                            'contentLength' => 30621,
                            'contentType' => 'image/jpeg',
                            'md5Hash' => 's4gKmXq1WC7ItJAf4ERhqA==',
                            'bucket' => 'cdn.cappasity.com',
                            'type' => 'c-preview',
                            'filename' => '4b528066036d2c07e6b9b53784509913/dd596de4-ae2b-4d66-a023-242ca7d86b51/7d001be0-4506-41d3-8bdb-66fd6fecae65.jpeg',
                        ],
                        [
                            'contentLength' => 21356,
                            'contentType' => 'image/vnd.cappasity',
                            'md5Hash' => 'RttYE61A6QvrVia5RIN3Kg==',
                            'bucket' => 'cdn.cappasity.com',
                            'type' => 'c-pack',
                            'filename' => '4b528066036d2c07e6b9b53784509913/dd596de4-ae2b-4d66-a023-242ca7d86b51/88893d10-ddfb-466e-a448-ea3874c169bb.pack',
                        ],
                        [
                            'contentLength' => 2790885,
                            'contentType' => 'image/vnd.cappasity',
                            'md5Hash' => 'SasLRjsWnvOTa2+Gr8CKTg==',
                            'bucket' => 'cdn.cappasity.com',
                            'type' => 'c-pack',
                            'filename' => '4b528066036d2c07e6b9b53784509913/dd596de4-ae2b-4d66-a023-242ca7d86b51/7294c37c-99dc-4e7f-aea5-bd7309035eb4.pack',
                        ],
                        [
                            'contentLength' => 8031768,
                            'contentType' => 'image/vnd.cappasity',
                            'md5Hash' => 'IADnsAfMkekgtMbuqKGMdw==',
                            'bucket' => 'cdn.cappasity.com',
                            'type' => 'c-pack',
                            'filename' => '4b528066036d2c07e6b9b53784509913/dd596de4-ae2b-4d66-a023-242ca7d86b51/f989e877-63e4-4307-9b39-f76837b5407d.pack',
                        ],
                    ],
                    'name' => 'Goddess Pink Clutch',
                    'owner' => 'alice',
                    'packed' => '1',
                    'parts' => 4,
                    'preview' => '4b528066036d2c07e6b9b53784509913/dd596de4-ae2b-4d66-a023-242ca7d86b51/7d001be0-4506-41d3-8bdb-66fd6fecae65.jpeg',
                    'public' => '1',
                    'simple' => '4b528066036d2c07e6b9b53784509913/dd596de4-ae2b-4d66-a023-242ca7d86b51/f989e877-63e4-4307-9b39-f76837b5407d.pack',
                    'startedAt' => 1525282009671,
                    'status' => 'processed',
                    'type' => 'object',
                    'uploadId' => 'dd596de4-ae2b-4d66-a023-242ca7d86b51',
                    'uploadType' => 'simple',
                    'uploaded' => 4,
                    'uploadedAt' => 1525282016275,
                    'embed' => [
                        'code' => '<iframe allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" width="{{ width }}" height="{{ height }}" frameborder="0" style="border:0;" src="https://api.cappasity.com/api/player/dd596de4-ae2b-4d66-a023-242ca7d86b51/embedded?autorun={{ autorun }}&closebutton={{ closebutton }}&logo={{ logo }}&autorotate={{ autorotate }}&autorotatetime={{ autorotatetime }}&autorotatedelay={{ autorotatedelay }}&autorotatedir={{ autorotatedir }}&hidefullscreen={{ hidefullscreen }}&hideautorotateopt={{ hideautorotateopt }}&hidesettingsbtn={{ hidesettingsbtn }}&enableimagezoom={{ enableimagezoom }}&zoomquality={{ zoomquality }}&hidezoomopt={{ hidezoomopt }}"></iframe>',
                        'params' => [
                            'autorun' => [
                                'type' => 'boolean',
                                'default' => 0,
                                'description' => 'Auto-start player',
                            ],
                            'closebutton' => [
                                'type' => 'boolean',
                                'default' => 1,
                                'description' => 'Close button',
                            ],
                            'logo' => [
                                'type' => 'boolean',
                                'own' => 0,
                                'default' => 1,
                                'description' => 'Show logo',
                                'paid' => true,
                                'reqPlanLevel' => 20,
                            ],
                            'autorotate' => [
                                'type' => 'boolean',
                                'default' => 0,
                                'description' => 'Autorotate',
                                'paid' => true,
                                'reqPlanLevel' => 30,
                            ],
                            'autorotatetime' => [
                                'type' => 'float',
                                'default' => 10,
                                'description' => 'Autorotate time, seconds',
                                'min' => 2,
                                'max' => 60,
                                'paid' => true,
                                'reqPlanLevel' => 30,
                            ],
                            'autorotatedelay' => [
                                'type' => 'float',
                                'default' => 2,
                                'description' => 'Autorotate delay, seconds',
                                'min' => 1,
                                'max' => 10,
                                'paid' => true,
                                'reqPlanLevel' => 30,
                            ],
                            'autorotatedir' => [
                                'type' => 'integer',
                                'default' => 1,
                                'description' => 'Autorotate direction',
                                'enum' => [
                                    'clockwise' => 1,
                                    'counter-clockwise' => -1,
                                ],
                                'paid' => true,
                                'reqPlanLevel' => 30,
                            ],
                            'hidefullscreen' => [
                                'type' => 'boolean',
                                'description' => 'Hide fullscreen',
                                'default' => 1,
                            ],
                            'hideautorotateopt' => [
                                'type' => 'boolean',
                                'own' => 0,
                                'default' => 1,
                                'invert' => true,
                                'description' => 'Autorotate button',
                                'paid' => true,
                                'reqPlanLevel' => 30,
                            ],
                            'hidesettingsbtn' => [
                                'type' => 'boolean',
                                'default' => 0,
                                'description' => 'Settings button',
                                'invert' => true,
                                'paid' => true,
                                'reqPlanLevel' => 30,
                            ],
                            'enableimagezoom' => [
                                'type' => 'boolean',
                                'default' => 1,
                                'description' => 'Enable zoom',
                                'paid' => true,
                                'reqPlanLevel' => 30,
                            ],
                            'zoomquality' => [
                                'type' => 'integer',
                                'default' => 1,
                                'enum' => [
                                    'SD' => 1,
                                    'HD' => 2,
                                ],
                                'description' => 'Zoom quality',
                                'paid' => true,
                                'reqPlanLevel' => 30,
                            ],
                            'hidezoomopt' => [
                                'type' => 'boolean',
                                'default' => 0,
                                'description' => 'Zoom button',
                                'paid' => true,
                                'invert' => true,
                                'reqPlanLevel' => 30,
                            ],
                            'width' => [
                                'type' => 'string',
                                'default' => '100%',
                                'description' => 'Width of embedded window (px or %)',
                            ],
                            'height' => [
                                'type' => 'string',
                                'default' => '600px',
                                'description' => 'Height of embedded window (px or %)',
                            ],
                        ],
                    ],
                ],
                'links' => [
                    'self' => 'https://api.cappasity.com/api/files/dd596de4-ae2b-4d66-a023-242ca7d86b51',
                    'owner' => 'https://api.cappasity.com/api/users/alice',
                    'player' => 'https://3d.cappasity.com/u/alice/dd596de4-ae2b-4d66-a023-242ca7d86b51',
                    'user' => 'https://3d.cappasity.com/u/alice',
                ],
            ],
        ];
        $mockedTransportResponse = $this->makeTransportResponseContainer(200, $mockedResponseData);
        $mockedClientResponse = $this->makeClientResponseContainer(
            $mockedTransportResponse,
            Client\Model\Response\Files\InfoGet::class
        );
        $this->expectResponseTransformed(
            [$mockedTransportResponse, Client\Model\Response\Files\InfoGet::class],
            $mockedClientResponse
        );

        $this->expectValidationPerformed(
            $requestParams,
            Client\Validator\Type\Request\Files\InfoGet::class
        );

        $this->expectRequestMade(
            [
                'GET',
                'https://api.cappasity.com/api/files/info/alice/dd596de4-ae2b-4d66-a023-242ca7d86b51',
                [
                    'headers' => [
                        'authorization' => "Bearer {$this->apiToken}",
                    ],
                    'timeout' => 5
                ]
            ],
            $mockedTransportResponse
        );

        $actualResponse = $client->getViewInfo($requestParams);
        $this->assertInstanceOf(Client\Model\Response\Container::class, $actualResponse);
        $this->assertInstanceOf(Client\Model\Response\Files\InfoGet::class, $actualResponse->getBodyData());
        /** @var Client\Model\Response\Files\InfoGet $actualResponseData */
        $actualResponseData = $actualResponse->getBodyData();

        $this->assertEquals('0b29e0dd-0e93-4acb-ab64-dc1a9ca20f03', $actualResponseData->getMeta()->getId());
        $this->assertEquals('dd596de4-ae2b-4d66-a023-242ca7d86b51', $actualResponseData->getData()->getId());
        $this->assertEquals('file', $actualResponseData->getData()->getType());
        $attributes = $actualResponseData->getData()->getAttributes();
        $this->assertInstanceOf(Client\Model\Response\Files\InfoGet\Data\Attributes::class, $attributes);

        $this->assertEquals('pinkclutch', $attributes->getAlias());
        $this->assertEquals('#FFFFFF', $attributes->getBackgroundColor());
        $this->assertEquals('', $attributes->getBackgroundImage());
        $this->assertEquals('cdn.cappasity.com', $attributes->getBucket());
        $this->assertEquals('4.1.0', $attributes->getCVer());
        $this->assertEquals(10874630, $attributes->getContentLength());
        $this->assertEquals('Goddess Pink Clutch', $attributes->getName());
        $this->assertEquals('alice', $attributes->getOwner());
        $this->assertEquals('1', $attributes->getPacked());
        $this->assertEquals(4, $attributes->getParts());
        $this->assertEquals(
            '4b528066036d2c07e6b9b53784509913/dd596de4-ae2b-4d66-a023-242ca7d86b51/7d001be0-4506-41d3-8bdb-66fd6fecae65.jpeg',
            $attributes->getPreview()
        );
        $this->assertEquals('1', $attributes->getPublic());
        $this->assertEquals(
            '4b528066036d2c07e6b9b53784509913/dd596de4-ae2b-4d66-a023-242ca7d86b51/f989e877-63e4-4307-9b39-f76837b5407d.pack',
            $attributes->getSimple()
        );
        $this->assertEquals(1525282009671, $attributes->getStartedAt());
        $this->assertEquals('processed', $attributes->getStatus());
        $this->assertEquals('object', $attributes->getType());
        $this->assertEquals('dd596de4-ae2b-4d66-a023-242ca7d86b51', $attributes->getUploadId());
        $this->assertEquals('simple', $attributes->getUploadType());
        $this->assertEquals(4, $attributes->getUploaded());
        $this->assertEquals(1525282016275, $attributes->getUploadedAt());

        $embed = $attributes->getEmbed();
        $this->assertInstanceOf(Client\Model\Response\Files\InfoGet\Data\Attributes\Embed::class, $embed);
        $this->assertEquals(
            '<iframe allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" width="{{ width }}" height="{{ height }}" frameborder="0" style="border:0;" src="https://api.cappasity.com/api/player/dd596de4-ae2b-4d66-a023-242ca7d86b51/embedded?autorun={{ autorun }}&closebutton={{ closebutton }}&logo={{ logo }}&autorotate={{ autorotate }}&autorotatetime={{ autorotatetime }}&autorotatedelay={{ autorotatedelay }}&autorotatedir={{ autorotatedir }}&hidefullscreen={{ hidefullscreen }}&hideautorotateopt={{ hideautorotateopt }}&hidesettingsbtn={{ hidesettingsbtn }}&enableimagezoom={{ enableimagezoom }}&zoomquality={{ zoomquality }}&hidezoomopt={{ hidezoomopt }}"></iframe>',
            $embed->getCode()
        );

        $embedParams = $embed->getParams();
        $this->assertEquals('Auto-start player', $embedParams->getAutoRun()->getDescription());
        $this->assertEquals('Close button', $embedParams->getCloseButton()->getDescription());
        $this->assertEquals('Show logo', $embedParams->getLogo()->getDescription());
        $this->assertEquals('Autorotate', $embedParams->getAutorotate()->getDescription());
        $this->assertEquals('Autorotate time, seconds', $embedParams->getAutorotateTime()->getDescription());
        $this->assertEquals('Autorotate delay, seconds', $embedParams->getAutorotateDelay()->getDescription());
        $this->assertEquals('Autorotate direction', $embedParams->getAutorotateDir()->getDescription());
        $this->assertEquals('Hide fullscreen', $embedParams->getHideFullScreen()->getDescription());
        $this->assertEquals('Autorotate button', $embedParams->getHideAutorotateOpt()->getDescription());
        $this->assertEquals('Settings button', $embedParams->getHideSettingsBtn()->getDescription());
        $this->assertEquals('Enable zoom', $embedParams->getEnableImageZoom()->getDescription());
        $this->assertEquals('Zoom quality', $embedParams->getZoomQuality()->getDescription());
        $this->assertEquals('Zoom button', $embedParams->getHideZoomOpt()->getDescription());
        $this->assertEquals('Width of embedded window (px or %)', $embedParams->getWidth()->getDescription());
        $this->assertEquals('Height of embedded window (px or %)', $embedParams->getHeight()->getDescription());

        $files = $attributes->getFiles();
        $this->assertCount(4, $files);
        $firstFile = $files[0];
        $this->assertInstanceOf(Client\Model\Response\Files\InfoGet\Data\Attributes\File::class, $firstFile);
        $this->assertEquals(30621, $firstFile->getContentLength());
        $this->assertEquals('image/jpeg', $firstFile->getContentType());
        $this->assertEquals('s4gKmXq1WC7ItJAf4ERhqA==', $firstFile->getMd5Hash());
        $this->assertEquals('cdn.cappasity.com', $firstFile->getBucket());
        $this->assertEquals('c-preview', $firstFile->getType());
        $this->assertEquals(
            '4b528066036d2c07e6b9b53784509913/dd596de4-ae2b-4d66-a023-242ca7d86b51/7d001be0-4506-41d3-8bdb-66fd6fecae65.jpeg',
            $firstFile->getFilename()
        );

        $links = $actualResponseData->getData()->getLinks();

        $this->assertEquals(
            'https://api.cappasity.com/api/files/dd596de4-ae2b-4d66-a023-242ca7d86b51',
            $links->getSelf()
        );
        $this->assertEquals(
            'https://api.cappasity.com/api/users/alice',
            $links->getOwner()
        );
        $this->assertEquals(
            'https://3d.cappasity.com/u/alice/dd596de4-ae2b-4d66-a023-242ca7d86b51',
            $links->getPlayer()
        );
        $this->assertEquals(
            'https://3d.cappasity.com/u/alice',
            $links->getUser()
        );
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
    private function expectResponseTransformed(array $transformArguments, $modelResponseContainer)
    {
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
        $mockedOriginalResponse = new \GuzzleHttp\Psr7\Response($code, $headers, $mockedResponseBody);

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
            throw new \LogicException("Class {$className} missing required `fromResponse` method");
        }

        return new \CappasitySDK\Client\Model\Response\Container(
            200,
            [],
            $className::fromResponse($transportResponseContainer->getData()),
            $transportResponseContainer->getOriginalResponse()
        );
    }
}
