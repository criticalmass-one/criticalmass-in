<?php declare(strict_types=1);

namespace Tests\ViewStorage;

use App\Criticalmass\ViewStorage\ViewInterface\ViewEntity;
use App\Entity\User;

class TestView implements ViewEntity
{
    /** @var int $id */
    protected $id;

    /** @var User $user */
    protected $user;

    /** @var \DateTime $dateTime */
    protected $dateTime;

    /** @var TestClass $testClass */
    protected $testClass;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): ViewEntity
    {
        $this->id = $id;

        return $this;
    }

    public function setUser(User $user = null): ViewEntity
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setDateTime(\DateTime $dateTime): ViewEntity
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setTest(TestClass $testClass): TestView
    {
        $this->testClass = $testClass;

        return $this;
    }

    public function getTest(): TestClass
    {
        return $this->testClass;
    }
}
