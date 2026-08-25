<?php declare(strict_types=1);

namespace App\Criticalmass\Util;

use Symfony\Component\HttpFoundation\Request;

/**
 * Drop-in replacement for the removed Request::get(): looks a parameter up in
 * the route attributes first, then in the query string, then in the request body.
 */
final class RequestParameter
{
    public static function get(Request $request, string $key, mixed $default = null): mixed
    {
        if ($request->attributes->has($key)) {
            return $request->attributes->get($key);
        }

        if ($request->query->has($key)) {
            return $request->query->all()[$key];
        }

        if ($request->request->has($key)) {
            return $request->request->all()[$key];
        }

        return $default;
    }
}
