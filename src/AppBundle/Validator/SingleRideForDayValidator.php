<?php

namespace AppBundle\Validator;

use AppBundle\Entity\Ride;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SingleRideForDayValidator extends ConstraintValidator
{
    protected $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @var Ride $ride
     */
    public function validate($ride, Constraint $constraint): void
    {
        $city = $ride->getCity();

        $rideList = $this->manager->getRepository('AppBundle:Ride')->findRidesForCity($city);

        /** @var Ride $oldRide */
        foreach ($rideList as $oldRide) {
            if ($oldRide->getDateTime()->format('Y-m-d') === $ride->getDateTime()->format('Y-m-d')) {
                $this
                    ->context
                    ->buildViolation($constraint->message)
                    ->atPath('dateTime')
                    ->addViolation();

                break;
            }
        }
    }
}
