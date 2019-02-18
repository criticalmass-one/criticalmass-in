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
use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractSocialNetworkController extends AbstractController
{
    protected function getProfileAbleObject(
        Ride $ride = null,
        Subride $subride = null,
        City $city = null,
        User $user = null
    ): SocialNetworkProfileAble {
        return $user ?? $ride ?? $city ?? $subride;
    }

    protected function assignProfileAble(SocialNetworkProfile $socialNetworkProfile, Request $request): SocialNetworkProfile
    {
        $classNameOrder = [User::class, Subride::class, Ride::class, City::class];

        foreach ($classNameOrder as $className) {
            $shortname = ClassUtil::getLowercaseShortnameFromFqcn($className);

            if ($request->get($shortname)) {
                $setMethodName = sprintf('set%s', ucfirst($shortname));

                $socialNetworkProfile->$setMethodName($request->get($shortname));

                break;
            }
        }

        return $socialNetworkProfile;
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

    protected function getRouteName(ObjectRouterInterface $router, SocialNetworkProfileAble $profileAble, string $actionName): string
    {
        $routeName = sprintf('criticalmass_socialnetwork_%s_%s', ClassUtil::getLowercaseShortname($profileAble), $actionName);

        return $router->generate($profileAble, $routeName);
    }

    protected function getProfileList(SocialNetworkProfileAble $profileAble): array
    {
        $methodName = sprintf('findBy%s', ClassUtil::getShortname($profileAble));

        $list = $this->getDoctrine()->getRepository(SocialNetworkProfile::class)->$methodName($profileAble);

        return $list;
    }
}
