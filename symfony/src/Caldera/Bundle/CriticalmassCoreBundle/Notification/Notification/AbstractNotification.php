<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Notification\Notification;

abstract class AbstractNotification
{
    const ON_CHANGE = 1;
    const ON_CREATE = 2;
    const ON_PUBLISHED_LOCATION = 4;
    const ON_LOCATION_UPDATE = 8;

    protected $entity;
    protected $title;
    protected $message;
    protected $shortMessage;
    protected $creationDateTime;

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getShortMessage()
    {
        return $this->shortMessage;
    }

    /**
     * @param mixed $shortMessage
     */
    public function setShortMessage($shortMessage)
    {
        $this->shortMessage = $shortMessage;
    }

    /**
     * @return mixed
     */
    public function getCreationDateTime()
    {
        return $this->creationDateTime;
    }

    /**
     * @param mixed $creationDateTime
     */
    public function setCreationDateTime($creationDateTime)
    {
        $this->creationDateTime = $creationDateTime;
    }

}