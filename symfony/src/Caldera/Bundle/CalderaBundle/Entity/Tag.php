<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="tag")
 * @ORM\Entity()
 */
class Tag
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
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="BikeShop", inversedBy="tags")
     */
    protected $bikeShops;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bikeShops = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): integer
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Tag
     */
    public function setName(string $name): Tag
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Add bikeShop
     *
     * @param BikeShop $bikeShop
     * @return Tag
     */
    public function addBikeShop(BikeShop $bikeShop): Tag
    {
        $this->bikeShops[] = $bikeShop;

        return $this;
    }

    /**
     * Remove bikeShop
     *
     * @param BikeShop $bikeShop
     */
    public function removeBikeShop(BikeShop $bikeShop)
    {
        $this->bikeShops->removeElement($bikeShop);
    }

    /**
     * Get bikeShops
     *
     * @return Collection
     */
    public function getBikeShops(): Collection
    {
        return $this->bikeShops;
    }
}
