<?php declare(strict_types=1);

namespace App\Controller\SocialNetwork;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\Util\ClassUtil;
use App\Entity\SocialNetworkProfile;
use App\Form\Type\SocialNetworkProfileEditType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SocialNetworkManagementController extends AbstractSocialNetworkController
{
    /**
     * @ParamConverter("socialNetworkProfile", class="App:SocialNetworkProfile", options={"id" = "profileId"})
     */
    public function editAction(Request $request, SocialNetworkProfile $socialNetworkProfile, ObjectRouterInterface $objectRouter): Response
    {
        $form = $this->createForm(
            SocialNetworkProfileEditType::class,
            $socialNetworkProfile
        );

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->editPostAction($request, $form, $objectRouter);
        } else {
            return $this->editGetAction($request, $form, $objectRouter);
        }
    }

    protected function editPostAction(
        Request $request,
        FormInterface $form,
        ObjectRouterInterface $objectRouter
    ): Response {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SocialNetworkProfile $socialNetworkProfile */
            $socialNetworkProfile = $form->getData();

            $this->getDoctrine()->getManager()->persist($socialNetworkProfile);

            $this->getDoctrine()->getManager()->flush();

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');
        }

        return $this->redirect($objectRouter->generate($socialNetworkProfile->getCity(), 'criticalmass_socialnetwork_city_list'));
    }

    protected function editGetAction(
        Request $request,
        FormInterface $form,
        ObjectRouterInterface $objectRouter
    ): Response {
        $socialNetworkProfile = $form->getData();

        return $this->render('SocialNetwork/edit.html.twig', [
                'form' => $form->createView(),
                'profileAbleType' => ClassUtil::getLowercaseShortname($this->getProfileAble($socialNetworkProfile)),
                'profileAble' => $this->getProfileAble($socialNetworkProfile),
            ]
        );
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
