<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

interface ParamConverterFactoryInterface
{
    public function createParamConverter(string $fqcn): ?ParamConverterInterface;
}