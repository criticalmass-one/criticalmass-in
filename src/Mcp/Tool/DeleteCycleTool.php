<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\Ride;
use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Repository\CityCycleRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Write-Tool: löscht einen Termin-Zyklus einer Stadt. Bereits aus dem Zyklus
 * erzeugte Rides werden vorher entkoppelt (cycle = null), damit sie NICHT durch
 * das cascade-remove der Zuordnung mitgelöscht werden.
 */
final class DeleteCycleTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly CityCycleRepository $cityCycleRepository,
        private readonly ManagerRegistry $registry,
    ) {
    }

    public function name(): string
    {
        return 'delete_cycle';
    }

    public function description(): string
    {
        return 'Löscht einen Termin-Zyklus einer Stadt. Zugehörige Rides bleiben erhalten (werden entkoppelt).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'cycleId' => ['type' => 'integer', 'description' => 'ID des Cycles.'],
            ],
            'required' => ['citySlug', 'cycleId'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::CycleWrite;
    }

    public function call(array $arguments): string
    {
        $city = $this->resolver->city((string) ($arguments['citySlug'] ?? ''));
        $cycle = $this->cityCycleRepository->find((int) ($arguments['cycleId'] ?? 0));

        if (null === $cycle || $cycle->getCity()?->getId() !== $city->getId()) {
            throw new McpToolException('Cycle nicht gefunden oder gehört nicht zu dieser Stadt.');
        }

        $cycleId = $cycle->getId();
        $manager = $this->registry->getManager();

        // Rides explizit entkoppeln (FK cycle_id nullen), damit das
        // cascade-remove der Zuordnung sie nicht mitnimmt.
        $rides = $manager->getRepository(Ride::class)->findBy(['cycle' => $cycle]);
        foreach ($rides as $ride) {
            $ride->setCycle(null);
        }
        $manager->flush();

        $manager->remove($cycle);
        $manager->flush();

        return json_encode(['status' => 'ok', 'deletedCycleId' => $cycleId], JSON_THROW_ON_ERROR);
    }
}
