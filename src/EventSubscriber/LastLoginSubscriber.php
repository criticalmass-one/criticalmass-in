<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Records when a user last signed in — regardless of how.
 *
 * InteractiveLoginEvent is dispatched for every interactive authenticator, so
 * this covers the magic link, the OAuth logins (Facebook, Strava) and any
 * future one such as passkeys. It is deliberately NOT the LoginSuccessEvent:
 * that one also fires for the stateless bearer-token firewalls (/mcp, API),
 * which would mean a database write on every single API request.
 */
class LastLoginSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof User) {
            return;
        }

        $user->setLastLogin(new \DateTime());

        $this->registry->getManager()->flush();
    }
}
