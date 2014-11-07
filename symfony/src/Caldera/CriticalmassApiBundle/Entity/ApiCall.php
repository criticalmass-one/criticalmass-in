<?php

namespace Caldera\CriticalmassApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="appcall")
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
     * @ORM\ManyToOne(targetEntity="Caldera\CriticalmassCoreBundle\Entity\App", inversedBy="api_calls")
     * @ORM\JoinColumn(name="app_id", referencedColumnName="id")
     */
    protected $app;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $referer;

    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $call;

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
     * Set call
     *
     * @param string $call
     * @return ApiCall
     */
    public function setCall($call)
    {
        $this->call = $call;

        return $this;
    }

    /**
     * Get call
     *
     * @return string 
     */
    public function getCall()
    {
        return $this->call;
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
     * @param \Caldera\CriticalmassCoreBundle\Entity\App $app
     * @return ApiCall
     */
    public function setApp(\Caldera\CriticalmassCoreBundle\Entity\App $app = null)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Get app
     *
     * @return \Caldera\CriticalmassCoreBundle\Entity\App 
     */
    public function getApp()
    {
        return $this->app;
    }
}
