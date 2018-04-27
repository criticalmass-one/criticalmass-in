<?php

namespace Criticalmass\Bundle\AppBundle\Twig\Extension;

use Criticalmass\Bundle\AppBundle\Entity\User;

class ProfilePhotoTwigExtension extends \Twig_Extension
{
    public function getFunctions()
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

