<?php declare(strict_types=1);

namespace App\Controller;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Photo;
use App\EntityInterface\PostableInterface;
use App\Criticalmass\Util\ClassUtil;
use App\Repository\PostRepository;
use App\Entity\City;
use App\Entity\Post;
use App\Entity\Ride;
use App\Entity\Thread;
use App\EntityInterface\BoardInterface;
use App\Form\Type\PostType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

class PostController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/post/write/city/{id}', name: 'caldera_criticalmass_timeline_post_write_city', priority: 120)]
    public function writeCityAction(Request $request, City $city, ObjectRouterInterface $objectRouter): Response
    {
        return $this->writeAction($request, $city, $objectRouter);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/post/write/ride/{id}', name: 'caldera_criticalmass_timeline_post_write_ride', priority: 120)]
    public function writeRideAction(Request $request, Ride $ride, ObjectRouterInterface $objectRouter): Response
    {
        return $this->writeAction($request, $ride, $objectRouter);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/post/write/photo/{id}', name: 'caldera_criticalmass_timeline_post_write_photo', priority: 120)]
    public function writePhotoAction(Request $request, Photo $photo, ObjectRouterInterface $objectRouter): Response
    {
        return $this->writeAction($request, $photo, $objectRouter);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/post/write/thread/{threadSlug}', name: 'caldera_criticalmass_timeline_post_write_thread', priority: 120)]
    public function writeThreadAction(
        Request $request,
        #[MapEntity(mapping: ['threadSlug' => 'slug'])] Thread $thread = null,
        ObjectRouterInterface $objectRouter
    ): Response {
        return $this->writeAction($request, $thread, $objectRouter);
    }

    #[Route('/post/write', name: 'caldera_criticalmass_timeline_post_write', priority: 120)]
    public function writeAction(Request $request, PostableInterface $postable, ObjectRouterInterface $objectRouter): Response
    {
        $post = $this->createPostForPostable($postable);
        $form = $this->getPostForm($postable, $post);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->addPostAction($request, $form, $post, $postable, $objectRouter);
        } else {
            return $this->addGetAction($request, $form, $post, $postable, $objectRouter);
        }
    }

    protected function addGetAction(Request $request, FormInterface $form, Post $post, PostableInterface $postable, ObjectRouterInterface $objectRouter): Response
    {
        return $this->render('Post/write.html.twig', [
            'form' => $form->createView()
        ]);
    }

    protected function addPostAction(Request $request, FormInterface $form, Post $post, PostableInterface $postable, ObjectRouterInterface $objectRouter): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();

            $post->setUser($this->getUser());
            $em->persist($post);

            // Threads: zusätzliche Logik
            if ($postable instanceof Thread) {
                $postable
                    ->setLastPost($post)
                    ->incPostNumber();

                /** @var BoardInterface $board */
                if ($postable->getBoard()) {
                    $board = $postable->getBoard();
                } else {
                    $board = $postable->getCity();
                }

                $board->incPostNumber();
                $board->setLastThread($postable);
            }

            $em->flush();

            return $this->redirect($objectRouter->generate($postable));
        }

        return $this->render('Post/write_failed.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function listAction(
        PostRepository $postRepository,
        int $cityId = null,
        int $rideId = null,
        int $photoId = null
    ): Response {
        $criteria = ['enabled' => true];

        if ($cityId) {
            $criteria['city'] = $cityId;
        }

        if ($rideId) {
            $criteria['ride'] = $rideId;
        }

        if ($photoId) {
            $criteria['photo'] = $photoId;
        }

        $posts = $postRepository->findBy($criteria, ['dateTime' => 'DESC']);

        return $this->render('Post/list.html.twig', ['posts' => $posts]);
    }

    protected function getPostForm(PostableInterface $postable, Post $post = null): FormInterface
    {
        if (!$post) {
            $post = new Post();
        }

        return $this->createForm(PostType::class, $post, [
            'action' => $this->generateActionUrl($postable),
        ]);
    }

    protected function generateActionUrl(PostableInterface $postable): string
    {
        $lcShortname = ClassUtil::getLowercaseShortname($postable);

        $routeName = sprintf('caldera_criticalmass_timeline_post_write_%s', $lcShortname);
        $parameter = ['id' => $postable->getId()];

        if ($postable instanceof Thread) {
            $parameter = ['threadSlug' => $postable->getSlug()];
        }

        return $this->generateUrl($routeName, $parameter);
    }

    protected function createPostForPostable(PostableInterface $postable): Post
    {
        $post = new Post();

        $shortname = ClassUtil::getShortname($postable);
        $setMethodName = sprintf('set%s', $shortname);

        $post->$setMethodName($postable);

        return $post;
    }
}
