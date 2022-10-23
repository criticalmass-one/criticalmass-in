<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\ViewStorage\ViewInterface\ViewEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="promotion_view")
 * @ORM\Entity()
 */
class PromotionView implements ViewEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="promotion_views")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity="Promotion", inversedBy="promotion_views")
     * @ORM\JoinColumn(name="photo_id", referencedColumnName="id")
     */
    protected ?Promotion $promotion = null;

    /**
     * @ORM\Column(type="datetime")
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

    public function getPromotion(): Promotion
    {
        return $this->promotion;
    }

    public function setPromotion(Promotion $promotion): PromotionView
    {
        $this->promotion= $promotion;

        return $this;
    }
}
