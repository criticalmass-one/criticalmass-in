<?php declare(strict_types=1);

namespace App\Controller\SocialNetwork;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\Util\ClassUtil;
use App\Entity\City;
use App\Entity\Ride;
use App\Entity\SocialNetworkProfile;
use App\Entity\Subride;
use App\Entity\User;
use App\Form\Type\SocialNetworkProfileType;
use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use App\Criticalmass\SocialNetwork\NetworkDetector\NetworkDetector;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SocialNetworkController extends AbstractController
{
    /**
     * @ParamConverter("city", class="App:City", isOptional=true)
     * @ParamConverter("ride", class="App:Ride", isOptional=true)
     * @ParamConverter("subride", class="App:Subride", isOptional=true)
     * @ParamConverter("user", class="App:User", isOptional=true)
     */
    public function listAction(
        ObjectRouterInterface $router,
        City $city = null,
        Ride $ride = null,
        Subride $subride = null,
        User $user = null
    ): Response {
        $profileAble = $this->getProfileAbleObject($ride, $subride, $city, $user);

        $addProfileForm = $this->getAddProfileForm($router, $profileAble);

        return $this->render('SocialNetwork/list.html.twig', [
            'list' => $this->getProfileList($profileAble),
            'addProfileForm' => $addProfileForm->createView(),
            'profileAbleType' => strtolower($this->getProfileAbleShortname($profileAble)),
            'profileAble' => $profileAble,
        ]);
    }

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

    protected function getProfileAbleObject(
        Ride $ride = null,
        Subride $subride = null,
        City $city = null,
        User $user = null
    ): SocialNetworkProfileAble {
        $profileAble = $user ?? $ride ?? $city ?? $subride;

        return $profileAble;
    }

    protected function getProfileAble(SocialNetworkProfile $socialNetworkProfile): SocialNetworkProfileAble
    {
        return $socialNetworkProfile->getUser() ?? $socialNetworkProfile->getRide() ?? $socialNetworkProfile->getCity() ?? $socialNetworkProfile->getSubride();
    }

    protected function getProfileAbleShortname(SocialNetworkProfileAble $profileAble): string
    {
        $reflection = new \ReflectionClass($profileAble);

        return $reflection->getShortName();
    }

    protected function getProfileList(SocialNetworkProfileAble $profileAble): array
    {
        $methodName = sprintf('findBy%s', $this->getProfileAbleShortname($profileAble));

        $list = $this->getDoctrine()->getRepository(SocialNetworkProfile::class)->$methodName($profileAble);

        return $list;
    }

    protected function getRouteName(ObjectRouterInterface $router, SocialNetworkProfileAble $profileAble, string $actionName): string
    {
        $routeName = sprintf('criticalmass_socialnetwork_%s_%s', ClassUtil::getLowercaseShortname($profileAble), $actionName);

        return $router->generate($profileAble, $routeName);
    }
}
