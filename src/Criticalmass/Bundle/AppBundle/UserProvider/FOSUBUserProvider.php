<?php

namespace AppBundle\UserProvider;

use AppBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{
    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser = $this->setServiceData($previousUser, $response, true);

            $this->userManager->updateUser($previousUser);
        }

        $user = $this->setServiceData($user, $response);

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $user = $this->findUserByUsername($response);

        if (null === $user) {
            if (null === $user) {
                $user = $this->findUserByEmail($response);
            } else {
                $user = $this->userManager->createUser();

                $user = $this->setUserData($user, $response);
            }

            $user = $this->setServiceData($user, $response);

            $this->userManager->updateUser($user);

            return $user;
        }

        $user = parent::loadUserByOAuthUserResponse($response);

        $user = $this->setServiceData($user, $response);

        return $user;
    }

    protected function setUserData(UserInterface $user, UserResponseInterface $response): UserInterface
    {
        $username = $response->getNickname() ? $response->getNickname() : $response->getUsername();
        $email = $response->getEmail() ? $response->getEmail() : $response->getUsername();

        $user
            ->setUsername($username)
            ->setEmail($email)
            ->setPassword('')
            ->setEnabled(true)
        ;

        return $user;
    }

    protected function setServiceData(UserInterface $user, UserResponseInterface $response, bool $clear = false): UserInterface
    {
        $username = $response->getUsername();
        $service = $response->getResourceOwner()->getName();

        $setter = 'set' . ucfirst($service);
        $setterId = $setter . 'Id';
        $setterToken = $setter . 'AccessToken';

        if ($clear) {
            $user->$setterId(null);
            $user->$setterToken(null);

        } else {
            $user->$setterId($username);
            $user->$setterToken($response->getAccessToken());
        }

        return $user;
    }

    protected function findUserByUsername(UserResponseInterface $response): ?UserInterface
    {
        return $this->userManager->findUserBy(['username' => $response->getUsername()]);
    }

    protected function findUserByEmail(UserResponseInterface $response): ?UserInterface
    {
        return $this->userManager->findUserBy(['email' => $response->getEmail()]);
    }
}