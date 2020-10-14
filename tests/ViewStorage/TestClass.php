<?php declare(strict_types=1);

namespace Tests\ViewStorage;

use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;

class TestClass implements ViewableEntity
{
    /** @var int $view */
    protected $views;

    public function getId(): ?int
    {
        return 1;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function setViews(int $views): ViewableEntity
    {
        $this->views = $views;

        return $this;
    }

    public function incViews(): ViewableEntity
    {
        ++$this->views;

        return $this;
    }
}