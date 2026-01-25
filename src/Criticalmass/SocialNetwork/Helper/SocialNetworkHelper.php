<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Helper;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\Util\ClassUtil;
use App\Entity\City;
use App\Entity\Ride;
use App\Entity\SocialNetworkProfile;
use App\Entity\Subride;
use App\Entity\User;
use App\EntityInterface\SocialNetworkProfileAble;
use Doctrine\Persistence\ManagerRegistry;

class SocialNetworkHelper implements SocialNetworkHelperInterface
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly ObjectRouterInterface $router
    ) {

    }

    public function getProfileAbleObject(?Ride $ride = null, ?Subride $subride = null, ?City $city = null, ?User $user = null): SocialNetworkProfileAble
    {
        return $user ?? $ride ?? $city ?? $subride;
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
