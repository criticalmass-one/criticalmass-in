<?php declare(strict_types=1);

namespace App\Criticalmass\ProfilePhotoGenerator;

use App\Entity\User;
use Imagine\Image\AbstractFont;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\PaletteInterface;
use Imagine\Image\Point;
use Imagine\Image\Point\Center;
use Imagine\Gd\Font;
use Imagine\Gd\Imagine;
use Imagine\Image\Palette;
use League\Flysystem\FilesystemInterface;

class ProfilePhotoGenerator implements ProfilePhotoGeneratorInterface
{
    final const FONT_FILE = '/assets/fonts/Verdana/Bold.ttf';

    /** @var User $user */
    protected $user;

    /** @var PaletteInterface $palette */
    protected $palette;

    public function __construct(protected FilesystemInterface $filesystem, protected string $projectDirectory)
    {
        $this->palette = new Palette\RGB();
    }

    public function setUser(User $user): ProfilePhotoGeneratorInterface
    {
        $this->user = $user;

        return $this;
    }

    public function generate(): string
    {
        $image = $this->createImage();

        $filename = $this->generateFilename();

        $this->writeText($image);

        $this->filesystem->put($filename, $image->get('jpeg'));

        return $filename;
    }

    protected function createImage(): ImageInterface
    {
        $imagine = new Imagine();

        $box = new Box(1024, 1024);

        return $imagine->create($box, $this->getUserBackgroundColor());
    }

    protected function writeText(ImageInterface $image): void
    {
        $text = $this->getUserInitials();
        $font = $this->getFont($image);

        $textBox = $font->box($text);
        $textCenterPosition = new Center($textBox);
        $imageCenterPosition = new Center($image->getSize());
        $centeredTextPosition = new Point(
            $imageCenterPosition->getX() - $textCenterPosition->getX(),
            $imageCenterPosition->getY() - $textCenterPosition->getY()
        );

        $image->draw()->text($text, $font, $centeredTextPosition);
    }

    protected function getUserInitials(): string
    {
        $parts = explode(' ', $this->user->getUsername());

        if (2 === count($parts)) {
            $initials = sprintf('%s%s', $parts[0][0], $parts[1][0]);
        } else {
            $initials = strtoupper(substr($this->user->getUsername(), 0, 2));
        }

        return $initials;
    }

    protected function getFont(ImageInterface $image): AbstractFont
    {
        $fontColor = $this->palette->color('fff');
        $fontSize = 256;
        $fontFilename = sprintf('%s%s', $this->projectDirectory, self::FONT_FILE);

        return new Font($fontFilename, $fontSize, $fontColor);
    }

    protected function getUserBackgroundColor(): ColorInterface
    {
        return $this->palette->color([
            $this->user->getColorRed(),
            $this->user->getColorGreen(),
            $this->user->getColorBlue()
        ]);
    }

    protected function generateFilename(): string
    {
        $filename = sprintf('%s.jpg', uniqid('', true));

        $this->user->setImageName($filename);

        return $filename;
    }
}
