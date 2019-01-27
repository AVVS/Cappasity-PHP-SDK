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

namespace CappasitySDK\Client\Model\Callback\Process;

use CappasitySDK\Client\Model\Response;

class JobsPushResultPost implements Response\DataInterface
{
    /**
     * @var JobsPushResultPost\Meta
     */
    private $meta;

    /**
     * @var JobsPushResultPost\SyncDataItem[]|mixed
     */
    private $data;

    /**
     * @param JobsPushResultPost\Meta $meta
     * @param JobsPushResultPost\SyncDataItem[]|mixed $data
     */
    public function __construct(JobsPushResultPost\Meta $meta, $data)
    {
        $this->meta = $meta;
        $this->data = $data;
    }

    /**
     * @return JobsPushResultPost\Meta
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param JobsPushResultPost\Meta $meta
     * @return $this
     */
    public function setMeta(JobsPushResultPost\Meta $meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return JobsPushResultPost\SyncDataItem[]|mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param JobsPushResultPost\SyncDataItem[]|mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array $body
     * @return JobsPushResultPost
     */
    public static function fromCallbackBody(array $body)
    {
        $meta = new JobsPushResultPost\Meta($body['meta']['jobId']);

        if ($body['meta']['jobType'] !== 'sync') {
            throw new \LogicException('Unhandled job type result to parse');
        }

        $data = array_map(function (array $item) {
            return new JobsPushResultPost\SyncDataItem(
                $item['id'],
                $item['uploadId'],
                array_key_exists('sku', $item) ? $item['sku'] : null,
                array_key_exists('capp', $item) ? $item['capp'] : null
            );
        }, $body['data']);

        return new self($meta, $data);
    }
}
