<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Template;

abstract class AbstractTemplate
{
    protected $template;

    public function getTemplate()
    {
        return $this->template;
    }
}