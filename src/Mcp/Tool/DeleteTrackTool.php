<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Event\Track\TrackDeletedEvent;
use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Write-Tool: löscht einen Track (Soft-Delete via deleted-Flag) und feuert
 * TrackDeletedEvent, analog zu DELETE /api/track/{id}.
 */
final class DeleteTrackTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly ManagerRegistry $registry,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function name(): string
    {
        return 'delete_track';
    }

    public function description(): string
    {
        return 'Löscht einen Track (Soft-Delete). Der Track bleibt erhalten, wird aber als gelöscht markiert.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'trackId' => ['type' => 'integer', 'description' => 'ID des Tracks.'],
            ],
            'required' => ['trackId'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::TrackWrite;
    }

    public function call(array $arguments): string
    {
        if (!isset($arguments['trackId']) || !is_numeric($arguments['trackId'])) {
            throw new McpToolException('trackId ist erforderlich und muss eine Zahl sein.');
        }

        $track = $this->resolver->track((int) $arguments['trackId']);
        $track->setDeleted(true);

        $this->registry->getManager()->flush();

        $this->eventDispatcher->dispatch(new TrackDeletedEvent($track), TrackDeletedEvent::NAME);

        return json_encode(['status' => 'ok', 'deletedTrackId' => $track->getId()], JSON_THROW_ON_ERROR);
    }
}
