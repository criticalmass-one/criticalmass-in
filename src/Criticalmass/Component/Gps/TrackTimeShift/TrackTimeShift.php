<?php declare(strict_types=1);

namespace Criticalmass\Component\Gps\TrackTimeShift;

use Criticalmass\Bundle\AppBundle\Entity\Position;

/**
 * @deprecated
 */
class TrackTimeShift extends AbstractTrackTimeshift
{
    public function shift(\DateInterval $interval): TrackTimeShiftInterface
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
