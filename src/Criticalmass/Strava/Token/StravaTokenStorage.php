<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\Token;

use Iamstuartwilson\StravaApi;

class StravaTokenStorage
{
    /** @var string $accessToken */
    protected $accessToken;

    /** @var string $refreshToken */
    protected $refreshToken;

    /** @var int $expiresAt */
    protected $expiresAt;

    public static function createFromStravaResponse(\stdClass $response): self
    {
        return new self(
            (string)$response->access_token,
            (string)$response->refresh_token,
            (int)$response->expires_at
        );
    }

    public static function setAccessToken(StravaApi $stravaApi, StravaTokenStorage $stravaTokenStorage): StravaApi
    {
        $stravaApi->setAccessToken(
            $stravaTokenStorage->getAccessToken(),
            $stravaTokenStorage->getRefreshToken(),
            $stravaTokenStorage->getExpiresAt()
        );

        return $stravaApi;
    }

    public function __construct(string $accessToken, string $refreshToken, int $expiresAt)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiresAt = $expiresAt;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getExpiresAt(): int
    {
        return $this->expiresAt;
    }


}