<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Criticalmass\CitySlug\Handler\CitySlugHandler;
use App\Entity\City;
use App\Entity\CitySlug;
use App\OAuth2\OAuthScope;

/**
 * Write-Tool: spiegelt PUT /api/{citySlug} (Stadt anlegen).
 */
final class CreateCityTool extends AbstractWriteTool
{
    public function name(): string
    {
        return 'create_city';
    }

    public function description(): string
    {
        return 'Legt eine neue Stadt an (inkl. Slugs).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der neuen Stadt (muss frei sein).'],
                'city' => [
                    'type' => 'object',
                    'description' => 'Stadt-Felder (api-write), z. B. city, title, description, latitude, longitude.',
                ],
            ],
            'required' => ['citySlug', 'city'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::CityWrite;
    }

    public function call(array $arguments): string
    {
        $citySlug = trim((string) ($arguments['citySlug'] ?? ''));

        if ('' === $citySlug) {
            throw new McpToolException('citySlug ist erforderlich.');
        }

        $manager = $this->registry->getManager();

        if (null !== $manager->getRepository(CitySlug::class)->findOneBy(['slug' => $citySlug])) {
            throw new McpToolException(sprintf('Eine Stadt mit dem Slug "%s" existiert bereits.', $citySlug));
        }

        /** @var City $city */
        $city = $this->deserialize(\is_array($arguments['city'] ?? null) ? $arguments['city'] : [], City::class);

        if (!$city->getTitle()) {
            $city->setTitle(sprintf('Critical Mass %s', $city->getCity()));
        }

        $city->setCreatedAt(new \DateTime());

        foreach (CitySlugHandler::createSlugsForCity($city) as $slug) {
            $slug->setCity($city);
            $manager->persist($slug);
        }

        $manager->persist($city);
        $manager->flush();

        return $this->serializer->serialize($city, 'json', ['groups' => ['ride-list']]);
    }
}
