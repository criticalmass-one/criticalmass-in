<?php declare(strict_types=1);

namespace App\Criticalmass\Router;

use App\Controller\BoardController;
use App\Entity\Post;
use App\Entity\Thread;
use App\Repository\PostRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Die Adresse eines Beitrags.
 *
 * Ein Beitrag hängt an genau einem Gegenstand — Thema, Tour, Stadt oder Foto —
 * und wird dort angezeigt, mit einem Anker auf den Beitrag selbst. In langen
 * Themen liegt er womöglich nicht auf der ersten Seite, deshalb reist die
 * Seitenzahl mit.
 */
class PostUrlGenerator
{
    public function __construct(
        private readonly ObjectRouterInterface $objectRouter,
        private readonly PostRepository $postRepository,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function generate(Post $post, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $postable = $post->getThread() ?? $post->getRide() ?? $post->getCity() ?? $post->getPhoto();

        // Die freien Beitraege von 2016 haengen an nichts und haben keine eigene Seite.
        if (null === $postable) {
            return $this->urlGenerator->generate('caldera_criticalmass_board_overview', [], $referenceType);
        }

        $url = $this->objectRouter->generate($postable, null, [], $referenceType);

        if ($postable instanceof Thread) {
            $page = (int) ceil($this->postRepository->findPositionInThread($post) / BoardController::POSTS_PER_PAGE);

            if ($page > 1) {
                $url .= (str_contains($url, '?') ? '&' : '?') . 'page=' . $page;
            }
        }

        return sprintf('%s#post-%d', $url, $post->getId());
    }
}
