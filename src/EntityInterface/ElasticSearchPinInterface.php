<?php declare(strict_types=1);

namespace App\EntityInterface;

interface ElasticSearchPinInterface
{
    public function getPin(): string;
}
