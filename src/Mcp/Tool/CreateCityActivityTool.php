<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\CityActivity;
use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Write-Tool: spiegelt POST /api/city/{citySlug}/activity (Aktivitäts-Score).
 */
final class CreateCityActivityTool extends AbstractWriteTool
{
    private const REQUIRED_SIGNAL_TYPES = ['participation', 'photo', 'track', 'social_feed'];

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
        return 'create_city_activity';
    }

    public function description(): string
    {
        return 'Meldet einen Aktivitäts-Score (0.0–1.0) für eine Stadt inkl. Detail-Signalen.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'score' => ['type' => 'number', 'minimum' => 0.0, 'maximum' => 1.0, 'description' => 'Gesamt-Score.'],
                'calculatedAt' => ['type' => 'string', 'description' => 'Berechnungszeitpunkt (ISO 8601, optional).'],
                'details' => [
                    'type' => 'array',
                    'description' => 'Detail-Signale, je mit signalType, normalizedScore, rawCount.',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'signalType' => ['type' => 'string', 'enum' => self::REQUIRED_SIGNAL_TYPES],
                            'normalizedScore' => ['type' => 'number'],
                            'rawCount' => ['type' => 'integer'],
                        ],
                    ],
                ],
            ],
            'required' => ['citySlug', 'score', 'details'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::ActivityWrite;
    }

    public function call(array $arguments): string
    {
        $city = $this->resolver->city((string) ($arguments['citySlug'] ?? ''));

        if (!isset($arguments['score']) || $arguments['score'] < 0.0 || $arguments['score'] > 1.0) {
            throw new McpToolException('score muss zwischen 0.0 und 1.0 liegen.');
        }

        if (!\is_array($arguments['details'] ?? null)) {
            throw new McpToolException('details ist erforderlich.');
        }

        $detailsByType = [];
        foreach ($arguments['details'] as $detail) {
            if (\is_array($detail) && isset($detail['signalType'])) {
                $detailsByType[(string) $detail['signalType']] = $detail;
            }
        }

        foreach (self::REQUIRED_SIGNAL_TYPES as $type) {
            if (!isset($detailsByType[$type])) {
                throw new McpToolException(sprintf('Fehlender Signaltyp: %s', $type));
            }
        }

        $score = (float) $arguments['score'];

        $activity = new CityActivity();
        $activity->setCity($city);
        $activity->setScore($score);
        $activity->setParticipationScore((float) $detailsByType['participation']['normalizedScore']);
        $activity->setParticipationRawCount((int) $detailsByType['participation']['rawCount']);
        $activity->setPhotoScore((float) $detailsByType['photo']['normalizedScore']);
        $activity->setPhotoRawCount((int) $detailsByType['photo']['rawCount']);
        $activity->setTrackScore((float) $detailsByType['track']['normalizedScore']);
        $activity->setTrackRawCount((int) $detailsByType['track']['rawCount']);
        $activity->setSocialFeedScore((float) $detailsByType['social_feed']['normalizedScore']);
        $activity->setSocialFeedRawCount((int) $detailsByType['social_feed']['rawCount']);

        if (isset($arguments['calculatedAt'])) {
            try {
                $activity->setCreatedAt(new \DateTimeImmutable((string) $arguments['calculatedAt']));
            } catch (\Exception) {
                throw new McpToolException('calculatedAt ist kein gültiger Zeitpunkt.');
            }
        }

        $this->persist($activity);
        $city->setActivityScore($score);
        $this->flush();

        return $this->ok(['city' => $city->getCity(), 'score' => $score]);
    }
}
