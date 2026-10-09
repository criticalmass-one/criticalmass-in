<?php declare(strict_types=1);

namespace App\Design;

use App\Entity\User;
use Flagception\Manager\FeatureManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Entscheidet, ob die aktuelle Anfrage die neue Ansicht bekommt.
 *
 * Die neue Ansicht ist ein freiwilliger Test: Wer sie einschaltet, sieht alle Seiten,
 * die es schon neu gibt, im neuen Design, alle anderen wie bisher. Die Wahl liegt bei
 * angemeldeten Nutzern am Konto (gilt dann auf jedem Gerät), sonst in einem Cookie.
 * Hat ein Konto noch nie gewählt, gilt das Cookie – so geht die Wahl eines Gastes beim
 * ersten Anmelden nicht verloren.
 *
 * Ohne den Schalter `design_v2` gibt es nur die bisherige Ansicht, gleich was Cookie
 * und Konto sagen.
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

        // Ohne Anfrage (Konsole, Cache-Warmup, Messenger) immer die bisherige Ansicht.
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
