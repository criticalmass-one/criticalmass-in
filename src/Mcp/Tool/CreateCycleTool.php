<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\CityCycle;
use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Write-Tool: spiegelt PUT /api/{citySlug}/cycles (Termin-Zyklus anlegen).
 */
final class CreateCycleTool extends AbstractWriteTool
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
        return 'create_cycle';
    }

    public function description(): string
    {
        return 'Legt einen wiederkehrenden Termin-Zyklus (Cycle) für eine Stadt an.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'cycle' => [
                    'type' => 'object',
                    'description' => 'Cycle-Felder (api-write), z. B. dayOfWeek, weekOfMonth, time, validFrom, validUntil.',
                ],
            ],
            'required' => ['citySlug', 'cycle'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::CycleWrite;
    }

    public function call(array $arguments): string
    {
        $city = $this->resolver->city((string) ($arguments['citySlug'] ?? ''));

        /** @var CityCycle $cycle */
        $cycle = $this->deserialize(\is_array($arguments['cycle'] ?? null) ? $arguments['cycle'] : [], CityCycle::class);
        $cycle
            ->setCity($city)
            ->setCreatedAt(new \DateTime());

        $this->validateEntity($cycle);
        $this->persist($cycle);
        $this->flush();

        return $this->serializer->serialize($cycle, 'json', []);
    }
}
