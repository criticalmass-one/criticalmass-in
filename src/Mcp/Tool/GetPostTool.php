<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;

/**
 * Read-Tool: liefert einen einzelnen Forenbeitrag (Post) per ID.
 */
final class GetPostTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly CriticalSerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'get_post';
    }

    public function description(): string
    {
        return 'Liefert einen einzelnen Forenbeitrag (Post) per ID.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'postId' => ['type' => 'integer', 'description' => 'ID des Posts.'],
            ],
            'required' => ['postId'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::PostRead;
    }

    public function call(array $arguments): string
    {
        if (!isset($arguments['postId']) || !is_numeric($arguments['postId'])) {
            throw new McpToolException('postId ist erforderlich und muss eine Zahl sein.');
        }

        $post = $this->resolver->post((int) $arguments['postId']);

        return $this->serializer->serialize($post, 'json', ['groups' => ['post-list']]);
    }
}
