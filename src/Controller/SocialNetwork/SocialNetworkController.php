<?php declare(strict_types=1);

namespace App\Controller\SocialNetwork;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\SocialNetwork\EntityNetworkDetector\EntityNetworkDetectorInterface;
use App\Criticalmass\SocialNetwork\Helper\SocialNetworkHelperInterface;
use App\Criticalmass\Util\ClassUtil;
use App\Entity\SocialNetworkProfile;
use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use App\Factory\SocialNetworkProfile\SocialNetworkProfileFactoryInterface;
use App\Form\Type\SocialNetworkProfileAddType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class SocialNetworkController extends AbstractController
{
    /**
     * @ParamConverter("city", class="App:City", isOptional=true)
     * @ParamConverter("ride", class="App:Ride", isOptional=true)
     * @ParamConverter("subride", class="App:Subride", isOptional=true)
     * @ParamConverter("user", class="App:User", isOptional=true)
     */
    public function addAction(
        Request $request,
        EntityNetworkDetectorInterface $networkDetector,
        ObjectRouterInterface $objectRouter,
        SocialNetworkProfileFactoryInterface $networkProfileFactory,
        SocialNetworkHelperInterface $socialNetworkHelper,
        UserInterface $user = null
    ): Response {
        $socialNetworkProfile = $networkProfileFactory
            ->withCreatedBy($user)
            ->build();

        $socialNetworkProfile = $socialNetworkHelper->assignProfileAble($socialNetworkProfile, $request);

        $form = $this->createForm(
            SocialNetworkProfileAddType::class,
            $socialNetworkProfile
        );

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->addPostAction($request, $form, $networkDetector, $objectRouter, $socialNetworkHelper);
        } else {
            return $this->addGetAction($request, $form, $networkDetector, $objectRouter, $socialNetworkHelper);
        }
    }

    protected function addPostAction(
        Request $request,
        FormInterface $form,
        EntityNetworkDetectorInterface $networkDetector,
        ObjectRouterInterface $objectRouter,
        SocialNetworkHelperInterface $socialNetworkHelper
    ): Response {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SocialNetworkProfile $socialNetworkProfile */
            $socialNetworkProfile = $form->getData();

            $socialNetworkProfile = $socialNetworkHelper->assignProfileAble($socialNetworkProfile, $request);

            $network = $networkDetector->detect($socialNetworkProfile);

            if ($network) {
                $socialNetworkProfile->setNetwork($network->getIdentifier());
            }

            $this->getDoctrine()->getManager()->persist($socialNetworkProfile);

            $this->getDoctrine()->getManager()->flush();

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
        SocialNetworkHelperInterface $socialNetworkHelper
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
