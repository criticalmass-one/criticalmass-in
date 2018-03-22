<?php

namespace Criticalmass\Bundle\AppBundle\Controller;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;
use Criticalmass\Bundle\AppBundle\Entity\Subride;
use Criticalmass\Bundle\AppBundle\Entity\User;
use Criticalmass\Bundle\AppBundle\Form\Type\SocialNetworkProfileType;
use Criticalmass\Component\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use Criticalmass\Component\SocialNetwork\FeedFetcher\HomepageFeedFetcher;
use Criticalmass\Component\SocialNetwork\FeedFetcher\TwitterFeedFetcher;
use Criticalmass\Component\SocialNetwork\NetworkDetector\NetworkDetector;
use Criticalmass\Component\SocialNetwork\NetworkManager\NetworkManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class SocialNetworkController extends AbstractController
{
    /**
     * @ParamConverter("city", class="AppBundle:City", isOptional=true)
     * @ParamConverter("ride", class="AppBundle:Ride", isOptional=true)
     */
    public function listAction(
        City $city = null,
        Ride $ride = null,
        Subride $subride = null,
        User $user = null
    ): Response {
        $profileAble = $this->getProfileAbleObject($ride, $subride, $city, $user);

        $socialNetworkProfile = new SocialNetworkProfile();
        $socialNetworkProfile->setCity($city);
        $addProfileForm = $this->getAddProfileForm($socialNetworkProfile);

        return $this->render('AppBundle:SocialNetwork:list.html.twig', [
            'list' => $this->getProfileList($profileAble),
            'addProfileForm' => $addProfileForm->createView(),
        ]);
    }

    /**
     * @ParamConverter("city", class="AppBundle:City", isOptional=true)
     */
    public function addAction(
        Request $request,
        NetworkDetector $networkDetector,
        UserInterface $user,
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
            return $this->addPostAction($request, $user, $form, $networkDetector);
        } else {
            return $this->addGetAction($request, $user, $form, $networkDetector);
        }
    }

    protected function addPostAction(
        Request $request,
        UserInterface $user,
        FormInterface $form,
        NetworkDetector $networkDetector
    ): Response {
        $form->handleRequest($request);

        $hasErrors = null;

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SocialNetworkProfile $socialNetworkProfile */
            $socialNetworkProfile = $form->getData();

            $network = $networkDetector->detect($socialNetworkProfile);

            if ($network) {
                $socialNetworkProfile->setNetwork($network->getIdentifier());
            }

            $this->getDoctrine()->getManager()->persist($socialNetworkProfile);

            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('criticalmass_socialnetwork_list_city', [
            'citySlug' => $socialNetworkProfile->getCity()->getMainSlugString(),
        ]);
    }

    protected function addGetAction(
        Request $request,
        UserInterface $user,
        FormInterface $form,
        NetworkDetector $networkDetector
    ): Response {
        return $this->render('AppBundle:SocialNetwork:edit.html.twig', [
                'form' => $form->createView(),
            ]
        );
    }

    protected function getAddProfileForm(SocialNetworkProfile $socialNetworkProfile): FormInterface
    {
        $form = $this->createForm(
            SocialNetworkProfileType::class,
            $socialNetworkProfile, [
                'action' => $this->generateUrl('criticalmass_socialnetwork_add_city', [
                    'citySlug' => $socialNetworkProfile->getCity()->getMainSlugString(),
                ])
            ]
        );

        return $form;
    }

    protected function getProfileAbleObject(
        Ride $ride = null,
        Subride $subride = null,
        City $city = null,
        User $user = null
    ): SocialNetworkProfileAble {
        return $user ?? $city ?? $subride ?? $ride;
    }

    protected function getProfileList(SocialNetworkProfileAble $profileAble): array
    {
        $reflection = new \ReflectionClass($profileAble);

        $methodName = sprintf('findBy%s', $reflection->getShortName());

        $list = $this->getDoctrine()->getRepository(SocialNetworkProfile::class)->$methodName($profileAble);

        return $list;
    }
}
