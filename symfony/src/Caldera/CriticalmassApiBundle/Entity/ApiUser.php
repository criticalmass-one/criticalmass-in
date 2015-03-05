<?php

namespace Caldera\CriticalmassApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="apiuser")
 * @ORM\Entity
 */
class ApiUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $token;

    /**
     * @ORM\ManyToOne(targetEntity="Caldera\CriticalmassApiBundle\Entity\App", inversedBy="api_users")
     * @ORM\JoinColumn(name="app_id", referencedColumnName="id")
     */
    protected $app;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $creationDateTime;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = 0;
    
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
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
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
     * Get creationDateTime
     *
     * @return \DateTime 
     */
    public function getCreationDateTime()
    {
        return $this->creationDateTime;
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

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
}
