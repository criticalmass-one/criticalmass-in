<?php

namespace Caldera\Bundle\CalderaBundle\Parser\MultiStepParser;

interface StepInterface
{
    public function parse(string $text): string;
}