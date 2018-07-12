<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\ProfilePhotoGenerator;

use AppBundle\Entity\User;

interface ProfilePhotoGeneratorInterface
{
    public function setUser(User $user): ProfilePhotoGeneratorInterface;
    public function generate(): string;
}
