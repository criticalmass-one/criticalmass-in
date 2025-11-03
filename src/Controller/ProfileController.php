<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends AbstractController
{
    public function showAction(User $user): Response
    {
        return $this->render('App:Profile:show.html.twig', [
            'userProfile' => $user,
        ]);
    }
}
