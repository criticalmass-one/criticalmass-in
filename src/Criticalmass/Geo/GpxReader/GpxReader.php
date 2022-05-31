<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\GpxReader;

use App\Criticalmass\Geo\Exception\GpxFileNotFoundException;
use League\Flysystem\FilesystemInterface;

class GpxReader implements GpxReaderInterface
{
    /** @var \SimpleXMLElement $rootNode */
    protected $rootNode;

    /** @var \SimpleXMLElement[]  $trackPointList */
    protected $trackPointList = [];

    /** @var FilesystemInterface $filesystem */
    protected $filesystem;

    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function loadFromString(string $gpxString): GpxReaderInterface
    {
        $this->prepareGpx($gpxString);

        return $this;
    }

    public function loadFromFile(string $filename): GpxReaderInterface
    {
        try {
            $gpxString = $this->filesystem->read($filename);
        } catch (\Exception $exception) {
            throw new GpxFileNotFoundException(sprintf('File %s was not found.', $filename));
        }

        $this->prepareGpx($gpxString);

        return $this;
    }

    protected function prepareGpx(string $xmlString): GpxReader
    {
        $this->rootNode = new \SimpleXMLElement($xmlString);

        $this->registerXpathNamespace('gpx', 'http://www.topografix.com/GPX/1/1');

        $this->createTrackPointList();

        return $this;
    }

    protected function registerXpathNamespace(string $prefix, string $namespace): GpxReader
    {
        $this->rootNode->registerXPathNamespace($prefix, $namespace);

        return $this;
    }

    protected function createTrackPointList(): GpxReader
    {
        $this->trackPointList = $this->rootNode->xpath('//gpx:trkpt');

        return $this;
    }

    public function getRootNode(): \SimpleXMLElement
    {
        return $this->rootNode;
    }

    public function getCreationDateTime(): \DateTime
    {
        return new \DateTime((string) $this->rootNode->metadata->time);
    }

    public function getStartDateTime(): \DateTime
    {
        return new \DateTime((string) $this->trackPointList[0]->time);
    }

    public function getEndDateTime(): \DateTime
    {
        $lastTrackPointNumber = count($this->rootNode->trk->trkseg->trkpt) - 1;

        return new \DateTime((string) $this->trackPointList[$lastTrackPointNumber]->time);
    }

    public function countPoints(): int
    {
        return count($this->trackPointList);
    }

    public function getLatitudeOfPoint(int $n): float
    {
        return (float) $this->trackPointList[$n]['lat'];
    }

    public function getLongitudeOfPoint(int $n): float
    {
        return (float) $this->trackPointList[$n]['lon'];
    }

    public function getElevationOfPoint(int $n): float
    {
        return (float) $this->trackPointList[$n]->ele[0];
    }

    public function getDateTimeOfPoint(int $n): \DateTime
    {
        return new \DateTime((string) $this->trackPointList[$n]->time);
    }

    public function getPoint(int $n): \SimpleXMLElement
    {
        return $this->trackPointList[$n];
    }
}
