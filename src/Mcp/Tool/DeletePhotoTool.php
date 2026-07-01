<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Write-Tool: löscht ein Foto (Soft-Delete via deleted-Flag).
 */
final class DeletePhotoTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly ManagerRegistry $registry,
    ) {
    }

    public function name(): string
    {
        return 'delete_photo';
    }

    public function description(): string
    {
        return 'Löscht ein Foto (Soft-Delete). Das Foto bleibt erhalten, wird aber als gelöscht markiert.';
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
        return OAuthScope::PhotoWrite;
    }

    public function call(array $arguments): string
    {
        if (!isset($arguments['photoId']) || !is_numeric($arguments['photoId'])) {
            throw new McpToolException('photoId ist erforderlich und muss eine Zahl sein.');
        }

        $photo = $this->resolver->photo((int) $arguments['photoId']);
        $photo->setDeleted(true);

        $this->registry->getManager()->flush();

        return json_encode(['status' => 'ok', 'deletedPhotoId' => $photo->getId()], JSON_THROW_ON_ERROR);
    }
}
