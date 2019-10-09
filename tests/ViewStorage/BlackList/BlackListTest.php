<?php declare(strict_types=1);

namespace Tests\ViewStorage\BlackList;

use App\Criticalmass\ViewStorage\BlackList\BlackList;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class BlackListTest extends TestCase
{
    public function testHostnameIsBlackListed(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method($this->equalTo('getHost'))
            ->will($this->returnValue('engine42.uptimerobot.com'));

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $blackList = new BlackList($requestStack);

        $this->assertTrue($blackList->isBlackListed());
    }

    public function testHostnameIsNotBlackListed(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method($this->equalTo('getHost'))
            ->will($this->returnValue('laurentina.caldera.cc'));

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $blackList = new BlackList($requestStack);

        $this->assertFalse($blackList->isBlackListed());
    }
}
