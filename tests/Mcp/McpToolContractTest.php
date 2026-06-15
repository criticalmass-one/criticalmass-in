<?php declare(strict_types=1);

namespace Tests\Mcp;

use App\Mcp\Tool\McpToolInterface;
use App\OAuth2\OAuthScope;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Vertrags-Tests über ALLE registrierten MCP-Tools (automatisch per Glob
 * entdeckt). Stellt sicher, dass jedes Tool ein valides Schema, einen Scope und
 * sinnvolle Metadaten liefert — und dass alle Tool-Namen eindeutig sind.
 */
final class McpToolContractTest extends KernelTestCase
{
    /**
     * @return iterable<string, array{class-string}>
     */
    public static function toolClassProvider(): iterable
    {
        foreach (glob(__DIR__ . '/../../src/Mcp/Tool/*Tool.php') ?: [] as $file) {
            $short = basename($file, '.php');

            if (str_starts_with($short, 'Abstract')) {
                continue;
            }

            $class = 'App\\Mcp\\Tool\\' . $short;

            if (!is_a($class, McpToolInterface::class, true)) {
                continue;
            }

            yield $short => [$class];
        }
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('toolClassProvider')]
    public function testToolMeetsContract(string $class): void
    {
        self::bootKernel();
        /** @var McpToolInterface $tool */
        $tool = static::getContainer()->get($class);

        self::assertMatchesRegularExpression('/^[a-z][a-z0-9_]+$/', $tool->name(), 'Tool-Name snake_case');
        self::assertNotSame('', trim($tool->description()), 'Beschreibung darf nicht leer sein');
        self::assertInstanceOf(OAuthScope::class, $tool->requiredScope());

        $schema = $tool->inputSchema();
        self::assertSame('object', $schema['type'] ?? null);
        self::assertArrayHasKey('properties', $schema);
        self::assertIsArray($schema['properties']);

        foreach (($schema['required'] ?? []) as $requiredProperty) {
            self::assertArrayHasKey(
                $requiredProperty,
                $schema['properties'],
                sprintf('required-Feld "%s" muss in properties stehen', $requiredProperty),
            );
        }
    }

    public function testAllToolNamesAreUnique(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $names = [];
        foreach (self::toolClassProvider() as [$class]) {
            /** @var McpToolInterface $tool */
            $tool = $container->get($class);
            $names[] = $tool->name();
        }

        self::assertNotEmpty($names);
        self::assertSame(array_values(array_unique($names)), $names, 'Tool-Namen müssen eindeutig sein');
    }

    public function testThereAreManyTools(): void
    {
        $count = iterator_count(self::toolClassProvider());

        self::assertGreaterThanOrEqual(25, $count, 'Es sollten viele Tools registriert sein');
    }
}
