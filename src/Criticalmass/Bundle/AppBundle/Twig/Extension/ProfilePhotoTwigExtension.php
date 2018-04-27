<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Twig\Extension;

use Criticalmass\Bundle\AppBundle\Entity\User;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ProfilePhotoTwigExtension extends \Twig_Extension
{
    protected $uploaderHelper;

    public function __construct(UploaderHelper $uploaderHelper)
    {
        $this->uploaderHelper = $uploaderHelper;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('gravatarHash', [$this, 'gravatarHash'], [
                'is_safe' => ['html']
            ]),
            new \Twig_SimpleFunction('gravatarUrl', [$this, 'gravatarUrl'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function gravatarHash(User $user = null): string
    {
        if (!$user) {
            return 'avatar';
        }

        return md5($user->getEmail());
    }

    public function gravatarUrl(User $user = null, $size = 64): string
    {
        return sprintf('https://www.gravatar.com/avatar/%s?s=%d', $this->gravatarHash($user), $size);
    }

    public function getName(): string
    {
        return 'profile_photo_extension';
    }
}

