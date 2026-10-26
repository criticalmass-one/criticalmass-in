<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Write-Tool: spiegelt POST /api/{citySlug} (Stadt bearbeiten).
 */
final class UpdateCityTool extends AbstractWriteTool
{
    public function __construct(
        ManagerRegistry $registry,
        CriticalSerializerInterface $serializer,
        ValidatorInterface $validator,
        private readonly EntityResolver $resolver,
    ) {
        parent::__construct($registry, $serializer, $validator);
    }

    public function name(): string
    {
        return 'update_city';
    }

    public function description(): string
    {
        return 'Aktualisiert eine bestehende Stadt (Name, Titel, Beschreibung, Koordinaten, Zeitzone).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'city' => [
                    'type' => 'object',
                    'description' => 'Zu ändernde Stadt-Felder (api-write), z. B. city, title, description, latitude, longitude, timezone.',
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
        $city = $this->resolver->city((string) ($arguments['citySlug'] ?? ''));

        $this->deserializeInto(\is_array($arguments['city'] ?? null) ? $arguments['city'] : [], $city);

        $this->validateEntity($city);
        $this->flush();

        return $this->serializer->serialize($city, 'json', ['groups' => ['ride-list']]);
    }
}
