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

use CappasitySDK\Client\Model\Request;
use CappasitySDK\Client\Model\Response;

class ReportableClient implements ClientInterface
{
    const DSN = 'https://6d68c26b33a34f008359c8647f02a110@sentry.io/1282472';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var \Raven_Client
     */
    private $ravenClient;

    /**
     * @param ClientInterface $client
     * @param \Raven_Client $ravenClient
     */
    public function __construct(ClientInterface $client, \Raven_Client $ravenClient)
    {
        $this->client = $client;
        $this->ravenClient = $ravenClient;
    }

    /**
     * @param Request\Process\JobsRegisterSyncPost $params
     * @return Response\Container
     * @throws \Exception
     */
    public function registerSyncJob(Request\Process\JobsRegisterSyncPost $params)
    {
        try {
            return $this->client->registerSyncJob($params);
        } catch (\Exception $e) {
            $this->ravenClient->captureException($e);

            throw $e;
        }
    }

    /**
     * @param Request\Process\JobsPullListGet $params
     * @return Response\Container
     * @throws \Exception
     */
    public function getPullJobList(Request\Process\JobsPullListGet $params)
    {
        try {
            return $this->client->getPullJobList($params);
        } catch (\Exception $e) {
            $this->ravenClient->captureException($e);

            throw $e;
        }
    }

    /**
     * @param Request\Process\JobsPullAckPost $params
     * @return Response\Container
     * @throws \Exception
     */
    public function ackPullJobList(Request\Process\JobsPullAckPost $params)
    {
        try {
            return $this->client->ackPullJobList($params);
        } catch (\Exception $e) {
            $this->ravenClient->captureException($e);

            throw $e;
        }
    }

    /**
     * @param Request\Process\JobsPullResultGet $params
     * @return Response\Container
     * @throws \Exception
     */
    public function getPullJobResult(Request\Process\JobsPullResultGet $params)
    {
        try {
            return $this->client->getPullJobResult($params);
        } catch (\Exception $e) {
            $this->ravenClient->captureException($e);

            throw $e;
        }
    }

    public static function createWithClient(ClientInterface $client)
    {
        $ravenClient = (new \Raven_Client(self::DSN, [
            'timeout' => 2,
        ]));

        return new self($client, $ravenClient);
    }
}
