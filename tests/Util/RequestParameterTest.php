<?php declare(strict_types=1);

namespace Tests\Util;

use App\Criticalmass\Util\RequestParameter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class RequestParameterTest extends TestCase
{
    #[Test]
    public function routeAttributeWinsOverQueryAndBody(): void
    {
        $request = Request::create('/?citySlug=query', 'POST', ['citySlug' => 'body']);
        $request->attributes->set('citySlug', 'attribute');

        self::assertSame('attribute', RequestParameter::get($request, 'citySlug'));
    }

    #[Test]
    public function queryStringWinsOverBody(): void
    {
        $request = Request::create('/?citySlug=query', 'POST', ['citySlug' => 'body']);

        self::assertSame('query', RequestParameter::get($request, 'citySlug'));
    }

    #[Test]
    public function bodyIsUsedWhenNothingElseMatches(): void
    {
        $request = Request::create('/', 'POST', ['citySlug' => 'body']);

        self::assertSame('body', RequestParameter::get($request, 'citySlug'));
    }

    #[Test]
    public function defaultIsReturnedForMissingKey(): void
    {
        $request = Request::create('/');

        self::assertNull(RequestParameter::get($request, 'missing'));
        self::assertSame('fallback', RequestParameter::get($request, 'missing', 'fallback'));
    }

    #[Test]
    public function emptyStringInQueryIsStillReturnedInsteadOfDefault(): void
    {
        $request = Request::create('/?citySlug=');

        self::assertSame('', RequestParameter::get($request, 'citySlug', 'fallback'));
    }

    #[Test]
    public function arrayQueryParametersAreReturnedAsArrays(): void
    {
        $request = Request::create('/?ids[]=1&ids[]=2');

        self::assertSame(['1', '2'], RequestParameter::get($request, 'ids'));
    }
}
