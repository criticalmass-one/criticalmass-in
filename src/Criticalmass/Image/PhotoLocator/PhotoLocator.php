<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoLocator;

use App\Entity\Photo;

class PhotoLocator extends AbstractPhotoLocator
{
    public function relocate(): PhotoLocatorInterface
    {
        $photoList = $this->doctrine->getRepository(Photo::class)->findPhotosByRide($this->ride);
        /** @todo only lookup users photos */

        /** @var Photo $photo */
        foreach ($photoList as $photo) {
            $this->photoGps
                ->setDateTimeZone($this->dateTimeZone)
                ->setPhoto($photo)
                ->setTrack($this->track);

            $this->photoGps->execute();

            $this->output->writeln(sprintf(
                'Updated location of photo <comment>#%d</comment> to <info>%f,%f</info>',
                $photo->getId(),
                $photo->getLatitude(),
                $photo->getLongitude()
            ));
        }

        $this->doctrine->getManager()->flush();

        return $this;
    }
}
