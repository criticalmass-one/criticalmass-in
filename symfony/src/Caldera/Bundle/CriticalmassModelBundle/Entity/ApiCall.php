<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="apicall")
 * @ORM\Entity
 */
class ApiCall
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Caldera\Bundle\CriticalmassModelBundle\Entity\App", inversedBy="api_calls")
     * @ORM\JoinColumn(name="app_id", referencedColumnName="id")
     */
    protected $app;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $referer;

    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $request;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateTime;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set referer
     *
     * @param string $referer
     * @return ApiCall
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;

        return $this;
    }

    /**
     * Get referer
     *
     * @return string 
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * Set request
     *
     * @param string $request
     * @return ApiCall
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get request
     *
     * @return string 
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     * @return ApiCall
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime 
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set app
     *
     * @param App $app
     * @return ApiCall
     */
    public function setApp(App $app = null)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Get app
     *
     * @return App
     */
    public function getApp()
    {
        return $this->app;
    }
}
