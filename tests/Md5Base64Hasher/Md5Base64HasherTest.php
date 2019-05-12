<?php declare(strict_types=1);

namespace Tests\Md5Base64HasherTest;

use App\Criticalmass\Image\GoogleCloud\ExportDataHandler\Md5Base64Hasher;
use PHPUnit\Framework\TestCase;

class Md5Base64HasherTest extends TestCase
{
    public function testGoogleFile(): void
    {
        $file = "Storage Transfer MD5 Test\n";
        
        $this->assertEquals('BfnRTwvHpofMOn2Pq7EVyQ==', Md5Base64Hasher::hash($file));
    }
}
