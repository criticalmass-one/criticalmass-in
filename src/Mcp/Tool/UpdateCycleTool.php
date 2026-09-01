<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Repository\CityCycleRepository;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Write-Tool: spiegelt POST /api/{citySlug}/cycles/{cycleId} (Cycle bearbeiten).
 */
final class UpdateCycleTool extends AbstractWriteTool
{
    public function __construct(
        ManagerRegistry $registry,
        CriticalSerializerInterface $serializer,
        ValidatorInterface $validator,
        private readonly EntityResolver $resolver,
        private readonly CityCycleRepository $cityCycleRepository,
    ) {
        parent::__construct($registry, $serializer, $validator);
    }

    public function name(): string
    {
        return 'update_cycle';
    }

    public function description(): string
    {
        return 'Aktualisiert einen bestehenden Termin-Zyklus einer Stadt.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'cycleId' => ['type' => 'integer', 'description' => 'ID des Cycles.'],
                'cycle' => [
                    'type' => 'object',
                    'description' => 'Zu ändernde Cycle-Felder (api-write).',
                ],
            ],
            'required' => ['citySlug', 'cycleId', 'cycle'],
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

        $this->deserializeInto(\is_array($arguments['cycle'] ?? null) ? $arguments['cycle'] : [], $cycle);
        $cycle->setUpdatedAt(new \DateTime());

        $this->validateEntity($cycle);
        $this->flush();

        return $this->serializer->serialize($cycle, 'json', []);
    }
}
