<?php declare(strict_types=1);

namespace App\Menu;

use App\Entity\User;
use Flagception\Manager\FeatureManagerInterface;
use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class AbstractBuilder
{
    /** @var FactoryInterface $factory */
    protected $factory;

    /** @var TokenStorageInterface $tokenStorage */
    protected $tokenStorage;

    /** @var FeatureManagerInterface $featureManager */
    protected $featureManager;

    public function __construct(FactoryInterface $factory, TokenStorageInterface $tokenStorage, FeatureManagerInterface $featureManager)
    {
        $this->factory = $factory;
        $this->tokenStorage = $tokenStorage;
        $this->featureManager = $featureManager;
    }

    protected function isUserLoggedIn(): bool
    {
        $user = $this->getUser();

        return null !== $user;
    }

    protected function getUser(): ?User
    {
        $token = $this->tokenStorage->getToken();

        if ($token && is_object($token->getUser())) {
            return $token->getUser();
        }

        return null;
    }
}
