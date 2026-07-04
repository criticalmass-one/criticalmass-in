<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\Track;
use App\OAuth2\OAuthScope;
use App\Repository\TrackRepository;
use App\Serializer\CriticalSerializerInterface;

/**
 * Read-Tool: spiegelt GET /api/track/{id} (öffentliche Track-Felder).
 */
final class GetTrackTool implements McpToolInterface
{
    public function __construct(
        private readonly TrackRepository $trackRepository,
        private readonly CriticalSerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'get_track';
    }

    public function description(): string
    {
        return 'Liefert einen einzelnen GPS-Track anhand seiner ID.';
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
        return OAuthScope::TrackRead;
    }

    public function call(array $arguments): string
    {
        $track = $this->trackRepository->find((int) ($arguments['trackId'] ?? 0));

        if (!$track instanceof Track) {
            throw new McpToolException('Track nicht gefunden.');
        }

        return $this->serializer->serialize($track, 'json', ['groups' => ['api-public']]);
    }
}
