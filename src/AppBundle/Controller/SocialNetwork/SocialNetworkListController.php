<?php

namespace AppBundle\Controller\SocialNetwork;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\City;
use AppBundle\Entity\Ride;
use AppBundle\Entity\SocialNetworkProfile;
use AppBundle\Entity\Subride;
use AppBundle\Entity\User;
use AppBundle\Form\Type\SocialNetworkProfileType;
use AppBundle\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use AppBundle\Criticalmass\SocialNetwork\NetworkDetector\NetworkDetector;
use AppBundle\Criticalmass\Util\ClassUtil;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class SocialNetworkListController extends AbstractController
{
    /**
     * @ParamConverter("city", class="AppBundle:City")
     */
    public function listCityAction(RouterInterface $router, City $city): Response
    {
        $addProfileForm = $this->getAddProfileForm($router, $city);

        return $this->render('AppBundle:SocialNetwork:list.html.twig', [
            'list' => $this->getProfileList($city),
            'addProfileForm' => $addProfileForm->createView(),
            'profileAbleType' => ClassUtil::getLowercaseShortname($city),
            'profileAble' => $city,
        ]);
    }

    /**
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function listRideAction(RouterInterface $router, Ride $ride): Response
    {
        $addProfileForm = $this->getAddProfileForm($router, $ride);

        return $this->render('AppBundle:SocialNetwork:list.html.twig', [
            'list' => $this->getProfileList($ride),
            'addProfileForm' => $addProfileForm->createView(),
            'profileAbleType' => ClassUtil::getLowercaseShortname($ride),
            'profileAble' => $ride,
        ]);
    }

    protected function getAddProfileForm(RouterInterface $router, SocialNetworkProfileAble $profileAble): FormInterface
    {
        $socialNetworkProfile = new SocialNetworkProfile();

        $setMethodName = sprintf('set%s', ClassUtil::getShortname($profileAble));
        $socialNetworkProfile->$setMethodName($profileAble);

        $form = $this->createForm(
            SocialNetworkProfileType::class,
            $socialNetworkProfile, [
                'action' => $this->getRouteName($router, $profileAble, 'add'),
            ]
        );

        return $form;
    }

    protected function getProfileList(SocialNetworkProfileAble $profileAble): array
    {
        $methodName = sprintf('findBy%s', ClassUtil::getShortname($profileAble));

        $list = $this->getDoctrine()->getRepository(SocialNetworkProfile::class)->$methodName($profileAble);

        return $list;
    }

    protected function getRouteName(RouterInterface $router, SocialNetworkProfileAble $profileAble, string $actionName): string
    {
        $routeName = sprintf('criticalmass_socialnetwork_%s_%s', ClassUtil::getLowercaseShortname($profileAble), $actionName);

        $parameters = [];

        if ($profileAble instanceof City) {
            $parameters = ['citySlug' => $profileAble->getMainSlugString()];
        } elseif ($profileAble instanceof Ride) {
            $parameters = [
                'citySlug' => $profileAble->getCity()->getMainSlugString(),
                'rideDate' => $profileAble->getFormattedDate(),
            ];
        } elseif ($profileAble instanceof Subride) {
            $parameters = [
                'citySlug' => $profileAble->getRide()->getCity()->getMainSlugString(),
                'rideDate' => $profileAble->getRide()->getFormattedDate(),
                'subrideId' => $profileAble->getId(),
            ];
        } elseif ($profileAble instanceof User) {
            $parameters = [
                'username' => $profileAble->getUsernameCanonical(),
            ];
        }

        return $router->generate($routeName, $parameters);
    }
}
