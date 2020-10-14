<?php declare(strict_types=1);

namespace Tests\Geo\LatLngListGenerator;

use App\Criticalmass\Geo\Entity\Track;
use App\Criticalmass\Geo\GpxReader\TrackReader;
use App\Criticalmass\Geo\LatLngListGenerator\SimpleLatLngListGenerator;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;

class SimpleLatLngListGeneratorTest extends TestCase
{
    public function testFoo(): void
    {
        $trackReader = new TrackReader($this->createFilesystemMock());

        $simpleLatLngListGenerator = new SimpleLatLngListGenerator($trackReader, 1);

        $track = new Track();
        $track->setTrackFilename('test.gpx');

        $actualList = $simpleLatLngListGenerator
            ->loadTrack($track)
            ->execute()
            ->getList();

        $expectedList = '[[52.50221,13.42493],[52.5022483,13.4249933],[52.5022967,13.4250517],[52.5023483,13.425095],[52.5023967,13.4251417],[52.5024483,13.4251733],[52.5024983,13.4252117],[52.50254,13.425255],[52.5025717,13.42533],[52.5026167,13.4253517],[52.50266,13.4253933],[52.5028583,13.425535],[52.5029117,13.42556],[52.50296,13.42559],[52.50301,13.4256233],[52.50306,13.42565],[52.5031017,13.4256917]]';

        $this->assertEquals($expectedList, $actualList);
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
      </trkpt>
      <trkpt lat="52.5022483" lon="13.4249933">
        <ele>-33.43</ele>
        <time>2017-04-28T18:26:37Z</time>
      </trkpt>
      <trkpt lat="52.5022967" lon="13.4250517">
        <ele>-32.43</ele>
        <time>2017-04-28T18:26:41Z</time>
      </trkpt>
      <trkpt lat="52.5023483" lon="13.425095">
        <ele>-31.73</ele>
        <time>2017-04-28T18:26:44Z</time>
      </trkpt>
      <trkpt lat="52.5023967" lon="13.4251417">
        <ele>-30.83</ele>
        <time>2017-04-28T18:26:47Z</time>
      </trkpt>
      <trkpt lat="52.5024483" lon="13.4251733">
        <ele>-29.93</ele>
        <time>2017-04-28T18:26:50Z</time>
      </trkpt>
      <trkpt lat="52.5024983" lon="13.4252117">
        <ele>-28.529999</ele>
        <time>2017-04-28T18:26:54Z</time>
      </trkpt>
      <trkpt lat="52.50254" lon="13.425255">
        <ele>-27.029999</ele>
        <time>2017-04-28T18:26:58Z</time>
      </trkpt>
      <trkpt lat="52.5025717" lon="13.42533">
        <ele>-25.83</ele>
        <time>2017-04-28T18:27:01Z</time>
      </trkpt>
      <trkpt lat="52.5026167" lon="13.4253517">
        <ele>-24.63</ele>
        <time>2017-04-28T18:27:04Z</time>
      </trkpt>
      <trkpt lat="52.50266" lon="13.4253933">
        <ele>-22.73</ele>
        <time>2017-04-28T18:27:09Z</time>
      </trkpt>
      <trkpt lat="52.5028583" lon="13.425535">
        <ele>-15.53</ele>
        <time>2017-04-28T18:27:31Z</time>
      </trkpt>
      <trkpt lat="52.5029117" lon="13.42556">
        <ele>-13.429999</ele>
        <time>2017-04-28T18:27:38Z</time>
      </trkpt>
      <trkpt lat="52.50296" lon="13.42559">
        <ele>-11.83</ele>
        <time>2017-04-28T18:27:42Z</time>
      </trkpt>
      <trkpt lat="52.50301" lon="13.4256233">
        <ele>-10.929999</ele>
        <time>2017-04-28T18:27:46Z</time>
      </trkpt>
      <trkpt lat="52.50306" lon="13.42565">
        <ele>-10.53</ele>
        <time>2017-04-28T18:27:49Z</time>
      </trkpt>
      <trkpt lat="52.5031017" lon="13.4256917">
        <ele>-10.23</ele>
        <time>2017-04-28T18:27:51Z</time>
      </trkpt>
    </trkseg>
  </trk>
</gpx>');

        return $filesystem;
    }
}