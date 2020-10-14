<?php declare(strict_types=1);

namespace App\Criticalmass\Corona\Twig;

use App\Criticalmass\Corona\ResultFetcher\ResultFetcherInterface;
use App\EntityInterface\CoordinateInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CoronaTwigExtension extends AbstractExtension
{
    protected ResultFetcherInterface $resultFetcher;

    public function __construct(ResultFetcherInterface $resultFetcher)
    {
        $this->resultFetcher = $resultFetcher;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('corona', function (CoordinateInterface $coordinate) {
                return $this->resultFetcher->fetch($coordinate);
            }),
        ];
    }
}