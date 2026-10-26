<?php declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\Post;
use App\Enum\PostKindEnum;
use PHPUnit\Framework\TestCase;

class PostKindTest extends TestCase
{
    public function testNewPostsAreComments(): void
    {
        self::assertSame(PostKindEnum::COMMENT, (new Post())->getKind());
    }

    public function testKindCanBeChanged(): void
    {
        $post = (new Post())->setKind(PostKindEnum::STATUS);

        self::assertSame(PostKindEnum::STATUS, $post->getKind());
    }

    /**
     * Die Migration legt einen CHECK mit genau diesen Werten an. Kommt ein
     * Fall hinzu, schlaegt dieser Test fehl und erinnert an die Migration.
     */
    public function testValuesMatchTheDatabaseCheck(): void
    {
        self::assertSame(
            ['COMMENT', 'STATUS', 'ARTICLE'],
            array_map(fn(PostKindEnum $kind) => $kind->value, PostKindEnum::cases())
        );
    }

    public function testValuesFitTheColumn(): void
    {
        foreach (PostKindEnum::cases() as $kind) {
            self::assertLessThanOrEqual(16, strlen($kind->value));
        }
    }

    public function testChoicesMapValuesToLabels(): void
    {
        self::assertSame([
            'COMMENT' => 'Kommentar',
            'STATUS' => 'Statusbeitrag',
            'ARTICLE' => 'Artikel',
        ], PostKindEnum::choices());
    }
}
