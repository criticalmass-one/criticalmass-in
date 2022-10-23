<?php declare(strict_types=1);

namespace App\Criticalmass\StaticMap\UrlGenerator;

abstract class AbstractUrlGenerator implements UrlGeneratorInterface
{
    /** @var array $defaultParameters */
    protected $defaultParameters = [
        'size' => '865x512',
    ];

    public function __construct(protected string $staticmapsHost)
    {
    }
}
