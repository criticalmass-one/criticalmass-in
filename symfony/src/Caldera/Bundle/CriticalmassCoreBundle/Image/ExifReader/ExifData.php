<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Image\ExifReader;

class ExifData
{
    /**
     * @var \DateTime $photoDateTime
     */
    protected $photoDateTime;

    /**
     * @var float $shutterSpeed
     */
    protected $shutterSpeed;

    /**
     * @var float $focalLength
     */
    protected $focalLength;

    /**
     * @var float $focus
     */
    protected $focus;

    /**
     * @var float $aperture
     */
    protected $aperture;

    /**
     * @var integer $iso
     */
    protected $iso;

    /**
     * @var string $model
     */
    protected $model;

    /**
     * @var string $lens
     */
    protected $lens;

    /**
     * @var boolean $flash
     */
    protected $flash;

    /**
     * @return \DateTime
     */
    public function getPhotoDateTime()
    {
        return $this->photoDateTime;
    }

    /**
     * @param \DateTime $photoDateTime
     */
    public function setPhotoDateTime($photoDateTime)
    {
        $this->photoDateTime = $photoDateTime;

        return $this;
    }

    /**
     * @return float
     */
    public function getShutterSpeed()
    {
        return $this->shutterSpeed;
    }

    /**
     * @param float $shutterSpeed
     */
    public function setShutterSpeed($shutterSpeed)
    {
        $this->shutterSpeed = $shutterSpeed;

        return $this;
    }

    /**
     * @return float
     */
    public function getFocalLength()
    {
        return $this->focalLength;
    }

    /**
     * @param float $focalLength
     */
    public function setFocalLength($focalLength)
    {
        $this->focalLength = $focalLength;

        return $this;
    }

    /**
     * @return float
     */
    public function getFocus()
    {
        return $this->focus;
    }

    /**
     * @param float $focus
     */
    public function setFocus($focus)
    {
        $this->focus = $focus;

        return $this;
    }

    /**
     * @return float
     */
    public function getAperture()
    {
        return $this->aperture;
    }

    /**
     * @param float $aperture
     */
    public function setAperture($aperture)
    {
        $this->aperture = $aperture;

        return $this;
    }

    /**
     * @return integer
     */
    public function getIso()
    {
        return $this->iso;
    }

    /**
     * @param integer $iso
     */
    public function setIso($iso)
    {
        $this->iso = $iso;

        return $this;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string $model
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return string
     */
    public function getLens()
    {
        return $this->lens;
    }

    /**
     * @param string $lens
     */
    public function setLens($lens)
    {
        $this->lens = $lens;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isFlash()
    {
        return $this->flash;
    }

    /**
     * @param boolean $flash
     */
    public function setFlash($flash)
    {
        $this->flash = $flash;

        return $this;
    }
}