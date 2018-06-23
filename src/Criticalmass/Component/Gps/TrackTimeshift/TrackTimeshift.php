<?php declare(strict_types=1);

namespace Criticalmass\Component\Gps\TrackTimeshift;

use Criticalmass\Bundle\AppBundle\Entity\Position;

/**
 * @deprecated
 */
class TrackTimeshift extends AbstractTrackTimeshift
{
    public function shift(\DateInterval $interval): TrackTimeshiftInterface
    {
        /** @var Position $position */
        foreach ($this->positionArray as $position) {
            $dateTime = new \DateTime();
            $dateTime->setTimestamp($position->getTimestamp());
            $dateTime->sub($interval);
            $position->setTimestamp($dateTime->getTimestamp());
        }

        return $this;
    }
}
