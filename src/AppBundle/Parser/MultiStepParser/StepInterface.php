<?php

namespace AppBundle\Parser\MultiStepParser;

/**
 * @deprecated
 */
interface StepInterface
{
    public function parse(string $text): string;
}