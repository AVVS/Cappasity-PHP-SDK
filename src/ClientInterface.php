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

interface ClientInterface
{
    /**
     * @param Request\Process\JobsRegisterSyncPost $params
     * @return Response\Container
     */
    public function registerSyncJob(Request\Process\JobsRegisterSyncPost $params);

    /**
     * @param Request\Process\JobsPullListGet $params
     * @return Response\Container
     */
    public function getPullJobList(Request\Process\JobsPullListGet $params);

    /**
     * @param Request\Process\JobsPullAckPost $params
     * @return Response\Container
     */
    public function ackPullJobList(Request\Process\JobsPullAckPost $params);

    /**
     * @param Request\Process\JobsPullResultGet $params
     * @return Response\Container
     */
    public function getPullJobResult(Request\Process\JobsPullResultGet $params);
}
