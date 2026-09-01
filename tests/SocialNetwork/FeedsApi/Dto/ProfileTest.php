<?php declare(strict_types=1);

namespace Tests\SocialNetwork\FeedsApi\Dto;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\Profile;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[TestDox('FeedsApi Profile DTO')]
class ProfileTest extends TestCase
{
    private function createSampleApiResponse(): array
    {
        return [
            'id' => 15,
            'identifier' => 'https://twitter.com/criticalmassHH',
            'network' => [
                'id' => 108,
                'identifier' => 'twitter',
                'name' => 'Twitter',
            ],
            'createdAt' => '2026-01-15T12:00:00+01:00',
            'lastFetchSuccessDateTime' => '2026-03-15T10:00:00+01:00',
            'lastFetchFailureDateTime' => null,
            'lastFetchFailureError' => null,
            'autoFetch' => true,
        ];
    }

    #[TestDox('creates Profile from API response')]
    public function testFromApiResponse(): void
    {
        $profile = Profile::fromApiResponse($this->createSampleApiResponse());

        $this->assertEquals(15, $profile->getId());
        $this->assertEquals('https://twitter.com/criticalmassHH', $profile->getIdentifier());
        $this->assertEquals('twitter', $profile->getNetworkIdentifier());
        $this->assertEquals(108, $profile->getNetworkId());
        $this->assertTrue($profile->isAutoFetch());
    }

    #[TestDox('parses datetime fields correctly')]
    public function testDateTimeParsing(): void
    {
        $profile = Profile::fromApiResponse($this->createSampleApiResponse());

        $this->assertInstanceOf(\DateTimeInterface::class, $profile->getCreatedAt());
        $this->assertInstanceOf(\DateTimeInterface::class, $profile->getLastFetchSuccessDateTime());
        $this->assertNull($profile->getLastFetchFailureDateTime());
        $this->assertNull($profile->getLastFetchFailureError());
    }

    #[TestDox('handles missing network data')]
    public function testMissingNetworkData(): void
    {
        $data = $this->createSampleApiResponse();
        unset($data['network']);

        $profile = Profile::fromApiResponse($data);

        $this->assertNull($profile->getNetworkIdentifier());
        $this->assertNull($profile->getNetworkId());
    }

    #[TestDox('handles fetch failure data')]
    public function testFetchFailure(): void
    {
        $data = $this->createSampleApiResponse();
        $data['lastFetchFailureDateTime'] = '2026-03-14T08:00:00+01:00';
        $data['lastFetchFailureError'] = 'Connection timeout';

        $profile = Profile::fromApiResponse($data);

        $this->assertInstanceOf(\DateTimeInterface::class, $profile->getLastFetchFailureDateTime());
        $this->assertEquals('Connection timeout', $profile->getLastFetchFailureError());
    }

    #[TestDox('defaults autoFetch to true when missing')]
    public function testAutoFetchDefault(): void
    {
        $data = $this->createSampleApiResponse();
        unset($data['autoFetch']);

        $profile = Profile::fromApiResponse($data);

        $this->assertTrue($profile->isAutoFetch());
    }
}
