<?php

namespace Criticalmass\Bundle\AppBundle\Command;

use Criticalmass\Bundle\AppBundle\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use PHPExif\Reader\Reader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReloadImageExifCommand extends ContainerAwareCommand
{
    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * @var EntityManager $manager
     */
    protected $manager;

    protected function configure()
    {
        $this
            ->setName('criticalmass:images:reloadExif')
            ->setDescription('Regenerate LatLng Tracks');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $this->doctrine->getManager();

        $photos = $this->doctrine->getRepository('AppBundle:Photo')->findAll();
        
        $reader = Reader::factory(Reader::TYPE_NATIVE);

        /**
         * @var Photo $photo
         */
        foreach ($photos as $photo) {
            $path = $this->getContainer()->getParameter('kernel.root_dir') . '/../web/photos/' . $photo->getImageName();

            $exif = $reader->getExifFromFile($path);

            if ($dateTime = $exif->getCreationDate()) {
                $photo->setDateTime($dateTime);
            }
        }

        $this->manager->flush();
    }
}
