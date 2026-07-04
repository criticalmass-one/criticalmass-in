<?php declare(strict_types=1);

namespace Tests\Criticalmass\Upload;

use App\Criticalmass\Upload\Handler\PhotoUploadHandler;
use App\Criticalmass\Upload\Handler\TrackUploadHandler;
use App\Criticalmass\Upload\UploadDispatcher;
use App\Criticalmass\Upload\UploadResult;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UploadDispatcherTest extends TestCase
{
    /**
     * @return iterable<string, array{0: string}>
     */
    public static function trackExtensions(): iterable
    {
        yield 'gpx' => ['ride.gpx'];
        yield 'fit' => ['ride.FIT'];
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function imageExtensions(): iterable
    {
        yield 'jpg' => ['photo.jpg'];
        yield 'jpeg' => ['photo.JPEG'];
        yield 'png' => ['photo.png'];
        yield 'webp' => ['photo.webp'];
        yield 'gif' => ['photo.gif'];
        yield 'heic' => ['photo.heic'];
        yield 'heif' => ['photo.heif'];
    }

    /**
     * @dataProvider trackExtensions
     */
    public function testTracksAreRoutedToTheTrackHandler(string $originalName): void
    {
        $expected = new UploadResult(UploadResult::KIND_TRACK, UploadResult::STATUS_PARKED, '…');

        $trackHandler = $this->createMock(TrackUploadHandler::class);
        $trackHandler->expects(self::once())->method('handle')->willReturn($expected);

        $photoHandler = $this->createMock(PhotoUploadHandler::class);
        $photoHandler->expects(self::never())->method('handle');

        $result = (new UploadDispatcher($trackHandler, $photoHandler))->dispatch('/tmp/x', $originalName, $this->createMock(User::class));

        self::assertSame($expected, $result);
    }

    /**
     * @dataProvider imageExtensions
     */
    public function testImagesAreRoutedToThePhotoHandler(string $originalName): void
    {
        $expected = new UploadResult(UploadResult::KIND_PHOTO, UploadResult::STATUS_STAGED, '…');

        $photoHandler = $this->createMock(PhotoUploadHandler::class);
        $photoHandler->expects(self::once())->method('handle')->willReturn($expected);

        $trackHandler = $this->createMock(TrackUploadHandler::class);
        $trackHandler->expects(self::never())->method('handle');

        $result = (new UploadDispatcher($trackHandler, $photoHandler))->dispatch('/tmp/x', $originalName, $this->createMock(User::class));

        self::assertSame($expected, $result);
    }

    public function testUnsupportedExtensionThrows(): void
    {
        $dispatcher = new UploadDispatcher(
            $this->createMock(TrackUploadHandler::class),
            $this->createMock(PhotoUploadHandler::class),
        );

        $this->expectException(\RuntimeException::class);

        $dispatcher->dispatch('/tmp/x', 'document.pdf', $this->createMock(User::class));
    }
}
