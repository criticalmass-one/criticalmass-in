<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\Router\Attribute as Routing;
use App\EntityInterface\RouteableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'city_cycle')]
#[ORM\Entity(repositoryClass: 'App\Repository\CityCycleRepository')]
class CityCycle implements RouteableInterface
{
    const DAY_MONDAY = 1;
    const DAY_TUESDAY = 2;
    const DAY_WEDNESDAY = 3;
    const DAY_THURSDAY = 4;
    const DAY_FRIDAY = 5;
    const DAY_SATURDAY = 6;
    const DAY_SUNDAY = 0;

    const WEEK_FIRST = 1;
    const WEEK_SECOND = 2;
    const WEEK_THIRD = 3;
    const WEEK_FOURTH = 4;
    const WEEK_LAST = 0;

    #[Routing\RouteParameter(name: 'id')]
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[Routing\RouteParameter(name: 'citySlug')]
    #[ORM\ManyToOne(targetEntity: 'City', inversedBy: 'cycles')]
    #[ORM\JoinColumn(name: 'city_id', referencedColumnName: 'id')]
    #[Ignore]
    protected ?City $city = null;

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'cityCycles')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[Ignore]
    protected ?User $user = null;

    #[ORM\OneToMany(targetEntity: 'Ride', mappedBy: 'cycle', cascade: ['persist', 'remove'])]
    #[Ignore]
    protected Collection $rides;

    #[Assert\Range(min: 0, max: 6)]
    #[ORM\Column(type: 'smallint', nullable: false)]
    #[Groups(['ride-list'])]
    protected ?int $dayOfWeek = null;

    #[Assert\Range(min: 0, max: 4)]
    #[ORM\Column(type: 'smallint', nullable: true)]
    #[Groups(['ride-list'])]
    protected ?int $weekOfMonth = null;

    #[Assert\Type(type: '\DateTime')]
    #[ORM\Column(type: 'time', nullable: true)]
    protected ?\DateTime $time = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['ride-list'])]
    protected ?string $location = null;

    #[Assert\NotEqualTo(value: '0.0')]
    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['ride-list'])]
    protected ?float $latitude = null;

    #[Assert\NotEqualTo(value: '0.0')]
    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['ride-list'])]
    protected ?float $longitude = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    protected \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTime $updatedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTime $disabledAt = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['ride-list'])]
    protected ?\DateTime $validFrom = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['ride-list'])]
    protected ?\DateTime $validUntil = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $rideCalculatorFqcn = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $specialDayOfWeek = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $specialWeekOfMonth = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->rides = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCity(City $city): CityCycle
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setUser(?User $user): CityCycle
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setLatitude(?float $latitude = null): CityCycle
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLongitude(?float $longitude = null): CityCycle
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setDayOfWeek(int $dayOfWeek): CityCycle
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getDayOfWeek(): ?int
    {
        return $this->dayOfWeek;
    }

    public function setWeekOfMonth(int $weekOfMonth): CityCycle
    {
        $this->weekOfMonth = $weekOfMonth;

        return $this;
    }

    public function getWeekOfMonth(): ?int
    {
        return $this->weekOfMonth;
    }

    public function setTime(?\DateTime $time = null): CityCycle
    {
        $this->time = $time;

        return $this;
    }

    public function getTime(): ?\DateTime
    {
        return $this->time;
    }

    public function setLocation(?string $location = null): CityCycle
    {
        $this->location = $location;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setCreatedAt(\DateTime $createdAt): CityCycle
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt = null): CityCycle
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setDisabledAt(?\DateTime $disabledAt = null): CityCycle
    {
        $this->disabledAt = $disabledAt;

        return $this;
    }

    public function getDisabledAt(): ?\DateTime
    {
        return $this->disabledAt;
    }

    public function setValidFrom(?\DateTime $validFrom = null): CityCycle
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    public function getValidFrom(): ?\DateTime
    {
        return $this->validFrom;
    }

    public function setValidUntil(?\DateTime $validUntil = null): CityCycle
    {
        $this->validUntil = $validUntil;

        return $this;
    }

    public function getValidUntil(): ?\DateTime
    {
        return $this->validUntil;
    }

    public function hasRange(): bool
    {
        return ($this->validFrom && $this->validUntil);
    }

    /**
     * @param \DateTime|null $dateTime
     * @return bool
     * @throws \Exception
     * @deprecated
     */
    public function isValid(?\DateTime $dateTime = null): bool
    {
        if (!$dateTime) {
            $dateTime = new \DateTime();
        }

        return ($this->validFrom <= $dateTime && $this->validUntil >= $dateTime) ||
            ($this->validFrom <= $dateTime && $this->validUntil === null) ||
            ($this->validFrom === null && $this->validUntil >= $dateTime);
    }

    public function addRide(Ride $ride): CityCycle
    {
        $this->rides->add($ride);

        return $this;
    }

    public function setRides(Collection $rides): CityCycle
    {
        $this->rides = $rides;

        return $this;
    }

    public function getRides(): Collection
    {
        return $this->rides;
    }

    public function removeRide(Ride $ride): CityCycle
    {
        $this->rides->removeElement($ride);

        return $this;
    }

    public function getRideCalculatorFqcn(): ?string
    {
        return $this->rideCalculatorFqcn;
    }

    public function setRideCalculatorFqcn(?string $rideCalculatorFqcn): self
    {
        $this->rideCalculatorFqcn = $rideCalculatorFqcn;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function hasSpecialCalculator(): bool
    {
        return $this->rideCalculatorFqcn !== null;
    }

    public function getSpecialDayOfWeek(): ?string
    {
        return $this->specialDayOfWeek;
    }

    public function setSpecialDayOfWeek(?string $specialDayOfWeek): self
    {
        $this->specialDayOfWeek = $specialDayOfWeek;

        return $this;
    }

    public function getSpecialWeekOfMonth(): ?string
    {
        return $this->specialWeekOfMonth;
    }

    public function setSpecialWeekOfMonth(?string $specialWeekOfMonth): self
    {
        $this->specialWeekOfMonth = $specialWeekOfMonth;

        return $this;
    }
}
