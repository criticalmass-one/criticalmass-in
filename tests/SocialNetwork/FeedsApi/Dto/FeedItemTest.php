<?php declare(strict_types=1);

namespace Tests\SocialNetwork\FeedsApi\Dto;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\FeedItem;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[TestDox('FeedsApi FeedItem DTO')]
class FeedItemTest extends TestCase
{
    private function createSampleApiResponse(): array
    {
        return [
            'id' => 42,
            'uniqueIdentifier' => 'post-12345',
            'permalink' => 'https://twitter.com/cm_hh/status/12345',
            'title' => null,
            'text' => 'Critical Mass heute Abend!',
            'dateTime' => '2026-03-15T18:00:00+01:00',
            'hidden' => false,
            'deleted' => false,
            'createdAt' => '2026-03-15T19:00:00+01:00',
            'profile' => ['id' => 7],
        ];
    }

    #[TestDox('creates FeedItem from API response')]
    public function testFromApiResponse(): void
    {
        $item = FeedItem::fromApiResponse($this->createSampleApiResponse());

        $this->assertEquals(42, $item->getId());
        $this->assertEquals('post-12345', $item->getUniqueIdentifier());
        $this->assertEquals('https://twitter.com/cm_hh/status/12345', $item->getPermalink());
        $this->assertNull($item->getTitle());
        $this->assertEquals('Critical Mass heute Abend!', $item->getText());
        $this->assertFalse($item->getHidden());
        $this->assertFalse($item->getDeleted());
        $this->assertEquals(7, $item->getProfileId());
    }

    #[TestDox('parses dateTime correctly')]
    public function testDateTimeParsing(): void
    {
        $item = FeedItem::fromApiResponse($this->createSampleApiResponse());

        $this->assertInstanceOf(\DateTimeInterface::class, $item->getDateTime());
        $this->assertEquals('2026-03-15', $item->getDateTime()->format('Y-m-d'));
    }

    #[TestDox('parses createdAt correctly')]
    public function testCreatedAtParsing(): void
    {
        $item = FeedItem::fromApiResponse($this->createSampleApiResponse());

        $this->assertInstanceOf(\DateTimeInterface::class, $item->getCreatedAt());
    }

    #[TestDox('handles nullable fields')]
    public function testNullableFields(): void
    {
        $data = $this->createSampleApiResponse();
        $data['permalink'] = null;
        $data['title'] = null;

        $item = FeedItem::fromApiResponse($data);

        $this->assertNull($item->getPermalink());
        $this->assertNull($item->getTitle());
    }

    #[TestDox('handles profile as integer instead of object')]
    public function testProfileAsInteger(): void
    {
        $data = $this->createSampleApiResponse();
        $data['profile'] = 99;

        $item = FeedItem::fromApiResponse($data);

        $this->assertEquals(99, $item->getProfileId());
    }

    #[TestDox('handles hidden and deleted flags')]
    public function testFlags(): void
    {
        $data = $this->createSampleApiResponse();
        $data['hidden'] = true;
        $data['deleted'] = true;

        $item = FeedItem::fromApiResponse($data);

        $this->assertTrue($item->getHidden());
        $this->assertTrue($item->getDeleted());
    }
}
