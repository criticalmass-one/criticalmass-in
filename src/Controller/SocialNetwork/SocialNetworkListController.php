<?php declare(strict_types=1);

namespace App\Controller\SocialNetwork;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Entity\Ride;
use App\Entity\SocialNetworkProfile;
use App\Form\Type\SocialNetworkProfileType;
use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use App\Criticalmass\Util\ClassUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class SocialNetworkListController extends AbstractController
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

    protected function getRouteName(ObjectRouterInterface $router, SocialNetworkProfileAble $profileAble, string $actionName): string
    {
        $routeName = sprintf('criticalmass_socialnetwork_%s_%s', ClassUtil::getLowercaseShortname($profileAble), $actionName);

        return $router->generate($profileAble, $routeName);
    }
}
