<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\Photo;
use App\OAuth2\OAuthScope;
use App\Repository\PhotoRepository;
use App\Serializer\CriticalSerializerInterface;

/**
 * Read-Tool: spiegelt GET /api/photo/{id} (Foto-Details).
 */
final class GetPhotoTool implements McpToolInterface
{
    public function __construct(
        private readonly PhotoRepository $photoRepository,
        private readonly CriticalSerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'get_photo';
    }

    public function description(): string
    {
        return 'Liefert ein einzelnes Foto (inkl. EXIF-Details) anhand seiner ID.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'photoId' => ['type' => 'integer', 'description' => 'ID des Fotos.'],
            ],
            'required' => ['photoId'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::PhotoRead;
    }

    public function call(array $arguments): string
    {
        $photo = $this->photoRepository->findOneBy([
            'id' => (int) ($arguments['photoId'] ?? 0),
            'enabled' => true,
            'deleted' => false,
        ]);

        if (!$photo instanceof Photo) {
            throw new McpToolException('Foto nicht gefunden.');
        }

        return $this->serializer->serialize($photo, 'json', ['groups' => ['photo-details']]);
    }
}
