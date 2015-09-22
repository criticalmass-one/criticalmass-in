<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Uploader\TrackUploader;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\GpxReader;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngArrayGenerator\SimpleLatLngArrayGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Uploader\UploaderInterface;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Application\Sonata\UserBundle\Entity\User;

class TrackUploader implements UploaderInterface {
    protected $track;
    protected $options;
    protected $doctrine;
    protected $user;
    protected $ride;
    
    public function __construct(Registry $doctrine, array $options)
    {
        $this->doctrine = $doctrine;

        $this->resolveOptions($options);
    }

    protected function resolveOptions(array $options)
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired([
            'trackDirectory'
        ]);

        $resolver->setAllowedTypes([
            'trackDirectory' => 'string'
        ]);

        $this->options = $resolver->resolve($options);
    }
    
    public function setUser(User $user)
    {
        $this->user = $user;
        
        return $this;
    }
    
    public function setRide(Ride $ride)
    {
        $this->ride = $ride;
        
        return $this;        
    }
    
    public function setTrack(Track $track)
    {
        $this->track = $track;
        
        return $this;
    }
    
    public function processUpload()
    {
        $gpxReader = new GpxReader();
        $gpxReader->loadFile($this->track->getFile());
        $this->setProperties($gpxReader);
        
        $this->saveFile();
    }
    
    
    protected function saveFile()
    {
        $directory = $this->options['trackDirectory'];

        $filename = $directory.$this->track->getId();

        if (!$handle = fopen($filename.'.gpx', "a")) {
            exit;
        }

        if (!fwrite($handle, $this->track->getGpx())) {
            exit;
        }

        fclose($handle);
    }
    
    protected function setProperties(GpxReader $gpxReader)
    {
        $this->track->setUser($this->user);
        $this->track->setUsername($this->user->getUsername());
        $this->track->setRide($this->ride);
        
        $this->track->setStartDateTime($gpxReader->getStartDateTime());
        $this->track->setEndDateTime($gpxReader->getEndDateTime());
        $this->track->setPoints($gpxReader->countPoints());
        $this->track->setMd5Hash($gpxReader->getMd5Hash());
        $this->track->setGpx($gpxReader->getFileContent());
        $this->track->setDistance($gpxReader->calculateDistance());
        $this->track->setActivated(true);

        $sag = new SimpleLatLngArrayGenerator();
        $sag->loadTrack($this->track);
        $sag->execute();

        $this->track->setPreviewJsonArray($sag->getJsonArray());
    }
    
    
}