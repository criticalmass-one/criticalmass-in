<?php declare(strict_types=1);

namespace App\Controller\SocialNetwork;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Entity\Ride;
use App\Entity\SocialNetworkProfile;
use App\Form\Type\SocialNetworkProfileAddType;
use App\Form\Type\SocialNetworkProfileType;
use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use App\Criticalmass\Util\ClassUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

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
    public function listRideAction(ObjectRouterInterface $router, Ride $ride): Response
    {
        $addProfileForm = $this->getAddProfileForm($router, $ride);

        return $this->render('SocialNetwork/list.html.twig', [
            'list' => $this->getProfileList($ride),
            'addProfileForm' => $addProfileForm->createView(),
            'profileAbleType' => ClassUtil::getLowercaseShortname($ride),
            'profileAble' => $ride,
        ]);
    }

    protected function getAddProfileForm(ObjectRouterInterface $router, SocialNetworkProfileAble $profileAble): FormInterface
    {
        $socialNetworkProfile = new SocialNetworkProfile();

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
