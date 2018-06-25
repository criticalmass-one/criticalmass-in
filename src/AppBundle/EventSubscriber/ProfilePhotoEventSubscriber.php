<?php declare(strict_types=1);

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\User;
use AppBundle\Criticalmass\ProfilePhotoGenerator\ProfilePhotoGenerator;
use Doctrine\Bundle\DoctrineBundle\Registry;
use FOS\UserBundle\Event\FormEvent as FosFormEvent;
use FOS\UserBundle\FOSUserEvents;
use HWI\Bundle\OAuthBundle\HWIOAuthEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use HWI\Bundle\OAuthBundle\Event\FormEvent as HwiFormEvent;

class ProfilePhotoEventSubscriber implements EventSubscriberInterface
{
    /** @var ProfilePhotoGenerator $profilePhotoGenerator */
    protected $profilePhotoGenerator;

    /** @var Registry $registry */
    protected $registry;

    public function __construct(ProfilePhotoGenerator $profilePhotoGenerator, Registry $registry)
    {
        $this->profilePhotoGenerator = $profilePhotoGenerator;
        $this->registry = $registry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FOSUserEvents::REGISTRATION_SUCCESS => 'onFosRegistrationSuccess',
            HWIOAuthEvents::CONNECT_INITIALIZE => 'onHwiRegistrationSuccess',
        ];
    }

    public function onFosRegistrationSuccess(FosFormEvent $event): void
    {
        $user = $event->getForm()->getData();

        $this->createProfilePhoto($user);
    }

    public function onHwiRegistrationSuccess(HwiFormEvent $event): void
    {
        $user = $user = $event->getForm()->getData();

        $this->createProfilePhoto($user);
    }

    protected function createProfilePhoto(User $user): void
    {
        $this->profilePhotoGenerator
            ->setUser($user)
            ->generate();

        $this->registry->getManager()->flush();
    }
}
