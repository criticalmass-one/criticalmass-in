<?php declare(strict_types=1);

namespace App\Criticalmass\Forum;

use App\Controller\BoardController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Post;
use App\Entity\User;
use App\Notifier\ForumPostNotification;
use App\Repository\ForumSubscriptionRepository;
use App\Repository\PostRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Benachrichtigt die Abonnenten eines Themas über einen neuen Beitrag.
 *
 * Der Versand laeuft im Request. Symfony verschickt Mails hier synchron, weil in der
 * Produktion kein Messenger-Worker laeuft — SendEmailMessage auf den async-Transport zu
 * legen wuerde den Mailversand stilllegen statt beschleunigen. Bei sehr grossen
 * Abonnentenzahlen waere ein Worker der naechste Schritt.
 *
 * Drei Regeln bestimmen, wer eine Mail bekommt:
 * - Wer über mehrere Ebenen zugleich abonniert hat, bekommt trotzdem nur eine.
 * - Wer den Beitrag selbst geschrieben hat, bekommt keine.
 * - Wer den Hauptschalter im Konto ausgeschaltet hat, bekommt keine.
 */
class ForumNotifier
{
    public function __construct(
        private readonly ForumSubscriptionRepository $subscriptionRepository,
        private readonly PostRepository $postRepository,
        private readonly NotifierInterface $notifier,
        private readonly ObjectRouterInterface $objectRouter,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly LoggerInterface $logger,
        #[Autowire('%notification.mail.sender_address%')] private readonly string $senderAddress
    ) {
    }

    public function notifyAboutPost(Post $post): void
    {
        $thread = $post->getThread();

        if (null === $thread) {
            return;
        }

        $notification = new ForumPostNotification(
            $post,
            (string) $thread->getTitle(),
            $this->postUrl($post),
            $this->urlGenerator->generate('caldera_criticalmass_forum_subscriptions', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $this->senderAddress
        );

        foreach ($this->recipients($post) as $user) {
            try {
                $this->notifier->send($notification, new Recipient((string) $user->getEmail()));
            } catch (\Throwable $exception) {
                // Eine unzustellbare Mail darf das Schreiben eines Beitrags nicht verhindern.
                $this->logger->error('Forums-Benachrichtigung konnte nicht zugestellt werden', [
                    'user' => $user->getId(),
                    'post' => $post->getId(),
                    'exception' => $exception,
                ]);
            }
        }
    }

    /**
     * @return list<User>
     */
    public function recipients(Post $post): array
    {
        $thread = $post->getThread();

        if (null === $thread) {
            return [];
        }

        $author = $post->getUser();
        $recipients = [];

        foreach ($this->subscriptionRepository->findSubscribersForThread($thread) as $user) {
            if ($user === $author) {
                continue;
            }

            if (!$user->wantsForumNotifications()) {
                continue;
            }

            if (null === $user->getEmail()) {
                continue;
            }

            $recipients[$user->getId()] = $user;
        }

        return array_values($recipients);
    }

    private function postUrl(Post $post): string
    {
        $thread = $post->getThread();
        $url = $this->objectRouter->generate($thread, null, [], UrlGeneratorInterface::ABSOLUTE_URL);

        $page = (int) ceil($this->postRepository->findPositionInThread($post) / BoardController::POSTS_PER_PAGE);

        if ($page > 1) {
            $url .= (str_contains($url, '?') ? '&' : '?') . 'page=' . $page;
        }

        return sprintf('%s#post-%d', $url, $post->getId());
    }
}
