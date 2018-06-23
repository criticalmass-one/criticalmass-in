<?php declare(strict_types=1);

namespace Criticalmass\Component\ProfilePhotoGenerator;

use Criticalmass\Bundle\AppBundle\Entity\User;

interface ProfilePhotoGeneratorInterface
{
    public function setUser(User $user): ProfilePhotoGeneratorInterface;
    public function generate(): string;
}
