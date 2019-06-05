<?php declare(strict_types=1);

namespace Tests\Geo\PolylineGenerator;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\PolylineGenerator\PolylineGenerator;
use App\Criticalmass\Geo\PolylineGenerator\PolylineStrategy\FullPolylineStrategy;
use App\Criticalmass\Geo\PolylineGenerator\PolylineStrategy\ReducedPolylineStrategy;
use App\Criticalmass\Geo\PositionList\PositionList;
use PHPUnit\Framework\TestCase;

class PolylineStrategyTest extends TestCase
{
    public function testPolylineGenerator(): void
    {
        $polylineGenerator = new PolylineGenerator();
        $polyline = $polylineGenerator
            ->setStrategy(new FullPolylineStrategy())
            ->execute($this->createTestPositionList());

        $this->assertEquals('oizeIaca|@?H@H@HBF@HBDBHDHBRAT?L@N?LAPAJ?N?P@P?N@R@R@R@RBV@R@TBRBV@TBV@TBTDP@TBR@TDV@P@RDRDRFNBRBT@P@P@R?V@RBP?R@N@RF\FVFTDP@R@NAJBF?LBH?JBLDJ?JCJ?FAJ@V?X@T@T@T@R?VB\?P?J@RBJ@L@LHNJDDBDHFJJDHBF?D@NCJ@J@JDHBH?HKBCDABI@MBG?KBG?I@K', $polyline);
    }

