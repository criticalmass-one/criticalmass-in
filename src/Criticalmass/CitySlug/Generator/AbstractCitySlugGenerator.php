<?php declare(strict_types=1);

namespace App\Criticalmass\CitySlug\Generator;

use Symfony\Component\String\Slugger\AsciiSlugger;

abstract class AbstractCitySlugGenerator implements CitySlugGeneratorInterface
{
    protected function createSlug(string $string): string
    {
        return (new AsciiSlugger())->slug($string)->lower()->toString();
    }
}