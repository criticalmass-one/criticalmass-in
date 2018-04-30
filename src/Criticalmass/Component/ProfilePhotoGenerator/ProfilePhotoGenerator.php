<?php declare(strict_types=1);

namespace Criticalmass\Component\ProfilePhotoGenerator;

use Criticalmass\Bundle\AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Imagine\Image\Box;
use Imagine\Imagick\Imagine;
use Imagine\Image\Palette;

class ProfilePhotoGenerator
{
    /** @var User $user */
    protected $user;

    /** @var Registry $registry */
    protected $registry;

    /** @var string $projectDirectory */
    protected $projectDirectory;

    public function __construct(string $projectDirectory, Registry $registry)
    {
        $this->registry = $registry;
        $this->projectDirectory = $projectDirectory;
    }

    public function setUser(User $user): ProfilePhotoGenerator
    {
        $this->user = $user;

        return $this;
    }

    public function generate(): string
    {
        $palette = new Palette\RGB();

        $color = $palette->color([
            $this->user->getColorRed(),
            $this->user->getColorGreen(),
            $this->user->getColorBlue()
        ]);

        $imagine = new Imagine();

        $box = new Box(1024, 1024);

        $image = $imagine->create($box, $color);

        $filename = $this->generateFilename();

        $image->save();

        return $filename;
    }

    protected function generateFilename(): string
    {
        return sprintf('%s/web/users/%s.png', $this->projectDirectory, uniqid());
    }
}
