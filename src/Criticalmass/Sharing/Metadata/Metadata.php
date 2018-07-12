<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing\Metadata;

use App\Criticalmass\Sharing\Annotation\Intro;
use App\Criticalmass\Sharing\Annotation\Title;
use App\Criticalmass\Sharing\ShareableInterface\Shareable;
use App\Criticalmass\Util\ClassUtil;

class Metadata extends AbstractMetadata
{
    public function getShareUrl(Shareable $shareable): string
    {
        $keyword = $this->checkShorturl($shareable);

        return $keyword;
    }

    public function getShareTitle(Shareable $shareable): string
    {
        $reflectionClass = new \ReflectionClass($shareable);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $key => $property) {
            $titleAnnotation = $this->annotationReader->getPropertyAnnotation($property, Title::class);

            if ($titleAnnotation) {
                $getMethodName = sprintf('get%s', ucfirst($property->getName()));

                if (!$reflectionClass->hasMethod($getMethodName)) {
                    continue;
                }

                return $shareable->$getMethodName();
            }
        }

        return sprintf('%s #%d', ClassUtil::getShortname($shareable), $shareable->getId());
    }

    public function getShareIntro(Shareable $shareable): ?string
    {
        $reflectionClass = new \ReflectionClass($shareable);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $key => $property) {
            $introAnnotation = $this->annotationReader->getPropertyAnnotation($property, Intro::class);

            if ($introAnnotation) {
                $getMethodName = sprintf('get%s', ucfirst($property->getName()));

                if (!$reflectionClass->hasMethod($getMethodName)) {
                    continue;
                }

                return $shareable->$getMethodName();
            }
        }

        return null;
    }

    public function getShorturl(Shareable $shareable): ?string
    {
        $permalinkPropertyName = $this->getShorturlPropertyName($shareable);
        $getMethodName = sprintf('get%s', ucfirst($permalinkPropertyName));

        return $shareable->$getMethodName();
    }

    public function setShorturl(Shareable $shareable, string $shorturl): Shareable
    {
        $permalinkPropertyName = $this->getShorturlPropertyName($shareable);
        $setMethodName = sprintf('set%s', ucfirst($permalinkPropertyName));

        return $shareable->$setMethodName($shorturl);
    }
}
