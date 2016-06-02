<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="facebook_city_properties")
 */
class FacebookCityProperties
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="facebookProperties")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $about;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $generalInfo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $likeNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $checkinNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $website;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * Set name
     *
     * @param string $name
     * @return FacebookCityProperties
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set about
     *
     * @param string $about
     * @return FacebookCityProperties
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get about
     *
     * @return string 
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return FacebookCityProperties
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
     * Set generalInfo
     *
     * @param string $generalInfo
     * @return FacebookCityProperties
     */
    public function setGeneralInfo($generalInfo)
    {
        $this->generalInfo = $generalInfo;

        return $this;
    }

    /**
     * Get generalInfo
     *
     * @return string 
     */
    public function getGeneralInfo()
    {
        return $this->generalInfo;
    }

    /**
     * Set likeNumber
     *
     * @param integer $likeNumber
     * @return FacebookCityProperties
     */
    public function setLikeNumber($likeNumber)
    {
        $this->likeNumber = $likeNumber;

        return $this;
    }

    /**
     * Get likeNumber
     *
     * @return integer 
     */
    public function getLikeNumber()
    {
        return $this->likeNumber;
    }

    /**
     * Set checkinNumber
     *
     * @param integer $checkinNumber
     * @return FacebookCityProperties
     */
    public function setCheckinNumber($checkinNumber)
    {
        $this->checkinNumber = $checkinNumber;

        return $this;
    }

    /**
     * Get checkinNumber
     *
     * @return integer 
     */
    public function getCheckinNumber()
    {
        return $this->checkinNumber;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return FacebookCityProperties
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return FacebookCityProperties
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set city
     *
     * @param City $city
     * @return FacebookCityProperties
     */
    public function setCity(City $city = null)
    {
        $this->city = $city;

        return $this;
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
}
