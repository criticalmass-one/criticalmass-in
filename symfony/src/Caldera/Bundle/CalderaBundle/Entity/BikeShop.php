<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Caldera\Bundle\CalderaBundle\Entity\BaseLocationEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="caldera_bikeshop")
 * @ORM\Entity()
 */
class BikeShop extends BaseLocationEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $address;
    
    /**
     * Set address
     *
     * @param string $address
     * @return BikeShop
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }
}
