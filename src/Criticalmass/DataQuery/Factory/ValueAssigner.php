<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory;

use App\Criticalmass\DataQuery\Query\QueryInterface;
use App\Criticalmass\DataQuery\QueryProperty\QueryProperty;
use Symfony\Component\HttpFoundation\Request;

class ValueAssigner
{
    private function __construct()
    {

    }

    public static function assignPropertyValue(Request $request, QueryInterface $query, QueryProperty $property): QueryInterface
    {
        $methodName = $property->getMethodName();
        $parameter = $request->query->get($property->getParameterName());
        $type = $property->getType();

        switch ($type) {
            case 'float':
                $query->$methodName((float)$parameter);
                break;

            case 'int':
                $query->$methodName((int)$parameter);
                break;

            case 'string':
                $query->$methodName((string)$parameter);
                break;
        }

        return $query;
    }
}
