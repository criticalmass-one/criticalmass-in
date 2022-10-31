<?php declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractVoter extends Voter
{
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $canMethodName = $this->getCanMethodName($attribute);

        return $this->$canMethodName($subject, $user);
    }

    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, $this->getAccessRightAttributes())) {
            return false;
        }

        $fqcn = $this->getFqcn();

        if (!$subject instanceof $fqcn) {
            return false;
        }

        return true;
    }

    protected function getAccessRightAttributes(): array
    {
        $reflection = new \ReflectionClass($this);

        $attributeList = [];

        foreach ($reflection->getMethods() as $method) {
            preg_match('/^can([A-Za-z]+)$/', $method->getName(), $matches);

            if (2 === count($matches)) {
                $attributeList[] = lcfirst((string) array_pop($matches));
            }
        }

        return $attributeList;
    }

    protected function getFqcn(): string
    {
        $voterClassname = $this::class;

        preg_match('/(.*)\\\([A-Za-z].*)Voter/', $voterClassname, $matches);

        $entityClassName = array_pop($matches);

        $fqcn = sprintf('App\\Entity\\%s', $entityClassName);

        return $fqcn;
    }

    protected function getCanMethodName(string $attribute): string
    {
        return sprintf('can%s', ucfirst(strtolower($attribute)));
    }
}
