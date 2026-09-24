<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Ride;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[AsDoctrineListener(event: Events::preUpdate)]
class RideImageCacheSubscriber
{
    public function __construct(
        private readonly CacheManager $cacheManager,
        private readonly UploaderHelper $uploaderHelper,
    ) {
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Ride) {
            return;
        }

        if (!$args->hasChangedField('imageName')) {
            return;
        }

        $oldImageName = $args->getOldValue('imageName');

        if (null === $oldImageName) {
            return;
        }

        // Entity already has the new imageName; temporarily restore the old one to resolve the asset path
        $newImageName = $entity->getImageName();
        $entity->setImageName($oldImageName);
        $oldPath = $this->uploaderHelper->asset($entity, 'imageFile');
        $entity->setImageName($newImageName);

        if (null !== $oldPath) {
            $this->cacheManager->remove($oldPath);
        }
    }
}
