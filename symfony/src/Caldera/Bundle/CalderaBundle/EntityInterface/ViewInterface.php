<?php

namespace Caldera\Bundle\CriticalmassModelBundle\EntityInterface;

use Caldera\Bundle\CriticalmassModelBundle\Entity\User;

interface ViewInterface
{
    public function getId();
    public function setUser(User $user = null);
    public function getUser();
    public function setDateTime(\DateTime $dateTime);
    public function getDateTime();
}