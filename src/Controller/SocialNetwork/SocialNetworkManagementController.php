<?php declare(strict_types=1);

namespace App\Controller\SocialNetwork;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\SocialNetworkProfile;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class SocialNetworkManagementController extends AbstractSocialNetworkController
{
    /**
     * @ParamConverter("networkProfile", class="App:SocialNetworkProfile", options={"id" = "profileId"})
     */
    public function editAction(SocialNetworkProfile $networkProfile): Response
    {
        return new Response('wefwef');
    }

    /**
     * @ParamConverter("socialNetworkProfile", class="App:SocialNetworkProfile", options={"id" = "profileId"})
     */
    public function disableAction(
        ObjectRouterInterface $router,
        EntityManagerInterface $entityManager,
        SocialNetworkProfile $socialNetworkProfile
    ): Response {
        $socialNetworkProfile->setEnabled(false);

        $entityManager->flush();

        return $this->redirect($this->getRouteName($router, $this->getProfileAble($socialNetworkProfile), 'list'));
    }

}
