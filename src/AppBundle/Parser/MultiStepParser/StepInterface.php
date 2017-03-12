<?php

namespace AppBundle\Parser\MultiStepParser;

interface StepInterface
{
    public function parse(string $text): string;
}