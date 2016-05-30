<?php
/**
 * Created by PhpStorm.
 * User: maltehuebner
 * Date: 30.05.16
 * Time: 22:57
 */

namespace Caldera\Bundle\CriticalmassModelBundle\EntityInterface;


use Caldera\Bundle\CriticalmassModelBundle\Entity\User;

interface ViewInterface
{
    public function getId();
    public function setUser(User $user);
    public function getUser();
    public function setDateTime(\DateTime $dateTime);
    public function getDateTime();
}