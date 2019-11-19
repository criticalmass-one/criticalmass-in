<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\RequestParameterList;

use Symfony\Component\HttpFoundation\Request;

class RequestToListConverter
{
    private function __construct()
    {

    }

    public static function convert(Request $request): RequestParameterList
    {
        $requestParameterList = new RequestParameterList();

        foreach ($request->query->all() as $key => $value) {
            $requestParameterList->add($key, $value);
        }

        return $requestParameterList;
    }
}
