<?php declare(strict_types=1);

namespace App\ValueResolver;

use Symfony\Component\HttpFoundation\Request;

/**
 * Nachbau des mit Symfony 8 entfernten Request::get().
 *
 * Die Resolver bekommen ihre Schlüssel nicht nur aus der Route: /api/cycles und
 * /api/socialnetwork-profiles erwarten `citySlug` als Query-Parameter, andere
 * Endpunkte als Formularfeld, und Sub-Requests aus Twig (render(controller()))
 * reichen sie als Attribut durch. Request::get() hat genau in dieser Reihenfolge
 * gesucht — Attribute, Query, Request-Body —, und daran hängt das Verhalten.
 */
trait RequestParameterTrait
{
    private function getRequestParameter(Request $request, string $key): mixed
    {
        foreach ([$request->attributes, $request->query, $request->request] as $bag) {
            if ($bag->has($key)) {
                return $bag->all()[$key];
            }
        }

        return null;
    }
}
