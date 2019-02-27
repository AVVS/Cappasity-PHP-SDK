<?php

namespace CappasitySDK\Client\Model\Response\Files\InfoGet\Data\Attributes\Embed;

class Params
{
    /**
     * @var Param
     */
    private $autoRun;

    /**
     * @var Param
     */
    private $closeButton;

    /**
     * @var Param
     */
    private $logo;

    /**
     * @var Param
     */
    private $autorotate;

    /**
     * @var Param
     */
    private $autorotateTime;

    /**
     * @var Param
     */
    private $autorotateDelay;

    /**
     * @var Param
     */
    private $autorotateDir;

    /**
     * @var Param
     */
    private $hideFullScreen;

    /**
     * @var Param
     */
    private $hideAutorotateOpt;

    /**
     * @var Param
     */
    private $hideSettingsBtn;

    /**
     * @var Param
     */
    private $enableImageZoom;

    /**
     * @var Param
     */
    private $zoomQuality;

    /**
     * @var Param
     */
    private $hideZoomOpt;

    /**
     * @var Param
     */
    private $width;

    /**
     * @var Param
     */
    private $height;

    /**
     * @return Param
     */
    public function getAutoRun()
    {
        return $this->autoRun;
    }

    /**
     * @param Param $autoRun
     * @return $this
     */
    public function setAutorun($autoRun)
    {
        $this->autoRun = $autoRun;

        return $this;
    }

    /**
     * @return Param
     */
    public function getCloseButton()
    {
        return $this->closeButton;
    }

    /**
     * @param Param $closeButton
     * @return $this
     */
    public function setCloseButton($closeButton)
    {
        $this->closeButton = $closeButton;

        return $this;
    }

    /**
     * @return Param
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param Param $logo
     * @return $this
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Param
     */
    public function getAutorotate()
    {
        return $this->autorotate;
    }

    /**
     * @param Param $autorotate
     * @return $this
     */
    public function setAutorotate($autorotate)
    {
        $this->autorotate = $autorotate;

        return $this;
    }

    /**
     * @return Param
     */
    public function getAutorotateTime()
    {
        return $this->autorotateTime;
    }

    /**
     * @param Param $autorotateTime
     * @return $this
     */
    public function setAutorotateTime($autorotateTime)
    {
        $this->autorotateTime = $autorotateTime;

        return $this;
    }

    /**
     * @return Param
     */
    public function getAutorotateDelay()
    {
        return $this->autorotateDelay;
    }

    /**
     * @param Param $autorotateDelay
     * @return $this
     */
    public function setAutorotateDelay($autorotateDelay)
    {
        $this->autorotateDelay = $autorotateDelay;

        return $this;
    }

    /**
     * @return Param
     */
    public function getAutorotateDir()
    {
        return $this->autorotateDir;
    }

    /**
     * @param Param $autorotateDir
     * @return $this
     */
    public function setAutorotateDir($autorotateDir)
    {
        $this->autorotateDir = $autorotateDir;

        return $this;
    }

    /**
     * @return Param
     */
    public function getHideFullScreen()
    {
        return $this->hideFullScreen;
    }

    /**
     * @param Param $hideFullScreen
     * @return $this
     */
    public function setHideFullScreen($hideFullScreen)
    {
        $this->hideFullScreen = $hideFullScreen;

        return $this;
    }

    /**
     * @return Param
     */
    public function getHideAutorotateOpt()
    {
        return $this->hideAutorotateOpt;
    }

    /**
     * @param Param $hideAutorotateOpt
     * @return $this
     */
    public function setHideAutorotateOpt($hideAutorotateOpt)
    {
        $this->hideAutorotateOpt = $hideAutorotateOpt;

        return $this;
    }

    /**
     * @return Param
     */
    public function getHideSettingsBtn()
    {
        return $this->hideSettingsBtn;
    }

    /**
     * @param Param $hideSettingsBtn
     * @return $this
     */
    public function setHideSettingsBtn($hideSettingsBtn)
    {
        $this->hideSettingsBtn = $hideSettingsBtn;

        return $this;
    }

    /**
     * @return Param
     */
    public function getEnableImageZoom()
    {
        return $this->enableImageZoom;
    }

    /**
     * @param Param $enableImageZoom
     * @return $this
     */
    public function setEnableImageZoom($enableImageZoom)
    {
        $this->enableImageZoom = $enableImageZoom;

        return $this;
    }

    /**
     * @return Param
     */
    public function getZoomQuality()
    {
        return $this->zoomQuality;
    }

    /**
     * @param Param $zoomQuality
     * @return $this
     */
    public function setZoomQuality($zoomQuality)
    {
        $this->zoomQuality = $zoomQuality;

        return $this;
    }

    /**
     * @return Param
     */
    public function getHideZoomOpt()
    {
        return $this->hideZoomOpt;
    }

    /**
     * @param Param $hideZoomOpt
     * @return $this
     */
    public function setHideZoomOpt($hideZoomOpt)
    {
        $this->hideZoomOpt = $hideZoomOpt;

        return $this;
    }

    /**
     * @return Param
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param Param $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return Param
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param Param $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }
}
