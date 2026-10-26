<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Write-Tool: aktualisiert die Eigenschaften eines Fotos (per ID):
 * Beschreibung, Sichtbarkeit (enabled), Löschstatus (deleted), Koordinaten.
 */
final class UpdatePhotoTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly ManagerRegistry $registry,
        private readonly CriticalSerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'update_photo';
    }

    public function description(): string
    {
        return 'Aktualisiert ein Foto (per ID): description, enabled, deleted, latitude, longitude.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'photoId' => ['type' => 'integer', 'description' => 'ID des Fotos.'],
                'photo' => [
                    'type' => 'object',
                    'description' => 'Zu ändernde Felder: description, enabled (bool), deleted (bool), latitude, longitude.',
                ],
            ],
            'required' => ['photoId', 'photo'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::PhotoWrite;
    }

    public function call(array $arguments): string
    {
        if (!isset($arguments['photoId']) || !is_numeric($arguments['photoId'])) {
            throw new McpToolException('photoId ist erforderlich und muss eine Zahl sein.');
        }

        $photo = $this->resolver->photo((int) $arguments['photoId']);
        $data = \is_array($arguments['photo'] ?? null) ? $arguments['photo'] : [];

        if (array_key_exists('description', $data)) {
            $photo->setDescription(null === $data['description'] ? null : (string) $data['description']);
        }

        if (array_key_exists('enabled', $data)) {
            if (!\is_bool($data['enabled'])) {
                throw new McpToolException('photo.enabled muss ein Boolean sein.');
            }
            $photo->setEnabled($data['enabled']);
        }

        if (array_key_exists('deleted', $data)) {
            if (!\is_bool($data['deleted'])) {
                throw new McpToolException('photo.deleted muss ein Boolean sein.');
            }
            $photo->setDeleted($data['deleted']);
        }

        if (array_key_exists('latitude', $data)) {
            $photo->setLatitude(null === $data['latitude'] ? null : (float) $data['latitude']);
        }

        if (array_key_exists('longitude', $data)) {
            $photo->setLongitude(null === $data['longitude'] ? null : (float) $data['longitude']);
        }

        $this->registry->getManager()->flush();

        return $this->serializer->serialize($photo, 'json', ['groups' => ['photo-details']]);
    }
}
