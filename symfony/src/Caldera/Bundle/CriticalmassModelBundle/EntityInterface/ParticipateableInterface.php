<?php

namespace Caldera\Bundle\CriticalmassModelBundle\EntityInterface;

interface ParticipateableInterface
{
    public function setParticipationsNumberYes($participationsNumberYes);
    public function getParticipationsNumberYes();

    public function setParticipationsNumberMaybe($participationsNumberMaybe);
    public function getParticipationsNumberMaybe();

    public function setParticipationsNumberNo($participationsNumberNo);
    public function getParticipationsNumberNo();
}