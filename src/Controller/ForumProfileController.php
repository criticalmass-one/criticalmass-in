<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Was jemand im Forum geschrieben hat. Eine allgemeine Profilseite gibt es in der
 * Anwendung nicht — diese Seite bleibt bewusst auf das Forum beschränkt.
 */
class ForumProfileController extends AbstractController
{
    #[Route('/forum/user/{username}', name: 'caldera_criticalmass_forum_profile', priority: 240)]
    public function showAction(
        Request $request,
        PaginatorInterface $paginator,
        PostRepository $postRepository,
        #[MapEntity(mapping: ['username' => 'username'])] User $user
    ): Response {
        $posts = $paginator->paginate(
            $postRepository->queryForumPostsByUser($user),
            $request->query->getInt('page', 1),
            BoardController::POSTS_PER_PAGE
        );

        return $this->render('Forum/profile.html.twig', [
            'forumUser' => $user,
            'posts' => $posts,
        ]);
    }
}
