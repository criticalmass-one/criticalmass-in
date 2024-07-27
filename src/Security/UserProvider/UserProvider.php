<?php declare(strict_types=1);

namespace App\Security\UserProvider;

use App\Entity\User;
use App\Criticalmass\ProfilePhotoGenerator\ProfilePhotoGeneratorInterface;
use Doctrine\Persistence\ManagerRegistry;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider implements OAuthAwareUserProviderInterface
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private ProfilePhotoGeneratorInterface $profilePhotoGenerator,
        private ManagerRegistry $managerRegistry
    )
    {

    }

    public function connect(UserInterface $user, UserResponseInterface $response): void
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        $previousUser = $this->findUserBy([$property => $username]);

        if (null !== $previousUser) {
            $previousUser = $this->setServiceData($previousUser, $response, true);

            $this->updateUser($previousUser);
        }

        $user = $this->setServiceData($user, $response);

        $this->updateUser($user);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        $user = $this->findUserByUsername($response);

        if (null === $user) {
            $user = $this->createUser();

            $user = $this->setUserData($user, $response);

            $user = $this->setServiceData($user, $response);

            $this->updateUser();

            return $user;
        }

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
            ->setEnabled(true);

        $this->setupProfilePhoto($user);

        return $user;
    }

    protected function setServiceData(
        UserInterface $user,
        UserResponseInterface $response,
        bool $clear = false
    ): UserInterface {
        $username = $response->getUsername();
        $service = $response->getResourceOwner()->getName();

        $setter = sprintf('set%s', ucfirst($service));
        $setterId = sprintf('%sId', $setter);
        $setterToken = sprintf('%sAccessToken', $setter);

        if ($clear) {
            $user
                ->$setterId(null)
                ->$setterToken(null);
        } else {
            $user
                ->$setterId($username)
                ->$setterToken($response->getAccessToken());
        }

        return $user;
    }

    protected function findUserByUsername(UserResponseInterface $response): ?UserInterface
    {
        $service = $response->getResourceOwner()->getName();
        $serviceId = sprintf('%sId', strtolower($service));

        return $this->findUserBy([$serviceId => $response->getUsername()]);
    }

    protected function findUserByEmail(UserResponseInterface $response): ?UserInterface
    {
        return $this->findUserBy(['email' => $response->getEmail()]);
    }

    /**
     * @TODO This should be done via event subscriber during hwio process, but it does not work :(
     */
    protected function setupProfilePhoto(User $user): User
    {
        $this->profilePhotoGenerator
            ->setUser($user)
            ->generate();

        return $user;
    }

    private function createUser(): User
    {
        $user = new User();

        $this->managerRegistry->getManager()->persist($user);

        return $user;
    }

    private function updateUser(): void
    {
        $this->managerRegistry->getManager()->flush();
    }

    private function findUserBy(array $criteria): ?User
    {
        return $this->managerRegistry->getRepository(User::class)->findOneBy($criteria);
    }
}
