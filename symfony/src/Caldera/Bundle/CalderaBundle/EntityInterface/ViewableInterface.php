<?php

namespace Caldera\Bundle\CriticalmassModelBundle\EntityInterface;

interface ViewableInterface
{
    public function getId();
    public function getViews();
    public function setViews($views);
}