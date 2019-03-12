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

namespace CappasitySDK\Client\Model\Response\Files;

use CappasitySDK\Client\Model\Response;

class InfoGet implements Response\DataInterface
{
    /**
     * @var InfoGet\Meta
     */
    private $meta;

    /**
     * @var InfoGet\Data
     */
    private $data;

    /**
     * @param InfoGet\Meta $meta
     * @param InfoGet\Data $data
     */
    public function __construct(InfoGet\Meta $meta, InfoGet\Data $data)
    {
        $this->meta = $meta;
        $this->data = $data;
    }

    /**
     * @return InfoGet\Meta
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param InfoGet\Meta $meta
     * @return $this
     */
    public function setMeta(InfoGet\Meta $meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return InfoGet\Data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param InfoGet\Data $data
     * @return $this
     */
    public function setData(InfoGet\Data $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array $response
     * @return InfoGet
     */
    public static function fromResponse(array $response)
    {
        $attributesData = $response['data']['attributes'];
        $linksData = $response['data']['links'];

        $embedParamsData = $attributesData['embed']['params'];
        $normalizedKeys = array_map(
            function ($key) {
                $keysToTransform = [
                    'c_ver' => 'cVer',
                    'autorun' => 'autoRun',
                    'closebutton' => 'closeButton',
                    'autorotate' => 'autorotate',
                    'autorotatetime' => 'autorotateTime',
                    'autorotatedelay' => 'autorotateDelay',
                    'autorotatedir' => 'autorotateDir',
                    'hidefullscreen' => 'hideFullScreen',
                    'hideautorotateopt' => 'hideAutorotateOpt',
                    'hidesettingsbtn' => 'hideSettingsBtn',
                    'enableimagezoom' => 'enableImageZoom',
                    'zoomquality' => 'zoomQuality',
                    'hidezoomopt' => 'hideZoomOpt',
                ];

                return array_key_exists($key, $keysToTransform) ? $keysToTransform[$key] : $key;
            },
            array_keys($embedParamsData)
        );

        $normalizedEmbedParamsData = array_combine($normalizedKeys, $embedParamsData);

        $embedParams = new InfoGet\Data\Attributes\Embed\Params();
        foreach ($normalizedEmbedParamsData as $paramTitle => $paramData) {
            $value = (new InfoGet\Data\Attributes\Embed\Param())
                ->setType($paramData['type'])
                ->setDefault($paramData['default'])
                ->setDescription($paramData['description'])
                ->setEnum($paramData['enum'])
                ->setMin($paramData['min'])
                ->setMax($paramData['max'])
                ->setPaid($paramData['paid'])
                ->setReqPlanLevel($paramData['reqPlanLevel'])
                ->setInvert($paramData['invert'])
                ->setOwn($paramData['own']);

            $capitalizedParamTitle = ucfirst($paramTitle);
            $setter = "set{$capitalizedParamTitle}";
            $embedParams->{$setter}($value);
        }

        $embed = (new InfoGet\Data\Attributes\Embed())
            ->setCode($attributesData['embed']['code'])
            ->setParams($embedParams);

        $files = array_map(
            function (array $file) {
                return (new InfoGet\Data\Attributes\File())
                    ->setType($file['type'])
                    ->setFilename($file['filename'])
                    ->setContentLength($file['contentLength'])
                    ->setContentType($file['contentType'])
                    ->setBucket($file['bucket'])
                    ->setMd5Hash($file['md5Hash']);
            },
            $attributesData['files']
        );

        $attributes = (new InfoGet\Data\Attributes())
            ->setAlias($attributesData['alias'])
            ->setName($attributesData['name'])
            ->setBackgroundColor($attributesData['backgroundColor'])
            ->setBucket($attributesData['bucket'])
            ->setContentLength($attributesData['contentLength'])
            ->setCVer($attributesData['c_ver'])
            ->setOwner($attributesData['owner'])
            ->setPacked($attributesData['packed'])
            ->setParts($attributesData['parts'])
            ->setPreview($attributesData['preview'])
            ->setPublic($attributesData['public'])
            ->setSimple($attributesData['simple'])
            ->setStartedAt($attributesData['startedAt'])
            ->setStatus($attributesData['status'])
            ->setType($attributesData['type'])
            ->setUploaded($attributesData['uploaded'])
            ->setUploadedAt($attributesData['uploadedAt'])
            ->setUploadId($attributesData['uploadId'])
            ->setUploadType($attributesData['uploadType'])
            ->setEmbed($embed)
            ->setFiles($files)
        ;

        $links = (new InfoGet\Data\Links())
            ->setSelf($linksData['self'])
            ->setOwner($linksData['owner'])
            ->setPlayer($linksData['player'])
            ->setUser($linksData['user']);

        return new self(
            new InfoGet\Meta($response['meta']['id']),
            new InfoGet\Data(
                $response['data']['id'],
                $response['data']['type'],
                $attributes,
                $links
            )
        );
    }
}
