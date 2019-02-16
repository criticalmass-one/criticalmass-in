<?php declare(strict_types=1);

namespace App\Controller\SocialNetwork;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Entity\Ride;
use App\Entity\SocialNetworkProfile;
use App\Entity\Subride;
use App\Entity\User;
use App\Form\Type\SocialNetworkProfileType;
use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use App\Criticalmass\SocialNetwork\NetworkDetector\NetworkDetector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SocialNetworkController extends AbstractSocialNetworkController
{
    /**
     * @ParamConverter("city", class="App:City", isOptional=true)
     */
    public function addAction(
        Request $request,
        NetworkDetector $networkDetector,
        ObjectRouterInterface $objectRouter,
        User $user = null,
        City $city = null,
        Ride $ride = null,
        Subride $subride = null
    ): Response {
        $socialNetworkProfile = new SocialNetworkProfile();

        $socialNetworkProfile
            ->setUser($user)
            ->setCity($city)
            ->setRide($ride)
            ->setSubride($subride);

        $form = $this->createForm(
            SocialNetworkProfileType::class,
            $socialNetworkProfile
        );

        if (Request::METHOD_POST == $request->getMethod()) {
            return $this->addPostAction($request, $form, $networkDetector, $objectRouter);
        } else {
            return $this->addGetAction($request, $form, $networkDetector, $objectRouter);
        }
    }

    protected function addPostAction(
        Request $request,
        FormInterface $form,
        NetworkDetector $networkDetector,
        ObjectRouterInterface $objectRouter
    ): Response {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SocialNetworkProfile $socialNetworkProfile */
            $socialNetworkProfile = $form->getData();

            $network = $networkDetector->detect($socialNetworkProfile);

            if ($network) {
                $socialNetworkProfile->setNetwork($network->getIdentifier());
            }

            $this->getDoctrine()->getManager()->persist($socialNetworkProfile);

            $this->getDoctrine()->getManager()->flush();

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');
        }

        return $this->redirect($objectRouter->generate($socialNetworkProfile->getCity(), 'criticalmass_socialnetwork_city_list'));
    }

    protected function addGetAction(
        Request $request,
        FormInterface $form,
        NetworkDetector $networkDetector,
        ObjectRouterInterface $objectRouter
    ): Response {
        return $this->render('SocialNetwork/edit.html.twig', [
                'form' => $form->createView(),
            ]
        );
    }

    protected function getAddProfileForm(ObjectRouterInterface $router, SocialNetworkProfileAble $profileAble): FormInterface
    {
        $socialNetworkProfile = new SocialNetworkProfile();

        $setMethodName = sprintf('set%s', $this->getProfileAbleShortname($profileAble));
        $socialNetworkProfile->$setMethodName($profileAble);

        $form = $this->createForm(
            SocialNetworkProfileType::class,
            $socialNetworkProfile, [
                'action' => $this->getRouteName($router, $this->getProfileAble($socialNetworkProfile), 'add'),
            ]
        );

        return $form;
    }
}
