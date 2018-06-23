<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ProfileController extends AbstractController
{
    /**
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function showAction(User $user): Response
    {
        return $this->render('AppBundle:Profile:show.html.twig', [
            'userProfile' => $user,
        ]);
    }
}
