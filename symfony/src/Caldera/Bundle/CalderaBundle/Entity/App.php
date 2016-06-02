<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="apps")
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
    protected $apiCalls = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $creationDateTime;

    /**
     * @ORM\Column(type="string", length=256)
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $description;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $restrictedAccess = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $allowedReferer;

    /**
     * @ORM\Column(type="string", length=256)
     * @Assert\Url()
     */
    protected $url;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $approved = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $deleted = 0;
    
    public function __construct()
    {
        $this->setToken(md5(microtime()));
        $this->setCreationDateTime(new \DateTime());
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
     * @param User $user
     * @return App
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
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

    /**
     * Set restrictedAccess
     *
     * @param boolean $restrictedAccess
     * @return App
     */
    public function setRestrictedAccess($restrictedAccess)
    {
        $this->restrictedAccess = $restrictedAccess;

        return $this;
    }

    /**
     * Get restrictedAccess
     *
     * @return boolean 
     */
    public function getRestrictedAccess()
    {
        return $this->restrictedAccess;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return App
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
}
