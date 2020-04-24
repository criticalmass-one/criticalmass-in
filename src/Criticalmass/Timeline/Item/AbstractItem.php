<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\User;

abstract class AbstractItem implements ItemInterface
{
    /** @var string $uniqId */
    protected $uniqId;

    /** @var \DateTime $dateTime */
    protected $dateTime;

    /** @var User $user */
    protected $user;

    /** @var string $tabName */
    protected $tabName = 'standard';

    public function __construct()
    {
        $this->uniqId = uniqid();
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): AbstractItem
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getUniqId(): string
    {
        return $this->uniqId;
    }

    public function setUser(User $user): AbstractItem
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getTabName(): string
    {
        return $this->tabName;
    }
}
