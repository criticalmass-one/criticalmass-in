<?php declare(strict_types=1);

namespace App\Criticalmass\Corona\Twig;

use App\Criticalmass\Corona\ResultFetcher\ResultFetcherInterface;
use App\EntityInterface\CoordinateInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CoronaTwigExtension extends AbstractExtension
{
    public function __construct(protected ResultFetcherInterface $resultFetcher)
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('corona', fn(CoordinateInterface $coordinate) => $this->resultFetcher->fetch($coordinate)),
        ];
    }
}