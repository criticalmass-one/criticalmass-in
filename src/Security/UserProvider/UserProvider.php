<?php declare(strict_types=1);

namespace App\Security\UserProvider;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface, OAuthAwareUserProviderInterface
{
    private ObjectManager $em;
    private string $class = User::class;
    private ?ObjectRepository $repository = null;

    /**
     * @var array<string, string>
     */
    private array $properties = [
        'identifier' => 'id',
    ];

    /**
     * @param string                $class      User entity class to load
     * @param array<string, string> $properties Mapping of resource owners to properties
     */
    public function __construct(ManagerRegistry $registry, array $properties, ?string $managerName = null)
    {
        $this->em = $registry->getManager($managerName);
        $this->properties = array_merge($this->properties, $properties);
    }

    /**
     * Erst genau suchen, dann schreibungsblind.
     *
     * MySQL vergleicht Text ueber die Kollation ohnehin schreibungsblind,
     * PostgreSQL nicht. Ohne den zweiten Versuch kaeme sich nach dem
     * Plattformwechsel niemand mehr anmelden, der seinen Namen anders schreibt
     * als er gespeichert ist — auf der Produktion tragen 3981 von 21140
     * Benutzernamen Grossbuchstaben.
     *
     * Der genaue Treffer behaelt Vorrang. Gaebe es je zwei Konten, die sich nur
     * in der Schreibung unterscheiden — heute gibt es keins —, bliebe die
     * Anmeldung damit eindeutig statt zufaellig.
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->findUser(['username' => $identifier])
            ?? $this->findUserIgnoringCase($identifier);

        if (!$user) {
            throw $this->createUserNotFoundException($identifier, sprintf("User '%s' not found.", $identifier));
        }

        return $user;
    }

    private function findUserIgnoringCase(string $identifier): ?UserInterface
    {
        $repository = $this->repository();

        if (!$repository instanceof EntityRepository) {
            return null;
        }

        $treffer = $repository->createQueryBuilder('u')
            ->where('LOWER(u.username) = :username')
            ->setParameter('username', mb_strtolower($identifier))
            // Zwei holen, um Mehrdeutigkeit zu erkennen, statt sie zu uebersehen.
            ->setMaxResults(2)
            ->getQuery()
            ->getResult();

        // Bei mehr als einem Treffer ist nicht entschieden, wer gemeint ist.
        // Dann niemanden einlassen, statt den Falschen.
        return 1 === count($treffer) ? $treffer[0] : null;
    }

    /**
     * Symfony <5.4 BC layer.
     *
     * @param string $username
     *
     * @return UserInterface
     */
    public function loadUserByUsername($username)
    {
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): ?UserInterface
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
        }

        $username = method_exists($response, 'getUserIdentifier') ? $response->getUserIdentifier() : $response->getUsername();

        if (null === $user = $this->findUser([$this->properties[$resourceOwnerName] => $username])) {
            $user = $this->createUser($response, $resourceOwnerName, $username);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $identifier = $this->properties['identifier'];
        if (!$accessor->isReadable($user, $identifier) || !$this->supportsClass($user::class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $userId = $accessor->getValue($user, $identifier);

        $username = $user->getUserIdentifier();

        if (null === $user = $this->findUser([$identifier => $userId])) {
            throw $this->createUserNotFoundException($username, sprintf('User with ID "%d" could not be reloaded.', $userId));
        }

        return $user;
    }

    public function supportsClass($class): bool
    {
        return $class === $this->class || is_subclass_of($class, $this->class);
    }

    private function findUser(array $criteria): ?UserInterface
    {
        return $this->repository()->findOneBy($criteria);
    }

    /**
     * @return ObjectRepository<User>
     */
    private function repository(): ObjectRepository
    {
        if (null === $this->repository) {
            $this->repository = $this->em->getRepository($this->class);
        }

        return $this->repository;
    }

    private function createUserNotFoundException(string $username, string $message): UserNotFoundException
    {
        $exception = new AccountNotLinkedException($message);
        $exception->setUserIdentifier($username);

        return $exception;
    }

    private function createUser(UserResponseInterface $response, string $resourceOwnerName, string $username): User
    {
        $user = new User();

        $accessor = PropertyAccess::createPropertyAccessor();
        $accessor->setValue($user, $this->properties[$resourceOwnerName], $username);

        $nickname = $response->getNickname();

        if (!empty($nickname)) {
            $user->setUsername($nickname);
        }

        $user
            ->setEnabled(true)
            ->setLastLogin(new \DateTime())
        ;

        if ($response->getEmail()) {
            $user->setEmail($response->getEmail());
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
