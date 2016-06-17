<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="notification_sent")
 * @ORM\Entity()
 */
class SentNotification
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="sent_notifications")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="sent_notifications")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="sent_notifications")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     */
    protected $event;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sent_notifications")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $email;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $pushover;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $shortmessage;

    /**
     * @ORM\Column(type="type", length=255)
     */
    protected $type;

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
     * Set email
     *
     * @param boolean $email
     * @return SentNotification
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return boolean 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set pushover
     *
     * @param boolean $pushover
     * @return SentNotification
     */
    public function setPushover($pushover)
    {
        $this->pushover = $pushover;

        return $this;
    }

    /**
     * Get pushover
     *
     * @return boolean 
     */
    public function getPushover()
    {
        return $this->pushover;
    }

    /**
     * Set shortmessage
     *
     * @param boolean $shortmessage
     * @return SentNotification
     */
    public function setShortmessage($shortmessage)
    {
        $this->shortmessage = $shortmessage;

        return $this;
    }

    /**
     * Get shortmessage
     *
     * @return boolean 
     */
    public function getShortmessage()
    {
        return $this->shortmessage;
    }

    /**
     * Set type
     *
     * @param \type $type
     * @return SentNotification
     */
    public function setType(\type $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \type 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set city
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\City $city
     * @return SentNotification
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

    /**
     * Set ride
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\Ride $ride
     * @return SentNotification
     */
    public function setRide(\Caldera\Bundle\CalderaBundle\Entity\Ride $ride = null)
    {
        $this->ride = $ride;

        return $this;
    }

    /**
     * Get ride
     *
     * @return \Caldera\Bundle\CalderaBundle\Entity\Ride 
     */
    public function getRide()
    {
        return $this->ride;
    }

    /**
     * Set event
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\Event $event
     * @return SentNotification
     */
    public function setEvent(\Caldera\Bundle\CalderaBundle\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \Caldera\Bundle\CalderaBundle\Entity\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set user
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\User $user
     * @return SentNotification
     */
    public function setUser(\Caldera\Bundle\CalderaBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Caldera\Bundle\CalderaBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
