<?php declare(strict_types=1);

namespace Tests\Strava\Token;

use App\Criticalmass\Strava\Token\StravaTokenStorage;
use PHPUnit\Framework\TestCase;

class StravaTokenStorageTest extends TestCase
{
    public function testCreatesTheTokenFromAValidResponse(): void
    {
        $token = StravaTokenStorage::createFromStravaResponse((object) [
            'access_token' => 'access',
            'refresh_token' => 'refresh',
            'expires_at' => 1760000000,
        ]);

        $this->assertSame('access', $token->getAccessToken());
        $this->assertSame('refresh', $token->getRefreshToken());
        $this->assertSame(1760000000, $token->getExpiresAt());
    }

    public function testRejectsAnErrorResponse(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        StravaTokenStorage::createFromStravaResponse((object) [
            'message' => 'Bad Request',
            'errors' => [['resource' => 'AuthorizationCode', 'field' => 'code', 'code' => 'invalid']],
        ]);
    }
}
