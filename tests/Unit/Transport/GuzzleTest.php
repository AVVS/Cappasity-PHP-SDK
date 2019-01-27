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

namespace CappasitySDK\Tests\Unit\Transport;

class GuzzleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GuzzleHttp\Client|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpClientMock;

    /**
     * @var array
     */
    private $config;

    public function setUp()
    {
        $this->httpClientMock = $this->getMockBuilder(\GuzzleHttp\Client::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'createRequest',
                'send',
            ])
            ->getMock();

        $this->config = [];
    }

    public function testMakeRequest()
    {
        $mockedRequest = $this->getMockBuilder(\GuzzleHttp\Message\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockedResponseBody = \GuzzleHttp\Stream\Stream::factory(json_encode(['data' => 'foobar']));
        $mockedResponse = new \GuzzleHttp\Message\Response(200, [], $mockedResponseBody);

        $this->httpClientMock
            ->expects($this->once())
            ->method('createRequest')
            ->with(
                'GET',
                'http://aaa.com',
                [
                    'query' => ['a' => 'b'],
                    'json' => ['c' => 'd'],
                    'headers' => ['e' => 'f'],
                    'timeout' => 5,
                ]
            )
            ->willReturn($mockedRequest);
        $this->httpClientMock
            ->expects($this->once())
            ->method('send')
            ->with($mockedRequest)
            ->willReturn($mockedResponse);

        $guzzleTransport = new \CappasitySDK\Transport\Guzzle($this->httpClientMock, $this->config);
        $response = $guzzleTransport->makeRequest('GET', 'http://aaa.com', [
            'query' => ['a' => 'b'],
            'data' => ['c' => 'd'],
            'headers' => ['e' => 'f'],
            'timeout' => 5,
        ]);

        $this->assertInstanceOf(\CappasitySDK\Transport\ResponseContainer::class,  $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([], $response->getHeaders());
        $this->assertEquals($mockedResponse, $response->getOriginalResponse());
        $this->assertEquals(['data' => 'foobar'], $response->getData());
    }

    /**
     * @expectedException \CappasitySDK\Transport\Exception\RequestException
     * @expectedExceptionMessage Server responded with an error [404: Not Found]: job data missing
     */
    public function testThrowExceptionOnProcessErrorResponse()
    {
        $mockedRequest = $this->getMockBuilder(\GuzzleHttp\Message\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockedResponseBody = \GuzzleHttp\Stream\Stream::factory(json_encode([
            'statusCode' => 404,
            'error' => 'Not Found',
            'message' => 'job data missing',
            'name' => 'HttpStatusError'
        ]));
        $mockedResponse = new \GuzzleHttp\Message\Response(404, [], $mockedResponseBody);

        $this->httpClientMock
            ->expects($this->once())
            ->method('createRequest')
            ->with(
                'GET',
                'http://aaa.com',
                [
                    'query' => ['a' => 'b'],
                    'json' => ['c' => 'd'],
                    'headers' => ['e' => 'f'],
                    'timeout' => 5,
                ]
            )
            ->willReturn($mockedRequest);
        $this->httpClientMock
            ->expects($this->once())
            ->method('send')
            ->with($mockedRequest)
            ->willReturn($mockedResponse);

        $guzzleTransport = new \CappasitySDK\Transport\Guzzle($this->httpClientMock, $this->config);
        $guzzleTransport->makeRequest('GET', 'http://aaa.com', [
            'query' => ['a' => 'b'],
            'data' => ['c' => 'd'],
            'headers' => ['e' => 'f'],
            'timeout' => 5,
        ]);
    }

    /**
     * @expectedException \CappasitySDK\Transport\Exception\RequestException
     * @expectedExceptionMessage Server responded with an error [404]: could not find associated data (detail: none)
     */
    public function testThrowExceptionOnFilesErrorResponse()
    {
        $mockedRequest = $this->getMockBuilder(\GuzzleHttp\Message\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockedResponseBody = \GuzzleHttp\Stream\Stream::factory(json_encode([
            'meta' => [
                'id' => 'd715ee2b-aea5-4f78-94ee-c7ec3bfaad40'
            ],
            'errors' => [
                [
                    'status' => 'HttpStatusError',
                    'code' => 404,
                    'title' => 'could not find associated data',
                    'detail' => [],
                ],
            ],
        ]));
        $mockedResponse = new \GuzzleHttp\Message\Response(404, [], $mockedResponseBody);

        $this->httpClientMock
            ->expects($this->once())
            ->method('createRequest')
            ->with(
                'GET',
                'http://aaa.com',
                [
                    'query' => ['a' => 'b'],
                    'json' => ['c' => 'd'],
                    'headers' => ['e' => 'f'],
                    'timeout' => 5,
                ]
            )
            ->willReturn($mockedRequest);
        $this->httpClientMock
            ->expects($this->once())
            ->method('send')
            ->with($mockedRequest)
            ->willReturn($mockedResponse);

        $guzzleTransport = new \CappasitySDK\Transport\Guzzle($this->httpClientMock, $this->config);
        $guzzleTransport->makeRequest('GET', 'http://aaa.com', [
            'query' => ['a' => 'b'],
            'data' => ['c' => 'd'],
            'headers' => ['e' => 'f'],
            'timeout' => 5,
        ]);
    }
}
