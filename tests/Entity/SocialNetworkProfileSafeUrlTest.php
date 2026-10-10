<?php declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\SocialNetworkProfile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Was in einem href stehen darf.
 *
 * Die Kennung eines Profils ist ein freies Textfeld, das jedes angemeldete
 * Konto fuellen kann, und sie ging bisher unveraendert in den Verweis. Auf der
 * Produktion standen dadurch sechs Verweise ohne Schema -- der Browser las sie
 * als relativen Pfad, "facebook.com/Critical Mass Kaiserslautern" wurde zu
 * criticalmass.in/kaiserslautern/facebook.com/... und antwortete mit 404.
 *
 * Der ernstere Fall stand nicht drin, war aber jederzeit eintragbar:
 * "javascript:..." haette Twig klaglos in den href geschrieben. Autoescaping
 * greift dort nicht, weil an der Zeichenkette nichts zu escapen ist.
 */
class SocialNetworkProfileSafeUrlTest extends TestCase
{
    private function profilMit(string $kennung): SocialNetworkProfile
    {
        $profil = new SocialNetworkProfile();
        $profil->setIdentifier($kennung);

        return $profil;
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function gueltigeAdressen(): array
    {
        return [
            'https' => ['https://www.instagram.com/critical_mass_kaiserslautern/'],
            'http' => ['http://example.org/critical-mass'],
            'mit Fragment' => ['https://signal.group/#CjQKIL4s1RXDzHG17zF'],
            'mit Abfrage' => ['https://instagram.com/xyclona?igshid=YmMyMTA2M2Y='],
            'Grossschreibung im Schema' => ['HTTPS://example.org/'],
        ];
    }

    #[DataProvider('gueltigeAdressen')]
    public function testARealWebAddressIsHandedThrough(string $kennung): void
    {
        self::assertSame($kennung, $this->profilMit($kennung)->getSafeUrl());
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function untauglicheKennungen(): array
    {
        return [
            // Genau die sechs Formen, die live standen oder stehen:
            'ohne Schema' => ['www.facebook.com/groups/786611191740363'],
            'ohne Schema, mit Leerzeichen' => ['facebook.com/Critical Mass Kaiserslautern'],
            'gar keine Adresse' => ['FB-67278'],
            'Mastodon-Kennung' => ['@criticalmass@karlsruhe-social.de'],
            'Schlagwort' => ['#CriticalMassNorderstedt'],

            // Und das, was nie drinstand, aber jederzeit haette drinstehen koennen:
            'javascript' => ['javascript:alert(document.cookie)'],
            'javascript in Grossbuchstaben' => ['JavaScript:alert(1)'],
            'data' => ['data:text/html,<script>alert(1)</script>'],
            'vbscript' => ['vbscript:msgbox(1)'],

            // Schemata, die zwar harmlos sind, aber in diesem Feld nichts zu
            // suchen haben -- der Verweis soll eine Webseite oeffnen.
            'Datei' => ['file:///etc/passwd'],
            'Post' => ['mailto:info@example.org'],

            'leer' => [''],
            'nur Leerzeichen' => ['   '],
            'Schema ohne Rechnernamen' => ['https://'],
        ];
    }

    #[DataProvider('untauglicheKennungen')]
    public function testAnythingElseYieldsNoLink(string $kennung): void
    {
        self::assertNull(
            $this->profilMit($kennung)->getSafeUrl(),
            sprintf('"%s" darf nicht in einem href landen.', $kennung)
        );
    }

    public function testAProfileWithoutAnIdentifierYieldsNoLink(): void
    {
        self::assertNull((new SocialNetworkProfile())->getSafeUrl());
    }

    /**
     * Fuehrende Leerzeichen sind ein Tippfehler, kein Grund, den Verweis
     * fallenzulassen -- aber die zurueckgegebene Adresse ist die getrimmte.
     */
    public function testSurroundingWhitespaceIsForgiven(): void
    {
        self::assertSame(
            'https://example.org/',
            $this->profilMit("  https://example.org/\n")->getSafeUrl()
        );
    }
}
