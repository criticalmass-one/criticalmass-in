<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\SlugGenerator;


class SlugGenerator
{
    public function generate($string)
    {
        $string = strtolower($string);

        $string = str_replace
        (
            [
                ' '
            ],
            [
                '-'
            ],
            $string
        );

        return $string;
    }
}