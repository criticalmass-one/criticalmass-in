<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Criticalmass\ProfilePhotoGenerator\ProfilePhotoGenerator;
use App\Event\User\UserColorChangedEvent;
use FOS\UserBundle\Event\FormEvent as FosFormEvent;
use FOS\UserBundle\FOSUserEvents;
use HWI\Bundle\OAuthBundle\HWIOAuthEvents;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use HWI\Bundle\OAuthBundle\Event\FormEvent as HwiFormEvent;

class ProfilePhotoEventSubscriber implements EventSubscriberInterface
{
    protected ProfilePhotoGenerator $profilePhotoGenerator;
    protected ManagerRegistry $registry;

    public function __construct(ProfilePhotoGenerator $profilePhotoGenerator, ManagerRegistry $registry)
    {
        $this->profilePhotoGenerator = $profilePhotoGenerator;
        $this->registry = $registry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FOSUserEvents::REGISTRATION_SUCCESS => 'onFosRegistrationSuccess',
            HWIOAuthEvents::CONNECT_INITIALIZE => 'onHwiRegistrationSuccess',
            UserColorChangedEvent::NAME => 'onUserColorChange',
        ];
    }

    public function onFosRegistrationSuccess(FosFormEvent $event): void
    {
        $user = $event->getForm()->getData();

        $this->createProfilePhoto($user);
    }

    public function onHwiRegistrationSuccess(HwiFormEvent $event): void
    {
        $user = $event->getForm()->getData();

        $this->createProfilePhoto($user);
    }

    public function onUserColorChange(UserColorChangedEvent $event): void
    {
        $user = $event->getUser();

        if (!$user->hasOwnProfilePhoto()) {
            $this->createProfilePhoto($user);
        }
    }

    protected function createProfilePhoto(User $user): void
    {
        $this->profilePhotoGenerator
            ->setUser($user)
            ->generate();

        $this->registry->getManager()->flush();
    }
}
