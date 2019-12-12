<?php declare(strict_types=1);

namespace App\Controller\SocialNetwork;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\SocialNetwork\Helper\SocialNetworkHelperInterface;
use App\Entity\City;
use App\Entity\Ride;
use App\Factory\SocialNetworkProfile\SocialNetworkProfileFactory;
use App\Factory\SocialNetworkProfile\SocialNetworkProfileFactoryInterface;
use App\Form\Type\SocialNetworkProfileAddType;
use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use App\Criticalmass\Util\ClassUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class SocialNetworkListController extends AbstractController
{
    /**
     * @ParamConverter("city", class="App:City")
     */
    public function listCityAction(ObjectRouterInterface $router, City $city, SocialNetworkProfileFactory $socialNetworkProfileFactory, SocialNetworkHelperInterface $socialNetworkHelper, UserInterface $user): Response
    {
        $addProfileForm = $this->getAddProfileForm($router, $city, $socialNetworkProfileFactory, $user, $socialNetworkHelper);

        return $this->render('SocialNetwork/list.html.twig', [
            'list' => $socialNetworkHelper->getProfileList($city),
            'addProfileForm' => $addProfileForm->createView(),
            'profileAbleType' => ClassUtil::getLowercaseShortname($city),
            'profileAble' => $city,
        ]);
    }

    /**
     * @ParamConverter("ride", class="App:Ride")
     */
    public function listRideAction(ObjectRouterInterface $router, Ride $ride, SocialNetworkProfileFactoryInterface $socialNetworkProfileFactory, SocialNetworkHelperInterface $socialNetworkHelper, UserInterface $user = null): Response
    {
        $addProfileForm = $this->getAddProfileForm($router, $ride, $socialNetworkProfileFactory, $user, $socialNetworkHelper);

        return $this->render('SocialNetwork/list.html.twig', [
            'list' => $socialNetworkHelper->getProfileList($ride),
            'addProfileForm' => $addProfileForm->createView(),
            'profileAbleType' => ClassUtil::getLowercaseShortname($ride),
            'profileAble' => $ride,
        ]);
    }

    protected function getAddProfileForm(ObjectRouterInterface $router, SocialNetworkProfileAble $profileAble, SocialNetworkProfileFactoryInterface $socialNetworkProfileFactory, UserInterface $user, SocialNetworkHelperInterface $socialNetworkHelper): FormInterface
    {
        $socialNetworkProfile = $socialNetworkProfileFactory
            ->withUser($user)
            ->build();

        $setMethodName = sprintf('set%s', ClassUtil::getShortname($profileAble));
        $socialNetworkProfile->$setMethodName($profileAble);

        $form = $this->createForm(
            SocialNetworkProfileAddType::class,
            $socialNetworkProfile, [
                'action' => $socialNetworkHelper->getRouteName($profileAble, 'add'),
            ]
        );

        return $form;
    }
}
