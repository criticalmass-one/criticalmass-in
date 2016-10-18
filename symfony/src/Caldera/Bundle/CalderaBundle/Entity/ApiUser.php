<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="apiuser")
 * @ORM\Entity
 */
class ApiUser
{
    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $token;
    /**
     * @ORM\ManyToOne(targetEntity="Caldera\Bundle\CalderaBundle\Entity\App", inversedBy="api_users")
     * @ORM\JoinColumn(name="app_id", referencedColumnName="id", nullable=false)
     */
    protected $app;
    /**
     * @ORM\ManyToOne(targetEntity="Caldera\Bundle\CalderaBundle\Entity\City", inversedBy="api_users")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=false)
     */
    protected $city;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $creationDateTime;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = 0;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function __construct()
    {
        $this->setToken(md5(microtime()));
    }

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
     * Get city
     *
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set city
     *
     * @param City
     * @return ApiUser
     */
    public function setCity(City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return App
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get creationDateTime
     *
     * @return \DateTime
     */
    public function getCreationDateTime()
    {
        return $this->creationDateTime;
    }

    /**
     * Set creationDateTime
     *
     * @param \DateTime $creationDateTime
     * @return App
     */
    public function setCreationDateTime($creationDateTime)
    {
        $this->creationDateTime = $creationDateTime;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return App
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }
}
