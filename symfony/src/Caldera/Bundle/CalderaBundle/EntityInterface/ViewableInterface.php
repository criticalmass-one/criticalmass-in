<?php

namespace Caldera\Bundle\CalderaBundle\EntityInterface;

interface ViewableInterface
{
    public function getId();
    public function getViews();
    public function setViews($views);
    public function incViews();
}