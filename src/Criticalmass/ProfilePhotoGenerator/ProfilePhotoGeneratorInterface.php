<?php declare(strict_types=1);

namespace App\Criticalmass\ProfilePhotoGenerator;

use App\Entity\User;

interface ProfilePhotoGeneratorInterface
{
    public function setUser(User $user): ProfilePhotoGeneratorInterface;
    public function generate(): string;
}
