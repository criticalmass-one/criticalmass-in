<?php declare(strict_types=1);

namespace Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Controller\Api\Util\IdKiller;

class AbstractApiControllerTest extends WebTestCase
{
    public function assertIdLessJsonEquals(string $expected, string $actual): void
    {
        $this->assertEquals(IdKiller::removeIds($expected), IdKiller::removeIds($actual));
    }
}