<?php

namespace Caldera\CriticalmassApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="app")
 * @ORM\Entity
 */
class App
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $token;

    /**
     * @ORM\Column(type="integer")
     */
    protected $apiCalls;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $creationDateTime;

    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(type="text")
     */
    protected $allowedReferer;

    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $url;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $approved;

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
     * Set apiCalls
     *
     * @param integer $apiCalls
     * @return App
     */
    public function setApiCalls($apiCalls)
    {
        $this->apiCalls = $apiCalls;

        return $this;
    }

    /**
     * Get apiCalls
     *
     * @return integer 
     */
    public function getApiCalls()
    {
        return $this->apiCalls;
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
     * Set user
     *
     * @param \Application\Sonata\UserBundle\Entity\User $user
     * @return App
     */
    public function setUser(\Application\Sonata\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return App
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return App
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set allowedReferer
     *
     * @param string $allowedReferer
     * @return App
     */
    public function setAllowedReferer($allowedReferer)
    {
        $this->allowedReferer = $allowedReferer;

        return $this;
    }

    /**
     * Get allowedReferer
     *
     * @return string 
     */
    public function getAllowedReferer()
    {
        return $this->allowedReferer;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return App
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
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

    /**
     * Set approved
     *
     * @param boolean $approved
     * @return App
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }

    /**
     * Get approved
     *
     * @return boolean 
     */
    public function getApproved()
    {
        return $this->approved;
    }
}
