<?php

namespace Caldera\Bundle\CalderaBundle\EntityInterface;

use Caldera\Bundle\CalderaBundle\Entity\User;

interface ViewInterface
{
    public function getId();
    public function setUser(User $user = null);
    public function getUser();
    public function setDateTime(\DateTime $dateTime);
    public function getDateTime();
}