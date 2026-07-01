<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Write-Tool: aktualisiert einen bestehenden Forenbeitrag (per ID).
 */
final class UpdatePostTool extends AbstractWriteTool
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
        return 'update_post';
    }

    public function description(): string
    {
        return 'Aktualisiert einen bestehenden Forenbeitrag (per ID): message, enabled, Koordinaten.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'postId' => ['type' => 'integer', 'description' => 'ID des Posts.'],
                'post' => [
                    'type' => 'object',
                    'description' => 'Zu ändernde Felder: message, enabled, latitude, longitude, dateTime (ISO 8601).',
                ],
            ],
            'required' => ['postId', 'post'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::PostWrite;
    }

    public function call(array $arguments): string
    {
        if (!isset($arguments['postId']) || !is_numeric($arguments['postId'])) {
            throw new McpToolException('postId ist erforderlich und muss eine Zahl sein.');
        }

        $post = $this->resolver->post((int) $arguments['postId']);
        $data = \is_array($arguments['post'] ?? null) ? $arguments['post'] : [];

        if (array_key_exists('message', $data)) {
            $post->setMessage((string) $data['message']);
        }

        if (array_key_exists('enabled', $data)) {
            if (!\is_bool($data['enabled'])) {
                throw new McpToolException('post.enabled muss ein Boolean sein.');
            }
            $post->setEnabled($data['enabled']);
        }

        if (array_key_exists('latitude', $data)) {
            $post->setLatitude((float) $data['latitude']);
        }

        if (array_key_exists('longitude', $data)) {
            $post->setLongitude((float) $data['longitude']);
        }

        if (array_key_exists('dateTime', $data)) {
            try {
                $post->setDateTime(new \DateTime((string) $data['dateTime']));
            } catch (\Exception) {
                throw new McpToolException('post.dateTime ist kein gültiger Zeitpunkt.');
            }
        }

        $this->validateEntity($post);
        $this->flush();

        return $this->serializer->serialize($post, 'json', ['groups' => ['post-list']]);
    }
}
