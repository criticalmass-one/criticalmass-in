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
        if (!$ride->getId()) {
            // ride is created, there may not be any rides at this date
            $maxRidesPerDay = 0;
        } else {
            // ride is edited, there may be the previous saved entity
            $maxRidesPerDay = 1;
        }

        $city = $ride->getCity();

        $rideList = $this->manager->getRepository('AppBundle:Ride')->findRidesForCity($city);

        $foundRidesForSameDay = 0;

        /** @var Ride $oldRide */
        foreach ($rideList as $oldRide) {
            if ($oldRide->getDateTime()->format('Y-m-d') === $ride->getDateTime()->format('Y-m-d')) {
                ++$foundRidesForSameDay;
            }

            if ($foundRidesForSameDay > $maxRidesPerDay) {
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
