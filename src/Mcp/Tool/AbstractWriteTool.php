<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Basis für Write-Tools, die die schreibenden API-Endpunkte spiegeln.
 * Bündelt Deserialisierung (api-write), Validierung und Persistenz analog zum
 * BaseController der API.
 */
abstract class AbstractWriteTool implements McpToolInterface
{
    public function __construct(
        protected readonly ManagerRegistry $registry,
        protected readonly CriticalSerializerInterface $serializer,
        protected readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @param list<string>         $groups
     */
    protected function deserialize(array $data, string $class, array $groups = ['api-write']): object
    {
        try {
            return $this->serializer->deserialize(
                json_encode($data, JSON_THROW_ON_ERROR),
                $class,
                'json',
                ['groups' => $groups],
            );
        } catch (\Throwable $exception) {
            throw new McpToolException(sprintf('Daten konnten nicht gelesen werden: %s', $exception->getMessage()));
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param list<string>         $groups
     */
    protected function deserializeInto(array $data, object $target, array $groups = ['api-write']): void
    {
        try {
            $this->serializer->deserializeInto(
                json_encode($data, JSON_THROW_ON_ERROR),
                $target,
                'json',
                ['groups' => $groups],
            );
        } catch (\Throwable $exception) {
            throw new McpToolException(sprintf('Daten konnten nicht gelesen werden: %s', $exception->getMessage()));
        }
    }

    protected function validateEntity(object $entity): void
    {
        $violations = $this->validator->validate($entity);

        if (0 === \count($violations)) {
            return;
        }

        $messages = [];
        foreach ($violations as $violation) {
            $messages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
        }

        throw new McpToolException('Validierung fehlgeschlagen: ' . implode('; ', $messages));
    }

    protected function persist(object $entity): void
    {
        $this->registry->getManager()->persist($entity);
    }

    protected function flush(): void
    {
        $this->registry->getManager()->flush();
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function ok(array $data): string
    {
        return json_encode(['status' => 'ok'] + $data, JSON_THROW_ON_ERROR);
    }
}
