<?php declare(strict_types=1);

namespace App\Criticalmass\OrderedEntities\Annotation;

/**
 * @Annotation
 */
class Order extends AbstractAnnotation
{
    /** @var string $directory */
    protected $directory;

    public function getDirectory(): ?string
    {
        return $this->directory;
    }
}
