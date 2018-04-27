<?php declare(strict_types=1);

namespace Criticalmass\Component\ProfilePhotoGenerator;

use Criticalmass\Bundle\AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;

class ProfilePhotoGenerator
{
    protected $user;

    protected $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function setUser(User $user): ProfilePhotoGenerator
    {
        $this->user = $user;

        return $this;
    }

    public function generate(): ProfilePhotoGenerator
    {
        return $this;
    }
}
