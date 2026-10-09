<?php declare(strict_types=1);

namespace App\Twig\Loader;

use App\Design\DesignChoice;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * Liefert in der neuen Ansicht templates/v2/<name>, falls vorhanden.
 *
 * Die alten Layouts sind ausgenommen, damit eine nicht umgestellte Seite nie halb im
 * neuen Rahmen landet.
 */
class DesignAwareLoader implements LoaderInterface
{
    public const string PREFIX = 'v2/';

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
