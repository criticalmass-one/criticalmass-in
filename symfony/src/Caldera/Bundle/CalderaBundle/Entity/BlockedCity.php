<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CalderaBundle\Repository\BlockedCityRepository")
 * @ORM\Table(name="city_blocked")
 */
class BlockedCity
{
    /**
     * Numerische ID der Stadt.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="blocked_cities", fetch="LAZY")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $blockStart;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $blockEnd;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $facebook;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $twitter;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $photosLink;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $rideListLink;

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
     * Set blockStart
     *
     * @param \DateTime $blockStart
     *
     * @return BlockedCity
     */
    public function setBlockStart($blockStart)
    {
        $this->blockStart = $blockStart;

        return $this;
    }

    /**
     * Get blockStart
     *
     * @return \DateTime
     */
    public function getBlockStart()
    {
        return $this->blockStart;
    }

    /**
     * Set blockEnd
     *
     * @param \DateTime $blockEnd
     *
     * @return BlockedCity
     */
    public function setBlockEnd($blockEnd)
    {
        $this->blockEnd = $blockEnd;

        return $this;
    }

    /**
     * Get blockEnd
     *
     * @return \DateTime
     */
    public function getBlockEnd()
    {
        return $this->blockEnd;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return BlockedCity
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
     * Set url
     *
     * @param string $url
     *
     * @return BlockedCity
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
     * Set facebook
     *
     * @param string $facebook
     *
     * @return BlockedCity
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
     *
     * @return BlockedCity
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
     * Set photosLink
     *
     * @param boolean $photosLink
     *
     * @return BlockedCity
     */
    public function setPhotosLink($photosLink)
    {
        $this->photosLink = $photosLink;

        return $this;
    }

    /**
     * Get photosLink
     *
     * @return boolean
     */
    public function getPhotosLink()
    {
        return $this->photosLink;
    }

    /**
     * Set rideListLink
     *
     * @param boolean $rideListLink
     *
     * @return BlockedCity
     */
    public function setRideListLink($rideListLink)
    {
        $this->rideListLink = $rideListLink;

        return $this;
    }

    /**
     * Get rideListLink
     *
     * @return boolean
     */
    public function getRideListLink()
    {
        return $this->rideListLink;
    }

    /**
     * Set city
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\City $city
     *
     * @return BlockedCity
     */
    public function setCity(\Caldera\Bundle\CalderaBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \Caldera\Bundle\CalderaBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }
}
