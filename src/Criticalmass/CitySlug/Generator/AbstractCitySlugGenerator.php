<?php declare(strict_types=1);

namespace App\Criticalmass\CitySlug\Generator;

use Malenki\Slug;

abstract class AbstractCitySlugGenerator implements CitySlugGeneratorInterface
{
    protected function createSlug(string $string): string
    {
        return (string) (new Slug($string))->noHistory();
    }
}