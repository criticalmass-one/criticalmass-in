<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="notification_subscription")
 * @ORM\Entity()
 */
class NotificationSubscription
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="notification_subscriptions")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="notification_subscriptions")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="notification_subscriptions")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     */
    protected $event;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="notification_subscriptions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $notifyByMail;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $notifyByPushover;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $notifyByShortmessage;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $notifyOnChange;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $notifyOnCreate;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $notifyOnSpecial;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $notifyOnActivity;


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
     * Set notifyByMail
     *
     * @param boolean $notifyByMail
     * @return Notification
     */
    public function setNotifyByMail($notifyByMail)
    {
        $this->notifyByMail = $notifyByMail;

        return $this;
    }

    /**
     * Get notifyByMail
     *
     * @return boolean 
     */
    public function getNotifyByMail()
    {
        return $this->notifyByMail;
    }

    /**
     * Set notifyByPushover
     *
     * @param boolean $notifyByPushover
     * @return Notification
     */
    public function setNotifyByPushover($notifyByPushover)
    {
        $this->notifyByPushover = $notifyByPushover;

        return $this;
    }

    /**
     * Get notifyByPushover
     *
     * @return boolean 
     */
    public function getNotifyByPushover()
    {
        return $this->notifyByPushover;
    }

    /**
     * Set notifyByShortmessage
     *
     * @param boolean $notifyByShortmessage
     * @return Notification
     */
    public function setNotifyByShortmessage($notifyByShortmessage)
    {
        $this->notifyByShortmessage = $notifyByShortmessage;

        return $this;
    }

    /**
     * Get notifyByShortmessage
     *
     * @return boolean 
     */
    public function getNotifyByShortmessage()
    {
        return $this->notifyByShortmessage;
    }

    /**
     * Set notifyOnChange
     *
     * @param boolean $notifyOnChange
     * @return Notification
     */
    public function setNotifyOnChange($notifyOnChange)
    {
        $this->notifyOnChange = $notifyOnChange;

        return $this;
    }

    /**
     * Get notifyOnChange
     *
     * @return boolean 
     */
    public function getNotifyOnChange()
    {
        return $this->notifyOnChange;
    }

    /**
     * Set notifyOnCreate
     *
     * @param boolean $notifyOnCreate
     * @return Notification
     */
    public function setNotifyOnCreate($notifyOnCreate)
    {
        $this->notifyOnCreate = $notifyOnCreate;

        return $this;
    }

    /**
     * Get notifyOnCreate
     *
     * @return boolean 
     */
    public function getNotifyOnCreate()
    {
        return $this->notifyOnCreate;
    }

    /**
     * Set notifyOnSpecial
     *
     * @param boolean $notifyOnSpecial
     * @return Notification
     */
    public function setNotifyOnSpecial($notifyOnSpecial)
    {
        $this->notifyOnSpecial = $notifyOnSpecial;

        return $this;
    }

    /**
     * Get notifyOnSpecial
     *
     * @return boolean 
     */
    public function getNotifyOnSpecial()
    {
        return $this->notifyOnSpecial;
    }

    /**
     * Set notifyOnActivity
     *
     * @param boolean $notifyOnActivity
     * @return Notification
     */
    public function setNotifyOnActivity($notifyOnActivity)
    {
        $this->notifyOnActivity = $notifyOnActivity;

        return $this;
    }

    /**
     * Get notifyOnActivity
     *
     * @return boolean 
     */
    public function getNotifyOnActivity()
    {
        return $this->notifyOnActivity;
    }

    /**
     * Set city
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\City $city
     * @return Notification
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
     * @return Notification
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
     * @return Notification
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
     * @return Notification
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
