<?php declare(strict_types=1);

namespace App\Controller\SocialNetwork;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\SocialNetwork\EntityNetworkDetector\EntityNetworkDetectorInterface;
use App\Criticalmass\SocialNetwork\Helper\SocialNetworkHelperInterface;
use App\Criticalmass\Util\ClassUtil;
use App\Entity\City;
use App\Entity\Ride;
use App\Entity\SocialNetworkProfile;
use App\EntityInterface\SocialNetworkProfileAble;
use App\Factory\SocialNetworkProfile\SocialNetworkProfileFactoryInterface;
use App\Form\Type\SocialNetworkProfileAddType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class SocialNetworkController extends AbstractController
{
    private const string DEFAULT_NETWORK = 'homepage';

    public function addAction(
        Request $request,
        EntityNetworkDetectorInterface $networkDetector,
        ObjectRouterInterface $objectRouter,
        SocialNetworkProfileFactoryInterface $networkProfileFactory,
        SocialNetworkHelperInterface $socialNetworkHelper,
        City $city = null,
        Ride $ride = null,
        UserInterface $user = null
    ): Response {
        $socialNetworkProfile = $networkProfileFactory
            ->withCreatedBy($user)
            ->build()
        ;

        if ($city) {
            $socialNetworkProfile->setCity($city);
        } elseif ($ride) {
            $socialNetworkProfile->setRide($ride);
        }

        $form = $this->createForm(
            SocialNetworkProfileAddType::class,
            $socialNetworkProfile
        );

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->addPostAction($request, $form, $networkDetector, $objectRouter, $socialNetworkHelper, $city, $ride);
        } else {
            return $this->addGetAction($request, $form, $networkDetector, $objectRouter, $socialNetworkHelper, $city, $ride);
        }
    }

    protected function addPostAction(
        Request $request,
        FormInterface $form,
        EntityNetworkDetectorInterface $networkDetector,
        ObjectRouterInterface $objectRouter,
        SocialNetworkHelperInterface $socialNetworkHelper,
        City $city = null,
        Ride $ride = null
    ): Response {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SocialNetworkProfile $socialNetworkProfile */
            $socialNetworkProfile = $form->getData();

            if ($city) {
                $socialNetworkProfile->setCity($city);
            } elseif ($ride) {
                $socialNetworkProfile->setRide($ride);
            }

            $network = $networkDetector->detect($socialNetworkProfile);

            if ($network) {
                $socialNetworkProfile->setNetwork($network->getIdentifier());
            } else {
                $socialNetworkProfile->setNetwork(self::DEFAULT_NETWORK);
            }

            $this->managerRegistry->getManager()->persist($socialNetworkProfile);

            $this->managerRegistry->getManager()->flush();

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');

            $routeName = sprintf('criticalmass_socialnetwork_%s_list', ClassUtil::getLowercaseShortname($socialNetworkHelper->getProfileAble($socialNetworkProfile)));

            return $this->redirect($objectRouter->generate($socialNetworkHelper->getProfileAble($socialNetworkProfile), $routeName));
        }

        return $this->addGetAction($request, $form, $networkDetector, $objectRouter, $socialNetworkHelper);
    }

    protected function addGetAction(
        Request $request,
        FormInterface $form,
        EntityNetworkDetectorInterface $networkDetector,
        ObjectRouterInterface $objectRouter,
        SocialNetworkHelperInterface $socialNetworkHelper,
        City $city = null,
        Ride $ride = null
    ): Response {
        $socialNetworkProfile = $form->getData();

        return $this->render('SocialNetwork/add.html.twig', [
                'form' => $form->createView(),
                'profileAbleType' => ClassUtil::getLowercaseShortname($socialNetworkHelper->getProfileAble($socialNetworkProfile)),
                'profileAble' => $socialNetworkHelper->getProfileAble($socialNetworkProfile),
            ]
        );
    }

    protected function getAddProfileForm(ObjectRouterInterface $router, SocialNetworkProfileAble $profileAble, SocialNetworkHelperInterface $socialNetworkHelper): FormInterface
    {
        $socialNetworkProfile = new SocialNetworkProfile();

        $setMethodName = sprintf('set%s', $socialNetworkHelper->getProfileAbleShortname($profileAble));
        $socialNetworkProfile->$setMethodName($profileAble);

        $form = $this->createForm(
            SocialNetworkProfileAddType::class,
            $socialNetworkProfile, [
                'action' => $socialNetworkHelper->getRouteName($socialNetworkHelper->getProfileAble($socialNetworkProfile), 'add'),
            ]
        );

        return $form;
    }
}
