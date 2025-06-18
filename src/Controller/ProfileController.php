<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ProfileController extends AbstractController
{
    /**
     * @ParamConverter("user", class="App:User")
     */
    public function showAction(User $user): Response
    {
        return $this->render('App:Profile:show.html.twig', [
            'userProfile' => $user,
        ]);
    }
}
