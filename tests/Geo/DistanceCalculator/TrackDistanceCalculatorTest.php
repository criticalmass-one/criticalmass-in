<?php declare(strict_types=1);

namespace Tests\Geo\DistanceCalculator;

use App\Criticalmass\Geo\DistanceCalculator\TrackDistanceCalculator;
use App\Criticalmass\Geo\Entity\Track;
use App\Criticalmass\Geo\GpxReader\TrackReader;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;

class TrackDistanceCalculatorTest extends TestCase
{
    public function testTrackDistanceCalculatorTestWithoutTrack(): void
    {
        $trackReader = new TrackReader($this->createFilesystemMock());

        $distanceCalculator = new TrackDistanceCalculator($trackReader);

        $actualDistance = $distanceCalculator->calculate();

        $this->assertEquals(0.0, $actualDistance);
    }

    public function testTrackDistanceCalculatorTestWithTrack(): void
    {
        $trackReader = new TrackReader($this->createFilesystemMock());
        $track = new Track();
        $track->setTrackFilename('test.gpx');

        $distanceCalculator = new TrackDistanceCalculator($trackReader);

        $actualDistance = $distanceCalculator
            ->setTrack($track)
            ->calculate();

        $this->assertEquals(0.11490919922802, $actualDistance);
    }

    protected function createFilesystemMock(): FilesystemInterface
    {
        $filesystem = $this->createMock(Filesystem::class);

        $filesystem->method('read')->willReturn('<?xml version=\'1.0\' encoding=\'UTF-8\' standalone=\'yes\' ?>
<gpx version="1.1" creator="OsmAnd+" xmlns="http://www.topografix.com/GPX/1/1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd">
   <metadata>
  <time>2016-11-25T15:39:38Z</time>
 </metadata>
 <trk>
    <trkseg>
      <trkpt lat="52.50221" lon="13.42493">
        <ele>-34.63</ele>
        <time>2017-04-28T18:26:33Z</time>
        <hdop>4.0</hdop>
        <extensions>
          <speed>1.1622427701950073</speed>
        </extensions>
      </trkpt>
      <trkpt lat="52.5022483" lon="13.4249933">
        <ele>-33.43</ele>
        <time>2017-04-28T18:26:37Z</time>
        <hdop>4.400000095367432</hdop>
        <extensions>
          <speed>1.3784260749816895</speed>
        </extensions>
      </trkpt>
      <trkpt lat="52.5022967" lon="13.4250517">
        <ele>-32.43</ele>
        <time>2017-04-28T18:26:41Z</time>
        <hdop>4.699999809265137</hdop>
        <extensions>
          <speed>1.5266661643981934</speed>
        </extensions>
      </trkpt>
      <trkpt lat="52.5023483" lon="13.425095">
        <ele>-31.73</ele>
        <time>2017-04-28T18:26:44Z</time>
        <hdop>5.699999809265137</hdop>
        <extensions>
          <speed>2.1798486709594727</speed>
        </extensions>
      </trkpt>
      <trkpt lat="52.5023967" lon="13.4251417">
        <ele>-30.83</ele>
        <time>2017-04-28T18:26:47Z</time>
        <hdop>5.400000095367432</hdop>
        <extensions>
          <speed>1.9894013404846191</speed>
        </extensions>
      </trkpt>
      <trkpt lat="52.5024483" lon="13.4251733">
        <ele>-29.93</ele>
        <time>2017-04-28T18:26:50Z</time>
        <hdop>4.900000095367432</hdop>
        <extensions>
          <speed>1.62086021900177</speed>
        </extensions>
      </trkpt>
      <trkpt lat="52.5024983" lon="13.4252117">
        <ele>-28.529999</ele>
        <time>2017-04-28T18:26:54Z</time>
        <hdop>4.400000095367432</hdop>
        <extensions>
          <speed>1.2379069328308105</speed>
        </extensions>
      </trkpt>
      <trkpt lat="52.50254" lon="13.425255">
        <ele>-27.029999</ele>
        <time>2017-04-28T18:26:58Z</time>
        <hdop>5.400000095367432</hdop>
        <extensions>
          <speed>1.1560660600662231</speed>
        </extensions>
      </trkpt>
      <trkpt lat="52.5025717" lon="13.42533">
        <ele>-25.83</ele>
        <time>2017-04-28T18:27:01Z</time>
        <hdop>5.199999809265137</hdop>
        <extensions>
          <speed>1.3176889419555664</speed>
        </extensions>
      </trkpt>
      <trkpt lat="52.5026167" lon="13.4253517">
        <ele>-24.63</ele>
        <time>2017-04-28T18:27:04Z</time>
        <hdop>4.699999809265137</hdop>
        <extensions>
          <speed>1.1452569961547852</speed>
        </extensions>
      </trkpt>
      <trkpt lat="52.50266" lon="13.4253933">
        <ele>-22.73</ele>
        <time>2017-04-28T18:27:09Z</time>
        <hdop>4.800000190734863</hdop>
        <extensions>
          <speed>1.2569516897201538</speed>
        </extensions>
      </trkpt>
      <trkpt lat="52.5028583" lon="13.425535">
        <ele>-15.53</ele>
        <time>2017-04-28T18:27:31Z</time>
        <hdop>4.400000095367432</hdop>
        <extensions>
          <speed>1.1133441925048828</speed>
        </extensions>
      </trkpt>
      <trkpt lat="52.5029117" lon="13.42556">
        <ele>-13.429999</ele>
        <time>2017-04-28T18:27:38Z</time>
        <hdop>4.400000095367432</hdop>
        <extensions>
          <speed>1.1684194803237915</speed>
        </extensions>
      </trkpt>
      <trkpt lat="52.50296" lon="13.42559">
        <ele>-11.83</ele>
        <time>2017-04-28T18:27:42Z</time>
        <hdop>4.300000190734863</hdop>
        <extensions>
          <speed>1.1637868881225586</speed>
        </extensions>
      </trkpt>
      <trkpt lat="52.50301" lon="13.4256233">
        <ele>-10.929999</ele>
        <time>2017-04-28T18:27:46Z</time>
        <hdop>4.599999904632568</hdop>
        <extensions>
          <speed>1.4149713516235352</speed>
        </extensions>
      </trkpt>
      <trkpt lat="52.50306" lon="13.42565">
        <ele>-10.53</ele>
        <time>2017-04-28T18:27:49Z</time>
        <hdop>5.900000095367432</hdop>
        <extensions>
          <speed>1.4757086038589478</speed>
        </extensions>
      </trkpt>
      <trkpt lat="52.5031017" lon="13.4256917">
        <ele>-10.23</ele>
        <time>2017-04-28T18:27:51Z</time>
        <hdop>6.0</hdop>
        <extensions>
          <speed>1.8365288972854614</speed>
        </extensions>
      </trkpt>
    </trkseg>
  </trk>
</gpx>');

        return $filesystem;
    }
}
