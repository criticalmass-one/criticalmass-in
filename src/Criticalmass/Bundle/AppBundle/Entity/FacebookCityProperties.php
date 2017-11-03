<?php

namespace Criticalmass\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Criticalmass\Bundle\AppBundle\Repository\FacebookCityPropertiesRepository")
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

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName($name = null): FacebookCityProperties
    {
        $this->name = $name;

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(string $about = null): FacebookCityProperties
    {
        $this->about = $about;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description = null): FacebookCityProperties
    {
        $this->description = $description;

        return $this;
    }

    public function getGeneralInfo(): ?string
    {
        return $this->generalInfo;
    }

    public function setGeneralInfo(string $generalInfo = null): FacebookCityProperties
    {
        $this->generalInfo = $generalInfo;

        return $this;
    }

    public function getLikeNumber(): ?int
    {
        return $this->likeNumber;
    }

    public function setLikeNumber(int $likeNumber = null): FacebookCityProperties
    {
        $this->likeNumber = $likeNumber;

        return $this;
    }

    public function getCheckinNumber(): ?int
    {
        return $this->checkinNumber;
    }

    public function setCheckinNumber(int $checkinNumber = null): FacebookCityProperties
    {
        $this->checkinNumber = $checkinNumber;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(string $website = null): FacebookCityProperties
    {
        $this->website = $website;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): FacebookCityProperties
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(City $city = null): FacebookCityProperties
    {
        $this->city = $city;

        return $this;
    }
}
