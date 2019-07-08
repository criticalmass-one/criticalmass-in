<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Helper;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use App\Criticalmass\Util\ClassUtil;
use App\Entity\City;
use App\Entity\Ride;
use App\Entity\SocialNetworkProfile;
use App\Entity\Subride;
use App\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class SocialNetworkHelper implements SocialNetworkHelperInterface
{
    /**
     * @var RegistryInterface $registry
     */
    protected $registry;

    /**
     * @var ObjectRouterInterface $router
     */
    protected $router;

    public function __construct(RegistryInterface $registry, ObjectRouterInterface $router)
    {
        $this->registry = $registry;
        $this->router = $router;
    }

    public function getProfileAbleObject(Ride $ride = null, Subride $subride = null, City $city = null, User $user = null): SocialNetworkProfileAble
    {
        return $user ?? $ride ?? $city ?? $subride;
    }

    public function assignProfileAble(SocialNetworkProfile $socialNetworkProfile, Request $request): SocialNetworkProfile
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

    public function getProfileAble(SocialNetworkProfile $socialNetworkProfile): ?SocialNetworkProfileAble
    {
        return $socialNetworkProfile->getUser() ?? $socialNetworkProfile->getRide() ?? $socialNetworkProfile->getCity() ?? $socialNetworkProfile->getSubride();
    }

    public function getProfileAbleShortname(SocialNetworkProfileAble $profileAble): string
    {
        $reflection = new \ReflectionClass($profileAble);

        return $reflection->getShortName();
    }

    public function getRouteName(SocialNetworkProfileAble $profileAble, string $actionName): string
    {
        $routeName = sprintf('criticalmass_socialnetwork_%s_%s', ClassUtil::getLowercaseShortname($profileAble), $actionName);

        return $this->router->generate($profileAble, $routeName);
    }

    public function getProfileList(SocialNetworkProfileAble $profileAble): array
    {
        $methodName = sprintf('findBy%s', ClassUtil::getShortname($profileAble));

        $list = $this->registry->getRepository(SocialNetworkProfile::class)->$methodName($profileAble);

        return $list;
    }
}
