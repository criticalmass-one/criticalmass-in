<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use Flagception\Manager\FeatureManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Statusbeitraege ueber die Stadtseite hinaus: alle Staedte zusammen und alles,
 * was eine Person geschrieben hat.
 *
 * Geschrieben wird weiterhin nur auf der Stadtseite — jeder Beitrag gehoert zu
 * einer Stadt.
 */
class StatusPostController extends AbstractController
{
    public const int POSTS_PER_PAGE = 20;

    #[Route('/beitraege', name: 'caldera_criticalmass_status_feed', priority: 240)]
    public function feedAction(
        Request $request,
        PaginatorInterface $paginator,
        PostRepository $postRepository,
        FeatureManagerInterface $featureManager
    ): Response {
        $this->denyUnlessActive($featureManager);

        return $this->render('StatusPost/feed.html.twig', [
            'posts' => $paginator->paginate(
                $postRepository->queryStatusPosts(),
                $request->query->getInt('page', 1),
                self::POSTS_PER_PAGE
            ),
        ]);
    }

    #[Route('/beitraege/{username}', name: 'caldera_criticalmass_status_user', priority: 240)]
    public function userAction(
        Request $request,
        PaginatorInterface $paginator,
        PostRepository $postRepository,
        FeatureManagerInterface $featureManager,
        #[MapEntity(mapping: ['username' => 'username'])] User $user
    ): Response {
        $this->denyUnlessActive($featureManager);

        return $this->render('StatusPost/user.html.twig', [
            'author' => $user,
            'posts' => $paginator->paginate(
                $postRepository->queryStatusPosts($user),
                $request->query->getInt('page', 1),
                self::POSTS_PER_PAGE
            ),
        ]);
    }

    private function denyUnlessActive(FeatureManagerInterface $featureManager): void
    {
        if (!$featureManager->isActive('status_posts')) {
            throw $this->createNotFoundException();
        }
    }
}
