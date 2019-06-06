<?php declare(strict_types=1);

namespace Tests\OrderedEntities;

use App\Criticalmass\OrderedEntities\Annotation\Identical;
use App\Criticalmass\OrderedEntities\Annotation\Order;
use App\Criticalmass\OrderedEntities\Annotation\OrderedEntity;
use App\Criticalmass\OrderedEntities\OrderedEntityInterface;

/**
 * @OrderedEntity()
 */
class TestEntity implements OrderedEntityInterface
{
    /**
     * @Order(direction="asc")
     * @var \DateTime $dateTime
     */
    protected $dateTime;

    /**
     * @Identical()
     * @var string $city
     */
    protected $city;

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): TestEntity
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): TestEntity
    {
        $this->city = $city;

        return $this;
    }
}
