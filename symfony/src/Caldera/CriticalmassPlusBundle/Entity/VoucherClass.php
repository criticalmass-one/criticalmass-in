<?php

namespace Caldera\CriticalmassPlusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="plus_voucher_class")
 */
class VoucherClass
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    protected $id;

    /*
	 * @ORM\Column(type="string", length=255)
	 */
    protected $title;

	/**
	 * @ORM\Column(type="text")
	 */
	protected $description;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $validSince;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $validUntil;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $codePrefix;


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
     * Set description
     *
     * @param string $description
     * @return VoucherClass
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
     * Set validSince
     *
     * @param \DateTime $validSince
     * @return VoucherClass
     */
    public function setValidSince($validSince)
    {
        $this->validSince = $validSince;

        return $this;
    }

    /**
     * Get validSince
     *
     * @return \DateTime 
     */
    public function getValidSince()
    {
        return $this->validSince;
    }

    /**
     * Set validUntil
     *
     * @param \DateTime $validUntil
     * @return VoucherClass
     */
    public function setValidUntil($validUntil)
    {
        $this->validUntil = $validUntil;

        return $this;
    }

    /**
     * Get validUntil
     *
     * @return \DateTime 
     */
    public function getValidUntil()
    {
        return $this->validUntil;
    }

    /**
     * Set codePrefix
     *
     * @param string $codePrefix
     * @return VoucherClass
     */
    public function setCodePrefix($codePrefix)
    {
        $this->codePrefix = $codePrefix;

        return $this;
    }

    /**
     * Get codePrefix
     *
     * @return string 
     */
    public function getCodePrefix()
    {
        return $this->codePrefix;
    }
}
