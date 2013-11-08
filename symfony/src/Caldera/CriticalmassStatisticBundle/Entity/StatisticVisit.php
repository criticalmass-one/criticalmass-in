<?php

namespace Caldera\CriticalmassStatisticBundle\Entity;

use Caldera\CriticalmassStatisticBundle\Utility\StatisticEntityWriter\StatisticEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity()
 * @ORM\Table(name="statisticvisit")
 */
class StatisticVisit implements StatisticEntity
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\ManyToOne(targetEntity="\Caldera\CriticalmassCoreBundle\Entity\User", inversedBy="statisticvisits", cascade={"persist", "remove"})
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
   */
  protected $user;

  /**
   * @ORM\ManyToOne(targetEntity="\Caldera\CriticalmassCoreBundle\Entity\City", inversedBy="statisticvisits", cascade={"persist", "remove"})
   * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
   */
  protected $city;

  /**
   * @ORM\Column(type="string", length=15)
   */
  protected $remoteAddr;

  /**
   * @ORM\Column(type="string", length=255)
   */
  protected $remoteHost;

  /**
   * @ORM\Column(type="string", length=255)
   */
  protected $referer;

  /**
   * @ORM\Column(type="string", length=255)
   */
  protected $query;

  /**
   * @ORM\Column(type="string", length=3)
   */
  protected $environment;

  /**
    * @ORM\Column(type="string", length=255)
    */
  protected $host;

  /**
   * @ORM\Column(type="string", length=255)
   */
  protected $agent;

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
     * Set remoteAddr
     *
     * @param string $remoteAddr
     * @return StatisticVisit
     */
    public function setRemoteAddr($remoteAddr)
    {
        $this->remoteAddr = $remoteAddr;
    
        return $this;
    }

    /**
     * Get remoteAddr
     *
     * @return string 
     */
    public function getRemoteAddr()
    {
        return $this->remoteAddr;
    }

    /**
     * Set remoteHost
     *
     * @param string $remoteHost
     * @return StatisticVisit
     */
    public function setRemoteHost($remoteHost)
    {
        $this->remoteHost = $remoteHost;
    
        return $this;
    }

    /**
     * Get remoteHost
     *
     * @return string 
     */
    public function getRemoteHost()
    {
        return $this->remoteHost;
    }

    /**
     * Set referer
     *
     * @param string $referer
     * @return StatisticVisit
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
     * Set query
     *
     * @param string $query
     * @return StatisticVisit
     */
    public function setQuery($query)
    {
        $this->query = $query;
    
        return $this;
    }

    /**
     * Get query
     *
     * @return string 
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set environment
     *
     * @param string $environment
     * @return StatisticVisit
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    
        return $this;
    }

    /**
     * Get environment
     *
     * @return string 
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Set host
     *
     * @param string $host
     * @return StatisticVisit
     */
    public function setHost($host)
    {
        $this->host = $host;
    
        return $this;
    }

    /**
     * Get host
     *
     * @return string 
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set agent
     *
     * @param string $agent
     * @return StatisticVisit
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
    
        return $this;
    }

    /**
     * Get agent
     *
     * @return string 
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     * @return StatisticVisit
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
     * Set user
     *
     * @param \Caldera\CriticalmassStatisticBundle\Entity\User $user
     * @return StatisticVisit
     */
    public function setUser($user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Caldera\CriticalmassStatisticBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set city
     *
     * @param \Caldera\CriticalmassStatisticBundle\Entity\City $city
     * @return StatisticVisit
     */
    public function setCity($city = null)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return \Caldera\CriticalmassStatisticBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }
}