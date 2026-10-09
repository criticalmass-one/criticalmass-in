<?php declare(strict_types=1);

namespace App\Twig\Loader;

use App\Design\DesignChoice;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * Legt die neue Ansicht über die bisherige: Wer sie eingeschaltet hat, bekommt für
 * `Ride/show.html.twig` die Datei `v2/Ride/show.html.twig`, sofern es sie gibt. Gibt es
 * sie nicht, meldet sich dieser Loader nicht zuständig und der nächste in der Kette
 * liefert das bisherige Template.
 *
 * Die alten Layouts und die Mails sind ausgenommen. Eine noch nicht umgestellte Seite
 * erbt dadurch immer vom alten Rahmen und bekommt das alte Stylesheet – sie landet nie
 * halb im neuen Design. Neue Seiten erben ausdrücklich von `v2/Layout/…`.
 *
 * Der Cache-Schlüssel ist der des v2-Templates, alte und neue Fassung werden also
 * getrennt kompiliert.
 */
class DesignAwareLoader implements LoaderInterface
{
    public const string PREFIX = 'v2/';

    /**
     * Templates, die nie eine v2-Fassung bekommen dürfen. `V2TemplateRulesTest` prüft,
     * dass unter templates/v2/ keine Datei sie überschattet.
     */
    public const array EXCLUDED = [
        'Template/MasterTemplate.html.twig',
        'Template/StandardTemplate.html.twig',
        'Template/FullscreenTemplate.html.twig',
        'email/',
    ];

    public function __construct(
        private readonly LoaderInterface $inner,
        private readonly DesignChoice $designChoice,
    ) {
    }

    public function getSourceContext(string $name): Source
    {
        return $this->inner->getSourceContext(self::PREFIX . $name);
    }

    public function getCacheKey(string $name): string
    {
        return $this->inner->getCacheKey(self::PREFIX . $name);
    }

    public function isFresh(string $name, int $time): bool
    {
        return $this->inner->isFresh(self::PREFIX . $name, $time);
    }

    public function exists(string $name): bool
    {
        if (!self::isOverridable($name) || !$this->designChoice->isV2()) {
            return false;
        }

        return $this->inner->exists(self::PREFIX . $name);
    }

    public static function isOverridable(string $name): bool
    {
        if (str_starts_with($name, '@') || str_starts_with($name, self::PREFIX)) {
            return false;
        }

        foreach (self::EXCLUDED as $excluded) {
            if (str_starts_with($name, $excluded)) {
                return false;
            }
        }

        return true;
    }
}
