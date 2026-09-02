<?php declare(strict_types=1);

namespace Tests\Criticalmass\Forum;

use App\Criticalmass\Forum\SearchSnippet;
use PHPUnit\Framework\TestCase;

class SearchSnippetTest extends TestCase
{
    private SearchSnippet $snippet;

    protected function setUp(): void
    {
        $this->snippet = new SearchSnippet();
    }

    public function testMarksTheSearchTerm(): void
    {
        $result = $this->snippet->build('Wir treffen uns am Rathaus.', 'Rathaus');

        self::assertStringContainsString('<mark>Rathaus</mark>', $result);
    }

    public function testMarkingIgnoresCase(): void
    {
        $result = $this->snippet->build('Das RATHAUS ist der Treffpunkt.', 'rathaus');

        self::assertStringContainsString('<mark>RATHAUS</mark>', $result, 'Die Schreibweise des Fundes bleibt erhalten.');
    }

    public function testEscapesTheTextBeforeMarking(): void
    {
        $result = $this->snippet->build('<script>alert(1)</script> Rathaus', 'Rathaus');

        self::assertStringNotContainsString('<script>', $result);
        self::assertStringContainsString('&lt;script&gt;', $result);
        self::assertStringContainsString('<mark>Rathaus</mark>', $result);
    }

    public function testATermWithMarkupCannotInjectHtml(): void
    {
        $result = $this->snippet->build('Ein ganz normaler Beitrag.', '<b>');

        self::assertStringNotContainsString('<b>', $result);
    }

    public function testCutsLongTextAroundTheHit(): void
    {
        $text = str_repeat('Fülltext ', 200) . 'Nadel' . str_repeat(' Fülltext', 200);

        $result = $this->snippet->build($text, 'Nadel');

        self::assertStringContainsString('<mark>Nadel</mark>', $result);
        self::assertLessThan(mb_strlen($text), mb_strlen($result));
        self::assertStringStartsWith('…', $result, 'Ein abgeschnittener Anfang wird kenntlich gemacht.');
    }

    public function testShortTextStaysWhole(): void
    {
        $result = $this->snippet->build('Kurzer Beitrag mit Nadel.', 'Nadel');

        self::assertStringStartsWith('Kurzer', $result);
    }

    public function testCollapsesWhitespace(): void
    {
        $result = $this->snippet->build("Zeile eins\n\n   Zeile zwei", 'Zeile');

        self::assertStringNotContainsString("\n", $result);
    }

    public function testEmptyTextGivesEmptySnippet(): void
    {
        self::assertSame('', $this->snippet->build(null, 'egal'));
        self::assertSame('', $this->snippet->build('   ', 'egal'));
    }

    public function testTextWithoutTheTermIsStillReturned(): void
    {
        $result = $this->snippet->build('Hier steht etwas anderes.', 'Nadel');

        self::assertStringContainsString('etwas anderes', $result);
        self::assertStringNotContainsString('<mark>', $result);
    }
}
