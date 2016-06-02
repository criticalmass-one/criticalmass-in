<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="tweet")
 */
class Tweet
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="City", mappedBy="city")
     */
    protected $city;

    /**
     * @ORM\OneToMany(targetEntity="Ride", mappedBy="ride")
     */
    protected $ride;

    /**
     * @ORM\Column(type="string", length=140, nullable=true)
     */
    protected $text;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $screenname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $profileImageUrl;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateTime;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $twitterId;

    /**
     * @ORM\Column(type="text")
     */
    protected $rawResponse;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->city = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ride = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set text
     *
     * @param string $text
     * @return Tweet
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Tweet
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set screenname
     *
     * @param string $screenname
     * @return Tweet
     */
    public function setScreenname($screenname)
    {
        $this->screenname = $screenname;

        return $this;
    }

    /**
     * Get screenname
     *
     * @return string 
     */
    public function getScreenname()
    {
        return $this->screenname;
    }

    /**
     * Set profileImageUrl
     *
     * @param string $profileImageUrl
     * @return Tweet
     */
    public function setProfileImageUrl($profileImageUrl)
    {
        $this->profileImageUrl = $profileImageUrl;

        return $this;
    }

    /**
     * Get profileImageUrl
     *
     * @return string 
     */
    public function getProfileImageUrl()
    {
        return $this->profileImageUrl;
    }

    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     * @return Tweet
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
     * Set twitterId
     *
     * @param string $twitterId
     * @return Tweet
     */
    public function setTwitterId($twitterId)
    {
        $this->twitterId = $twitterId;

        return $this;
    }

    /**
     * Get twitterId
     *
     * @return string 
     */
    public function getTwitterId()
    {
        return $this->twitterId;
    }

    /**
     * Set rawResponse
     *
     * @param string $rawResponse
     * @return Tweet
     */
    public function setRawResponse($rawResponse)
    {
        $this->rawResponse = $rawResponse;

        return $this;
    }

    /**
     * Get rawResponse
     *
     * @return string 
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    /**
     * Add city
     *
     * @param City $city
     * @return Tweet
     */
    public function addCity(City $city)
    {
        $this->city[] = $city;

        return $this;
    }

    /**
     * Remove city
     *
     * @param City $city
     */
    public function removeCity(City $city)
    {
        $this->city->removeElement($city);
    }

    /**
     * Get city
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Add ride
     *
     * @param Ride $ride
     * @return Tweet
     */
    public function addRide(Ride $ride)
    {
        $this->ride[] = $ride;

        return $this;
    }

    /**
     * Remove ride
     *
     * @param Ride $ride
     */
    public function removeRide(Ride $ride)
    {
        $this->ride->removeElement($ride);
    }

    /**
     * Get ride
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRide()
    {
        return $this->ride;
    }
}
