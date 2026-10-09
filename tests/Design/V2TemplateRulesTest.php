<?php declare(strict_types=1);

namespace Tests\Design;

use App\Twig\Loader\DesignAwareLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

/**
 * Hält die Regeln für templates/v2/ ein, die der Loader voraussetzt: Die alten Layouts
 * und die Mails bekommen nie eine v2-Fassung, und jede v2-Seite erbt von v2/Layout/.
 */
final class V2TemplateRulesTest extends TestCase
{
    private const string V2_DIR = __DIR__ . '/../../templates/v2';

    /**
     * @return array<string, string>
     */
    private function v2Templates(): array
    {
        $templates = [];

        foreach ((new Finder())->files()->in(self::V2_DIR)->name('*.twig') as $file) {
            $templates[str_replace('\\', '/', $file->getRelativePathname())] = $file->getContents();
        }

        return $templates;
    }

    public function testExcludedTemplatesHaveNoV2Version(): void
    {
        foreach (array_keys($this->v2Templates()) as $name) {
            self::assertTrue(
                DesignAwareLoader::isOverridable($name) || str_starts_with($name, 'Layout/'),
                sprintf('templates/v2/%s überschattet ein Template, das nie neu werden darf.', $name)
            );
        }
    }

    public function testV2PagesExtendOnlyV2Layouts(): void
    {
        foreach ($this->v2Templates() as $name => $source) {
            if (!preg_match_all("/{%-?\s*extends\s+['\"]([^'\"]+)['\"]/", $source, $matches)) {
                continue;
            }

            foreach ($matches[1] as $parent) {
                $allowed = str_starts_with($parent, 'v2/Layout/')
                    || ('Layout/base.html.twig' === $name && 'Template/MasterTemplate.html.twig' === $parent);

                self::assertTrue($allowed, sprintf('templates/v2/%s erbt von %s statt von v2/Layout/….', $name, $parent));
            }
        }
    }
}
