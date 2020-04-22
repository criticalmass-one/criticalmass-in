<?php declare(strict_types=1);

namespace Tests\Embed;

use App\Criticalmass\Embed\LinkFinder\LinkFinder;
use PHPUnit\Framework\TestCase;

class LinkFinderTest extends TestCase
{
    public function testEmptyString(): void
    {
        $text = '';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(0, $linkList);
    }

    public function testUrlWithHttpsAndWwwAndPath(): void
    {
        $text = 'https://www.criticalmass.in/hamburg/2011-06-24';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['https://www.criticalmass.in/hamburg/2011-06-24'], $linkList);
    }

    public function testUrlWithHttpAndWwwAndPath(): void
    {
        $text = 'http://www.criticalmass.in/hamburg/2011-06-24';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['http://www.criticalmass.in/hamburg/2011-06-24'], $linkList);
    }

    public function testUrlWithWwwAndPath(): void
    {
        $text = 'www.criticalmass.in/hamburg/2011-06-24';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['www.criticalmass.in/hamburg/2011-06-24'], $linkList);
    }

    public function testUrlWithHttpsAndPath(): void
    {
        $text = 'https://criticalmass.in/hamburg/2011-06-24';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['https://criticalmass.in/hamburg/2011-06-24'], $linkList);
    }

    public function testUrlWithHttpAndPath(): void
    {
        $text = 'http://criticalmass.in/hamburg/2011-06-24';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['http://criticalmass.in/hamburg/2011-06-24'], $linkList);
    }

    public function testUrlWithPath(): void
    {
        $text = 'criticalmass.in/hamburg/2011-06-24';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['criticalmass.in/hamburg/2011-06-24'], $linkList);
    }

    public function testQueryString(): void
    {
        $text = 'https://www.criticalmass.in/hamburg/2011-06-24?id=2';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['https://www.criticalmass.in/hamburg/2011-06-24?id=2'], $linkList);
    }

    public function testQueryAnchor(): void
    {
        $text = 'https://www.criticalmass.in/hamburg/2011-06-24#headline';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['https://www.criticalmass.in/hamburg/2011-06-24#headline'], $linkList);
    }

    public function testEmptyLipsum(): void
    {
        $text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut et nulla mollis, feugiat metus at, commodo odio. Proin et finibus velit. Curabitur cursus fringilla urna sed malesuada. Fusce non porta dui. Duis at dolor venenatis, finibus urna sed, pellentesque augue. Nam finibus lorem id feugiat tincidunt. Nam dignissim ultrices nisl, et tincidunt mi accumsan in. Quisque rutrum leo a tortor consectetur pulvinar. Sed posuere erat fringilla, cursus nisi consectetur, porta tellus. Aliquam erat volutpat. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum aliquam ullamcorper elit, eget egestas tellus tempor eget. Donec porttitor semper orci, vel dictum sem pretium et.';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(0, $linkList);
    }

    public function testLipsumWithPrependedSpaceAndLink(): void
    {
        $text = 'https://www.criticalmass.in/hamburg/2011-06-24 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut et nulla mollis, feugiat metus at, commodo odio. Proin et finibus velit. Curabitur cursus fringilla urna sed malesuada. Fusce non porta dui. Duis at dolor venenatis, finibus urna sed, pellentesque augue. Nam finibus lorem id feugiat tincidunt. Nam dignissim ultrices nisl, et tincidunt mi accumsan in. Quisque rutrum leo a tortor consectetur pulvinar. Sed posuere erat fringilla, cursus nisi consectetur, porta tellus. Aliquam erat volutpat. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum aliquam ullamcorper elit, eget egestas tellus tempor eget. Donec porttitor semper orci, vel dictum sem pretium et.';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['https://www.criticalmass.in/hamburg/2011-06-24'], $linkList);
    }

    public function testLipsumWithPrependedLinkExpectBroken(): void
    {
        $text = 'https://www.criticalmass.in/hamburg/2011-06-24Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut et nulla mollis, feugiat metus at, commodo odio. Proin et finibus velit. Curabitur cursus fringilla urna sed malesuada. Fusce non porta dui. Duis at dolor venenatis, finibus urna sed, pellentesque augue. Nam finibus lorem id feugiat tincidunt. Nam dignissim ultrices nisl, et tincidunt mi accumsan in. Quisque rutrum leo a tortor consectetur pulvinar. Sed posuere erat fringilla, cursus nisi consectetur, porta tellus. Aliquam erat volutpat. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum aliquam ullamcorper elit, eget egestas tellus tempor eget. Donec porttitor semper orci, vel dictum sem pretium et.';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['https://www.criticalmass.in/hamburg/2011-06-24Lorem'], $linkList);
    }

    public function testLipsumWithSpaceAndLink(): void
    {
        $text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut et nulla mollis, feugiat metus at, commodo odio. Proin et finibus velit. Curabitur cursus fringilla urna sed malesuada. Fusce non porta dui. Duis at dolor venenatis, finibus urna sed, pellentesque augue. Nam finibus lorem id feugiat tincidunt. Nam dignissim ultrices nisl, et tincidunt mi accumsan in. https://www.criticalmass.in/hamburg/2011-06-24 Quisque rutrum leo a tortor consectetur pulvinar. Sed posuere erat fringilla, cursus nisi consectetur, porta tellus. Aliquam erat volutpat. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum aliquam ullamcorper elit, eget egestas tellus tempor eget. Donec porttitor semper orci, vel dictum sem pretium et.';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['https://www.criticalmass.in/hamburg/2011-06-24'], $linkList);
    }

    public function testLipsumWithLinkExpectBroken(): void
    {
        $text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut et nulla mollis, feugiat metus at, commodo odio. Proin et finibus velit. Curabitur cursus fringilla urna sed malesuada. Fusce non porta dui. Duis at dolor venenatis, finibus urna sed, pellentesque augue. Namhttps://www.criticalmass.in/hamburg/2011-06-24finibus lorem id feugiat tincidunt. Nam dignissim ultrices nisl, et tincidunt mi accumsan in. Quisque rutrum leo a tortor consectetur pulvinar. Sed posuere erat fringilla, cursus nisi consectetur, porta tellus. Aliquam erat volutpat. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum aliquam ullamcorper elit, eget egestas tellus tempor eget. Donec porttitor semper orci, vel dictum sem pretium et.';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['https://www.criticalmass.in/hamburg/2011-06-24finibus'], $linkList);
    }

    public function testLipsumWithAppendedSpaceAndLink(): void
    {
        $text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut et nulla mollis, feugiat metus at, commodo odio. Proin et finibus velit. Curabitur cursus fringilla urna sed malesuada. Fusce non porta dui. Duis at dolor venenatis, finibus urna sed, pellentesque augue. Nam finibus lorem id feugiat tincidunt. Nam dignissim ultrices nisl, et tincidunt mi accumsan in. Quisque rutrum leo a tortor consectetur pulvinar. Sed posuere erat fringilla, cursus nisi consectetur, porta tellus. Aliquam erat volutpat. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum aliquam ullamcorper elit, eget egestas tellus tempor eget. Donec porttitor semper orci, vel dictum sem pretium et. https://www.criticalmass.in/hamburg/2011-06-24';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['https://www.criticalmass.in/hamburg/2011-06-24'], $linkList);
    }

    public function testLipsumWithAppendedLinkExpectBroken(): void
    {
        $text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut et nulla mollis, feugiat metus at, commodo odio. Proin et finibus velit. Curabitur cursus fringilla urna sed malesuada. Fusce non porta dui. Duis at dolor venenatis, finibus urna sed, pellentesque augue. Nam finibus lorem id feugiat tincidunt. Nam dignissim ultrices nisl, et tincidunt mi accumsan in. Quisque rutrum leo a tortor consectetur pulvinar. Sed posuere erat fringilla, cursus nisi consectetur, porta tellus. Aliquam erat volutpat. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum aliquam ullamcorper elit, eget egestas tellus tempor eget. Donec porttitor semper orci, vel dictum sem pretium et.https://www.criticalmass.in/hamburg/2011-06-24';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(2, $linkList);
        $this->assertEquals(['et.https', 'www.criticalmass.in/hamburg/2011-06-24'], $linkList);
    }

    public function testUrlInHtmlParagraph(): void
    {
        $text = '<p>http://criticalmass.in/hamburg/2011-06-24</p>';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['http://criticalmass.in/hamburg/2011-06-24'], $linkList);
    }

    public function testUrlInHtmlAnchor(): void
    {
        $text = '<a href="http://criticalmass.in/hamburg/2011-06-24">Critical Mass</a>';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['http://criticalmass.in/hamburg/2011-06-24'], $linkList);
    }

    public function testSpiegelOnline(): void
    {
        $text = 'https://www.spiegel.de/wissenschaft/adfc-chef-burkhard-stork-radfahrern-geht-es-nirgends-wirklich-gut-a-00000000-0002-0001-0000-000164302368';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['https://www.spiegel.de/wissenschaft/adfc-chef-burkhard-stork-radfahrern-geht-es-nirgends-wirklich-gut-a-00000000-0002-0001-0000-000164302368'], $linkList);
    }

    public function testSpiegelOnlineHtml(): void
    {
        $text = '<a href="https://www.spiegel.de/wissenschaft/adfc-chef-burkhard-stork-radfahrern-geht-es-nirgends-wirklich-gut-a-00000000-0002-0001-0000-000164302368">SPIEGEL ONLINE</a>';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['https://www.spiegel.de/wissenschaft/adfc-chef-burkhard-stork-radfahrern-geht-es-nirgends-wirklich-gut-a-00000000-0002-0001-0000-000164302368'], $linkList);
    }

    public function testNdr(): void
    {
        $text = 'https://www.ndr.de/kultur/norddeutsche_sprache/plattdeutsch/-,norichten39258.html';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['https://www.ndr.de/kultur/norddeutsche_sprache/plattdeutsch/-,norichten39258.html'], $linkList);
    }

    public function testNdrHtml(): void
    {
        $text = '<a href="https://www.ndr.de/kultur/norddeutsche_sprache/plattdeutsch/-,norichten39258.html">NDR</a>';

        $linkList = (new LinkFinder())->findInText($text);

        $this->assertCount(1, $linkList);
        $this->assertEquals(['https://www.ndr.de/kultur/norddeutsche_sprache/plattdeutsch/-,norichten39258.html'], $linkList);
    }
}