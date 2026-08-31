<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\Post;
use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Write-Tool: löscht einen Forenbeitrag (per ID). Antworten (child posts)
 * werden vorher vom Elternbeitrag entkoppelt, damit sie erhalten bleiben.
 */
final class DeletePostTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly ManagerRegistry $registry,
    ) {
    }

    public function name(): string
    {
        return 'delete_post';
    }

    public function description(): string
    {
        return 'Löscht einen Forenbeitrag (per ID). Antworten bleiben erhalten (werden entkoppelt).';
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
        return OAuthScope::PostWrite;
    }

    public function call(array $arguments): string
    {
        if (!isset($arguments['postId']) || !is_numeric($arguments['postId'])) {
            throw new McpToolException('postId ist erforderlich und muss eine Zahl sein.');
        }

        $post = $this->resolver->post((int) $arguments['postId']);
        $id = $post->getId();

        $manager = $this->registry->getManager();

        // Antworten entkoppeln (parent = null), damit sie erhalten bleiben.
        $children = $manager->getRepository(Post::class)->findBy(['parent' => $post]);
        foreach ($children as $child) {
            $child->setParent(null);
        }
        $manager->flush();

        $manager->remove($post);
        $manager->flush();

        return json_encode(['status' => 'ok', 'deletedPostId' => $id], JSON_THROW_ON_ERROR);
    }
}
