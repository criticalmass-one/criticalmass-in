<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Caldera\Bundle\CalderaBundle\EntityInterface\CoordinateInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\ElasticSearchPinInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\FacebookInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\TwitterInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\UrlInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="caldera_bikeshop")
 * @ORM\Entity()
 */
class BikeShop implements CoordinateInterface, FacebookInterface, TwitterInterface, UrlInterface, ViewableInterface, ElasticSearchPinInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="bikeShops")
     * @ORM\JoinTable(name="caldera_bikeshop_tags")
     */
    protected $tags;

    /**
     * @ORM\ManyToMany(targetEntity="OpeningTime")
     * @ORM\JoinTable(name="caldera_bikeshop_openingtime",
     *      joinColumns={@ORM\JoinColumn(name="bikeshop_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="openingtime_id", referencedColumnName="id", unique=true)}
     *      )
     */
    protected $openingTimes;

    /**
     * @ORM\Column(type="float")
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float")
     */
    protected $longitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $facebook;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $twitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(type="integer")
     */
    protected $views = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $street;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    protected $zip;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->openingTimes = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return BikeShop
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
     * @return BikeShop
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return BikeShop
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
     * Set latitude
     *
     * @param float $latitude
     * @return BikeShop
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return BikeShop
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set facebook
     *
     * @param string $facebook
     * @return BikeShop
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string 
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set twitter
     *
     * @param string $twitter
     * @return BikeShop
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return string 
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return BikeShop
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
     * @return $this
     */
    public function incViews()
    {
        ++$this->views;
        
        return $this;
    }
    
    /**
     * Set views
     *
     * @param integer $views
     * @return BikeShop
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer 
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Add tag
     *
     * @param Tag $tag
     * @return BikeShop
     */
    public function addTag(Tag $tag): BikeShop
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return Collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Add openingTime
     *
     * @param OpeningTime $openingTime
     * @return BikeShop
     */
    public function addOpeningTime(OpeningTime $openingTime): BikeShop
    {
        $this->openingTimes[] = $openingTime;

        return $this;
    }

    /**
     * Remove openingTime
     *
     * @param OpeningTime $openingTimes
     */
    public function removeOpeningTime(OpeningTime $openingTime)
    {
        $this->openingTimes->removeElement($openingTime);
    }

    /**
     * Get openingTimes
     *
     * @return Collection
     */
    public function getOpeningTimes(): Collection
    {
        return $this->openingTimes;
    }

    public function getPin(): string
    {
        return $this->latitude.','.$this->longitude;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return BikeShop
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return BikeShop
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return BikeShop
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return BikeShop
     */
    public function setCity($city): BikeShop
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set zip
     *
     * @param string $zip
     * @return BikeShop
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string 
     */
    public function getZip()
    {
        return $this->zip;
    }
}
