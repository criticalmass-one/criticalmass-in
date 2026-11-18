<?php declare(strict_types=1);

namespace App\Design;

use App\Entity\User;
use Flagception\Manager\FeatureManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Neue Ansicht ja oder nein: Kontofeld vor Cookie, beides nur bei aktivem Flag.
 */
class DesignChoice
{
    public const string FEATURE = 'design_v2';
    public const string COOKIE = 'cm_design';
    public const string V1 = 'v1';
    public const string V2 = 'v2';

    private ?Request $decidedFor = null;

    private bool $decision = false;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly Security $security,
        private readonly FeatureManagerInterface $featureManager,
    ) {
    }

    public function isAvailable(): bool
    {
        return $this->featureManager->isActive(self::FEATURE);
    }

    public function isV2(): bool
    {
        $request = $this->requestStack->getMainRequest();

        if (null === $request) {
            return false;
        }

        if ($request !== $this->decidedFor) {
            $this->decidedFor = $request;
            $this->decision = $this->decide($request);
        }

        return $this->decision;
    }

    private function decide(Request $request): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $user = $this->security->getUser();

        if ($user instanceof User && null !== $user->getDesignV2()) {
            return $user->getDesignV2();
        }

        return self::V2 === $request->cookies->get(self::COOKIE);
    }
}
