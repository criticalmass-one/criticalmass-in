<?php declare(strict_types=1);

namespace App\Criticalmass\RideDuplicates\DuplicateFinder;

use App\Entity\City;

interface DuplicateFinderInterface
{
    public function setCity(City $city): DuplicateFinderInterface;
    public function findDuplicates(): array;
}
