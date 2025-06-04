<?php declare(strict_types=1);

namespace App\EntityInterface;

interface ParticipateableInterface
{
    public function setParticipationsNumberYes(int $participationsNumberYes): ParticipateableInterface;

    public function getParticipationsNumberYes(): int;

    public function setParticipationsNumberMaybe(int $participationsNumberMaybe): ParticipateableInterface;

    public function getParticipationsNumberMaybe(): int;

    public function setParticipationsNumberNo(int $participationsNumberNo): ParticipateableInterface;

    public function getParticipationsNumberNo(): int;
}
