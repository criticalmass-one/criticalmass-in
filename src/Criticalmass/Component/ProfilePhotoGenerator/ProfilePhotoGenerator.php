<?php declare(strict_types=1);

namespace Criticalmass\Component\ProfilePhotoGenerator;

use Criticalmass\Bundle\AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Imagick;
use Imagine\Image\AbstractFont;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\PaletteInterface;
use Imagine\Image\Point;
use Imagine\Image\Point\Center;
use Imagine\Imagick\Font;
use Imagine\Imagick\Imagine;
use Imagine\Image\Palette;

class ProfilePhotoGenerator
{
    const FONT_FILE = '/app/Resources/fonts/Verdana/Bold.ttf';

    /** @var User $user */
    protected $user;

    /** @var Registry $registry */
    protected $registry;

    /** @var string $projectDirectory */
    protected $projectDirectory;

    /** @var PaletteInterface $palette */
    protected $palette;

    /** @var \Imagick $imagick */
    protected $imagick;

    public function __construct(string $projectDirectory, Registry $registry)
    {
        $this->registry = $registry;
        $this->projectDirectory = $projectDirectory;
        $this->palette = new Palette\RGB();
        $this->imagick = new Imagick();
    }

    public function setUser(User $user): ProfilePhotoGenerator
    {
        $this->user = $user;

        return $this;
    }

    public function generate(): string
    {
        $image = $this->createImage();

        $filename = $this->generateFilePath();

        $this->writeText($image);

        $image->save($filename);

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
        $fontFilename = sprintf('%s', $this->projectDirectory, self::FONT_FILE);

        $font = new Font($this->imagick, $fontFilename, $fontSize, $fontColor);

        return $font;
    }

    protected function getUserBackgroundColor(): ColorInterface
    {
        return $this->palette->color([
            $this->user->getColorRed(),
            $this->user->getColorGreen(),
            $this->user->getColorBlue()
        ]);
    }

    protected function generateFilePath(): string
    {
        $filename = sprintf('%s.png', uniqid());

        $this->user->setImageName($filename);

        return sprintf('%s/web/users/%s', $this->projectDirectory, $filename);
    }
}
