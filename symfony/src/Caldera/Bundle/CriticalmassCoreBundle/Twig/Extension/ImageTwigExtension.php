<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Twig\Extension;

use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageTwigExtension extends \Twig_Extension
{
    protected $uploaderHelper;
    protected $rootDirectory;

    public function __construct(UploaderHelper $uploaderHelper, $rootDirectory)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->rootDirectory = $rootDirectory . '/../web';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('photoPath', [$this, 'photoPath'], array(
                'is_safe' => array('html')
            ))
        ];
    }

    public function photoPath(Photo $photo, $type = null)
    {
        $filename = $this->uploaderHelper->asset($photo, 'imageFile');

        if ($type) {
            $filenameParts = explode('.', $filename);

            $newFilenameParts = array_slice($filenameParts, 0, count($filenameParts) - 1, true) +
                array('type' => $type) +
                array_slice($filenameParts, count($filenameParts) - 1, 1, true);

            $filename = implode('.', $newFilenameParts);
        }
        
        return $filename;
    }

    public function getName()
    {
        return 'image_extension';
    }
}

