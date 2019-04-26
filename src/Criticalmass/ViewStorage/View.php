<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage;

use App\Entity\User;
use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 */
class View
{
    /**
     * @var int $entityId
     * @JMS\Expose
     * @JMS\Type("int")
     */
    protected $entityId;

    /**
     * @var string $entityClassName
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $entityClassName;

    /**
     * @var User $user
     * @JMS\Expose
     * @JMS\Type("App\Entity\User")
     */
    protected $user;

    /**
     * @var \DateTime $dateTime
     * @JMS\Expose
     * @JMS\Type("DateTime<'U'>")
     */
    protected $dateTime;

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): View
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    public function setEntityClassName(string $entityClassName): View
    {
        $this->entityClassName = $entityClassName;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user = null): View
    {
        $this->user = $user;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): View
    {
        $this->dateTime = $dateTime;

        return $this;
    }
}