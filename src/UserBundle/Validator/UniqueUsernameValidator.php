<?php

namespace UserBundle\Validator;

use AppBundle\Entity\Ride;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueUsernameValidator extends ConstraintValidator
{
    protected $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @var string $username
     */
    public function validate($username, Constraint $constraint): void
    {
        $user = $this->userManager->findUserByUsername($username);

        if ($user) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->atPath('username')
                ->addViolation()
            ;
        }
    }
}
