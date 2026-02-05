<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\RequestParameterList;

class QueryStringToListConverter
{
    public static function convert(string $queryString): RequestParameterList
    {
        $requestParameterList = new RequestParameterList();

        parse_str($queryString, $parameters);

        foreach ($parameters as $key => $value) {
            if (is_string($value)) {
                $requestParameterList->add($key, $value);
            }
        }

        return $requestParameterList;
    }
}
