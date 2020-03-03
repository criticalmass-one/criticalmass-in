<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\RequestParameterList;

class ArrayToListConverter
{
    private function __construct()
    {

    }

    public static function convert(array $array): RequestParameterList
    {
        $requestParameterList = new RequestParameterList();

        foreach ($array as $key => $value) {
            $requestParameterList->add($key, $value);
        }

        return $requestParameterList;
    }
}
