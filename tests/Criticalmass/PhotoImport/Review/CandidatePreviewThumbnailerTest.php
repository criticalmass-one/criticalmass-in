<?php declare(strict_types=1);

namespace Tests\Criticalmass\PhotoImport\Review;

use App\Criticalmass\PhotoImport\Review\CandidatePreviewThumbnailer;
use PHPUnit\Framework\TestCase;

class CandidatePreviewThumbnailerTest extends TestCase
{
    /**
     * Invalid input yields null in every environment: without ImageMagick the guard
     * returns null, and with it the decode fails and is caught — so the caller can
     * always rely on the null fallback.
     */
    public function testInvalidImageYieldsNull(): void
    {
        self::assertNull((new CandidatePreviewThumbnailer())->thumbnail('this is not an image'));
    }

    public function testEmptyInputYieldsNull(): void
    {
        self::assertNull((new CandidatePreviewThumbnailer())->thumbnail(''));
    }
}
