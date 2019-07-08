<?php declare(strict_types=1);

namespace App\Controller\SocialNetwork;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Entity\Ride;
use App\Entity\SocialNetworkProfile;
use App\Factory\SocialNetworkProfile\SocialNetworkProfileFactoryInterface;
use App\Form\Type\SocialNetworkProfileAddType;
use App\Form\Type\SocialNetworkProfileType;
use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use App\Criticalmass\Util\ClassUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class SocialNetworkListController extends AbstractSocialNetworkController
{
    /**
     * @ParamConverter("city", class="App:City")
     */
    public function listCityAction(ObjectRouterInterface $router, City $city): Response
    {
        $addProfileForm = $this->getAddProfileForm($router, $city);

        return $this->render('SocialNetwork/list.html.twig', [
            'list' => $this->getProfileList($city),
            'addProfileForm' => $addProfileForm->createView(),
            'profileAbleType' => ClassUtil::getLowercaseShortname($city),
            'profileAble' => $city,
        ]);
    }

    /**
     * @ParamConverter("ride", class="App:Ride")
     */
    public function listRideAction(ObjectRouterInterface $router, Ride $ride, SocialNetworkProfileFactoryInterface $socialNetworkProfileFactory, UserInterface $user = null): Response
    {
        $addProfileForm = $this->getAddProfileForm($router, $ride, $socialNetworkProfileFactory, $user);

        return $this->render('SocialNetwork/list.html.twig', [
            'list' => $this->getProfileList($ride),
            'addProfileForm' => $addProfileForm->createView(),
            'profileAbleType' => ClassUtil::getLowercaseShortname($ride),
            'profileAble' => $ride,
        ]);
    }

    protected function getAddProfileForm(ObjectRouterInterface $router, SocialNetworkProfileAble $profileAble, SocialNetworkProfileFactoryInterface $socialNetworkProfileFactory, UserInterface $user): FormInterface
    {
        $socialNetworkProfile = $socialNetworkProfileFactory
            ->withUser($user)
            ->build();

        $setMethodName = sprintf('set%s', ClassUtil::getShortname($profileAble));
        $socialNetworkProfile->$setMethodName($profileAble);

        $form = $this->createForm(
            SocialNetworkProfileAddType::class,
            $socialNetworkProfile, [
                'action' => $this->getRouteName($router, $profileAble, 'add'),
            ]
        );

        return $form;
    }
}
