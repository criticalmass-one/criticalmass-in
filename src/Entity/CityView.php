<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\ViewStorage\ViewInterface\ViewEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="city_view")
 * @ORM\Entity()
 */
class CityView implements ViewEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="city_views")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected ?City $city = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected \DateTime $dateTime;

    public function __construct()
    {
        $this->dateTime = new \DateTime();
    }

    public function setId(int $id): ViewEntity
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user = null): ViewEntity
    {
        $this->user = $user;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): ViewEntity
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(City $city = null): CityView
    {
        $this->city = $city;

        return $this;
    }
}
