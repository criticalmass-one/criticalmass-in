<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\ViewInterface;

interface ViewableEntity
{
    public function getId(): ?int;

    public function getViews(): int;

    public function setViews(int $views): ViewableEntity;

    public function incViews(): ViewableEntity;
}
