<?php declare(strict_types=1);

namespace App\Entity;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'participation')]
#[ORM\Entity(repositoryClass: 'App\Repository\ParticipationRepository')]
class Participation
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'Ride', inversedBy: 'participations')]
    #[ORM\JoinColumn(name: 'ride_id', referencedColumnName: 'id')]
    protected ?Ride $ride = null;

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'participations')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    protected ?User $user = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected Carbon $dateTime;

    #[ORM\Column(type: 'boolean', nullable: true)]
    protected bool $goingYes = true;

    #[ORM\Column(type: 'boolean', nullable: true)]
    protected bool $goingMaybe = true;

    #[ORM\Column(type: 'boolean', nullable: true)]
    protected bool $goingNo = true;

    public function __construct()
    {
        $this->dateTime = Carbon::now();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateTime(): Carbon
    {
        return $this->dateTime;
    }

    public function setDateTime($dateTime): Participation
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getGoingYes(): bool
    {
        return $this->goingYes;
    }

    public function setGoingYes(bool $goingYes): Participation
    {
        $this->goingYes = $goingYes;

        return $this;
    }

    public function getGoingMaybe(): bool
    {
        return $this->goingMaybe;
    }

    public function setGoingMaybe(bool $goingMaybe): Participation
    {
        $this->goingMaybe = $goingMaybe;

        return $this;
    }

    public function getGoingNo(): bool
    {
        return $this->goingNo;
    }

    public function setGoingNo(bool $goingNo): Participation
    {
        $this->goingNo = $goingNo;

        return $this;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setRide(?Ride $ride = null): Participation
    {
        $this->ride = $ride;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user = null): Participation
    {
        $this->user = $user;

        return $this;
    }
}
