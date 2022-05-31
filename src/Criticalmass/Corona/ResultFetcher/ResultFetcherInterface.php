<?php declare(strict_types=1);

namespace App\Criticalmass\Corona\ResultFetcher;

use App\Criticalmass\Corona\Model\Result;
use App\EntityInterface\CoordinateInterface;

interface ResultFetcherInterface
{
    public function fetch(CoordinateInterface $coordinate): ?Result;
}