    public function testPolylineGeneratorWithReducedStrategy(): void
    {
        $polylineGenerator = new PolylineGenerator();
        $polyline = $polylineGenerator
            ->setStrategy(new ReducedPolylineStrategy())
            ->execute($this->createTestPositionList());
        $this->assertEquals('oizeIaca|@', $polyline);
    }
    public function testPolylineGeneratorWithEmptyPositionList(): void
    {
        $polylineGenerator = new PolylineGenerator();
        $polyline = $polylineGenerator
            ->setStrategy(new FullPolylineStrategy())
            ->execute(new PositionList());
        $this->assertEquals('', $polyline);
    }
    protected function createTestPositionList(): PositionList
    {
        $positionList = new PositionList();
        $positionList
            ->add(new Position(53.5517630, 10.0051340))
            ->add(new Position(53.5517630, 10.0050800))
            ->add(new Position(53.5517460, 10.0050300))
            ->add(new Position(53.5517420, 10.0049810))
            ->add(new Position(53.5517230, 10.0049400))
            ->add(new Position(53.5517140, 10.0048940))
            ->add(new Position(53.5516870, 10.0048600))
            ->add(new Position(53.5516690, 10.0048130))
            ->add(new Position(53.5516360, 10.0047610))
            ->add(new Position(53.5516220, 10.0046560))
            ->add(new Position(53.5516270, 10.0045460))
            ->add(new Position(53.5516250, 10.0044810))
            ->add(new Position(53.5516190, 10.0044040))
            ->add(new Position(53.5516240, 10.0043250))
            ->add(new Position(53.5516310, 10.0042410))
            ->add(new Position(53.5516440, 10.0041760))
            ->add(new Position(53.5516370, 10.0041030))
            ->add(new Position(53.5516350, 10.0040130))
            ->add(new Position(53.5516290, 10.0039240))
            ->add(new Position(53.5516260, 10.0038370))
            ->add(new Position(53.5516150, 10.0037370))
            ->add(new Position(53.5516060, 10.0036350))
            ->add(new Position(53.5515980, 10.0035390))
            ->add(new Position(53.5515850, 10.0034350))
            ->add(new Position(53.5515740, 10.0033180))
            ->add(new Position(53.5515560, 10.0032160))
            ->add(new Position(53.5515450, 10.0031100))
            ->add(new Position(53.5515320, 10.0030060))
            ->add(new Position(53.5515120, 10.0028920))
            ->add(new Position(53.5514980, 10.0027790))
            ->add(new Position(53.5514830, 10.0026620))
            ->add(new Position(53.5514700, 10.0025520))
            ->add(new Position(53.5514540, 10.0024430))
            ->add(new Position(53.5514230, 10.0023470))
            ->add(new Position(53.5514120, 10.0022410))
            ->add(new Position(53.5513930, 10.0021380))
            ->add(new Position(53.5513770, 10.0020300))
            ->add(new Position(53.5513530, 10.0019120))
            ->add(new Position(53.5513440, 10.0018160))
            ->add(new Position(53.5513290, 10.0017210))
            ->add(new Position(53.5512950, 10.0016200))
            ->add(new Position(53.5512720, 10.0015170))
            ->add(new Position(53.5512280, 10.0014380))
            ->add(new Position(53.5512080, 10.0013370))
            ->add(new Position(53.5511850, 10.0012330))
            ->add(new Position(53.5511780, 10.0011410))
            ->add(new Position(53.5511670, 10.0010480))
            ->add(new Position(53.5511630, 10.0009500))
            ->add(new Position(53.5511570, 10.0008300))
            ->add(new Position(53.5511470, 10.0007270))
            ->add(new Position(53.5511340, 10.0006350))
            ->add(new Position(53.5511290, 10.0005440))
            ->add(new Position(53.5511210, 10.0004570))
            ->add(new Position(53.5511060, 10.0003580))
            ->add(new Position(53.5510730, 10.0002140))
            ->add(new Position(53.5510260, 10.0000870))
            ->add(new Position(53.5509890, 9.9999830))
            ->add(new Position(53.5509630, 9.9998860))
            ->add(new Position(53.5509460, 9.9997930))
            ->add(new Position(53.5509440, 9.9997140))
            ->add(new Position(53.5509470, 9.9996480))
            ->add(new Position(53.5509270, 9.9996130))
            ->add(new Position(53.5509270, 9.9995370))
            ->add(new Position(53.5509130, 9.9994850))
            ->add(new Position(53.5509110, 9.9994260))
            ->add(new Position(53.5508880, 9.9993560))
            ->add(new Position(53.5508640, 9.9992990))
            ->add(new Position(53.5508580, 9.9992350))
            ->add(new Position(53.5508800, 9.9991820))
            ->add(new Position(53.5508780, 9.9991360))
            ->add(new Position(53.5508880, 9.9990800))
            ->add(new Position(53.5508820, 9.9989580))
            ->add(new Position(53.5508750, 9.9988300))
            ->add(new Position(53.5508680, 9.9987240))
            ->add(new Position(53.5508560, 9.9986050))
            ->add(new Position(53.5508500, 9.9984990))
            ->add(new Position(53.5508440, 9.9983990))
            ->add(new Position(53.5508390, 9.9982820))
            ->add(new Position(53.5508190, 9.9981340))
            ->add(new Position(53.5508170, 9.9980380))
            ->add(new Position(53.5508210, 9.9979800))
            ->add(new Position(53.5508070, 9.9978780))
            ->add(new Position(53.5507920, 9.9978210))
            ->add(new Position(53.5507800, 9.9977480))
            ->add(new Position(53.5507680, 9.9976780))
            ->add(new Position(53.5507190, 9.9975970))
            ->add(new Position(53.5506620, 9.9975730))
            ->add(new Position(53.5506310, 9.9975460))
            ->add(new Position(53.5506040, 9.9975020))
            ->add(new Position(53.5505570, 9.9974440))
            ->add(new Position(53.5504970, 9.9974080))
            ->add(new Position(53.5504450, 9.9973920))
            ->add(new Position(53.5504090, 9.9973900))
            ->add(new Position(53.5503760, 9.9973790))
            ->add(new Position(53.5503010, 9.9974030))
            ->add(new Position(53.5502420, 9.9973930))
            ->add(new Position(53.5501780, 9.9973750))
            ->add(new Position(53.5501210, 9.9973510))
            ->add(new Position(53.5500680, 9.9973270))
            ->add(new Position(53.5500160, 9.9973310))
            ->add(new Position(53.5499730, 9.9973880))
            ->add(new Position(53.5499480, 9.9974100))
            ->add(new Position(53.5499210, 9.9974190))
            ->add(new Position(53.5499030, 9.9974700))
            ->add(new Position(53.5498900, 9.9975380))
            ->add(new Position(53.5498740, 9.9975770))
            ->add(new Position(53.5498670, 9.9976370))
            ->add(new Position(53.5498450, 9.9976800))
            ->add(new Position(53.5498510, 9.9977300))
            ->add(new Position(53.5498380, 9.9977910));
        
        return $positionList;
    }
}
