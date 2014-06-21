<?php

namespace Caldera\CriticalmassPlusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="plus_voucher_code")
 */
class VoucherCode
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Voucherclass", inversedBy="codes")
     * @ORM\JoinColumn(name="voucher_code_id", referencedColumnName="id")
     */
    protected $voucherClass;

    /*
	 * @ORM\Column(type="string", length=255)
	 */
    protected $code;

    /**
     * @ORM\OneToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="code")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
	protected $user;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $activationDateTime;

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
     * Set activationDateTime
     *
     * @param \DateTime $activationDateTime
     * @return VoucherCode
     */
    public function setActivationDateTime($activationDateTime)
    {
        $this->activationDateTime = $activationDateTime;

        return $this;
    }

    /**
     * Get activationDateTime
     *
     * @return \DateTime 
     */
    public function getActivationDateTime()
    {
        return $this->activationDateTime;
    }

    /**
     * Set voucherClass
     *
     * @param \Caldera\CriticalmassPlusBundle\Entity\Voucherclass $voucherClass
     * @return VoucherCode
     */
    public function setVoucherClass(\Caldera\CriticalmassPlusBundle\Entity\Voucherclass $voucherClass = null)
    {
        $this->voucherClass = $voucherClass;

        return $this;
    }

    /**
     * Get voucherClass
     *
     * @return \Caldera\CriticalmassPlusBundle\Entity\Voucherclass 
     */
    public function getVoucherClass()
    {
        return $this->voucherClass;
    }

    /**
     * Set user
     *
     * @param \Application\Sonata\UserBundle\Entity\User $user
     * @return VoucherCode
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
}
