<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\RequestParameterList;

use Symfony\Component\HttpFoundation\Request;

class RequestToListConverter
{
    public static function convert(Request $request): RequestParameterList
    {
        $requestParameterList = new RequestParameterList();

        foreach ($request->query->all() as $key => $value) {
            if (is_string($value)) {
                $requestParameterList->add($key, $value);
            }
        }

        return $requestParameterList;
    }
}
