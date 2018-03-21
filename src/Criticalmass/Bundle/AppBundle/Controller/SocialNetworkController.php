<?php

namespace Criticalmass\Bundle\AppBundle\Controller;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;
use Criticalmass\Bundle\AppBundle\Entity\Subride;
use Criticalmass\Bundle\AppBundle\Form\Type\SocialNetworkProfileType;
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
     * @ParamConverter("city", class="AppBundle:City")
     */
    public function listAction(City $city, UserInterface $user): Response
    {
        $list = $this->getDoctrine()->getRepository(SocialNetworkProfile::class)->findByCity($city);

        $socialNetworkProfile = new SocialNetworkProfile();
        $socialNetworkProfile->setCity($city);
        $addProfileForm = $this->getAddProfileForm($socialNetworkProfile);

        return $this->render('AppBundle:SocialNetwork:list.html.twig', [
            'list' => $list,
            'addProfileForm' => $addProfileForm->createView(),
        ]);
    }

    public function addAction(
        Request $request,
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
            return $this->addPostAction($request, $user, $form);
        } else {
            return $this->addGetAction($request, $user, $form);
        }
    }

    protected function addPostAction(Request $request, UserInterface $user, FormInterface $form): Response
    {
        $form->handleRequest($request);

        $hasErrors = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $socialNetworkProfile = $form->getData();

            $this->getDoctrine()->getManager()->persist($socialNetworkProfile);
        }

        return new Response('asdf');
    }

    protected function addGetAction(Request $request, UserInterface $user, FormInterface $form): Response
    {
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

    protected function detectNetwork(SocialNetworkProfile $socialNetworkProfile)
    {
        $networkDetector = $this->get(NetworkDetector::class);

        $networkDetector->detect($socialNetworkProfile);
    }
}
