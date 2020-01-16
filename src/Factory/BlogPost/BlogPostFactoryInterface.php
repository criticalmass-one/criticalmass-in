<?php declare(strict_types=1);

namespace App\Factory\BlogPost;

use App\Entity\BlogPost;
use Symfony\Component\Security\Core\User\UserInterface;

interface BlogPostFactoryInterface
{
    public function withUser(UserInterface $user): BlogPostFactoryInterface;
    public function build(): BlogPost;
}