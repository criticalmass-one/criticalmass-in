<?php

namespace Criticalmass\Bundle\AppBundle\EntityInterface;

interface ParticipateableInterface
{
    public function setParticipationsNumberYes(int $participationsNumberYes);

    public function getParticipationsNumberYes(): int;

    public function setParticipationsNumberMaybe(int $participationsNumberMaybe);

    public function getParticipationsNumberMaybe(): int;

    public function setParticipationsNumberNo(int $participationsNumberNo);

    public function getParticipationsNumberNo(): int;
}
