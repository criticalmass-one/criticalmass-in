<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\RequestParameterList;

class QueryStringToListConverter
{
    private function __construct()
    {

    }

    public static function convert(string $queryString): RequestParameterList
    {
        parse_str(html_entity_decode($queryString), $parameterList);

        return ArrayToListConverter::convert($parameterList);
    }
}
