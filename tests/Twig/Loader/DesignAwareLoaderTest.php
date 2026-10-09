<?php declare(strict_types=1);

namespace Tests\Twig\Loader;

use App\Design\DesignChoice;
use App\Twig\Loader\DesignAwareLoader;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;

final class DesignAwareLoaderTest extends TestCase
{
    private const array TEMPLATES = [
        'Template/StandardTemplate.html.twig' => 'alt-rahmen[{% block content %}{% endblock %}]',
        'v2/Template/StandardTemplate.html.twig' => 'darf-nie-greifen',
        'v2/Layout/standard.html.twig' => 'neu-rahmen[{% block content %}{% endblock %}]',
        'Ride/show.html.twig' => "{% extends 'Template/StandardTemplate.html.twig' %}{% block content %}alte-tour{% endblock %}",
        'v2/Ride/show.html.twig' => "{% extends 'v2/Layout/standard.html.twig' %}{% block content %}neue-tour{% endblock %}",
        'City/show.html.twig' => "{% extends 'Template/StandardTemplate.html.twig' %}{% block content %}{% include 'City/_box.html.twig' %}{% endblock %}",
        'City/_box.html.twig' => 'alte-box',
        'v2/City/_box.html.twig' => 'neue-box',
        'email/login_link.html.twig' => 'alte-mail',
        'v2/email/login_link.html.twig' => 'darf-nie-greifen',
    ];

    private function environment(bool $v2): Environment
    {
        $designChoice = $this->createStub(DesignChoice::class);
        $designChoice->method('isV2')->willReturn($v2);

        $inner = new ArrayLoader(self::TEMPLATES);

        return new Environment(new ChainLoader([new DesignAwareLoader($inner, $designChoice), $inner]));
    }

    public function testOldDesignIgnoresV2Templates(): void
    {
        self::assertSame('alt-rahmen[alte-tour]', $this->environment(false)->render('Ride/show.html.twig'));
    }

    public function testNewDesignTakesV2PageWithItsOwnLayout(): void
    {
        self::assertSame('neu-rahmen[neue-tour]', $this->environment(true)->render('Ride/show.html.twig'));
    }

    public function testPageWithoutV2VersionStaysCompletelyOld(): void
    {
        self::assertSame('alt-rahmen[neue-box]', $this->environment(true)->render('City/show.html.twig'));
    }

    public function testMailsNeverSwitch(): void
    {
        self::assertSame('alte-mail', $this->environment(true)->render('email/login_link.html.twig'));
    }

    public function testCacheKeysDiffer(): void
    {
        $designChoice = $this->createStub(DesignChoice::class);
        $designChoice->method('isV2')->willReturn(true);
        $inner = new ArrayLoader(self::TEMPLATES);

        $loader = new DesignAwareLoader($inner, $designChoice);

        self::assertTrue($loader->exists('Ride/show.html.twig'));
        self::assertNotSame($inner->getCacheKey('Ride/show.html.twig'), $loader->getCacheKey('Ride/show.html.twig'));
    }

    public function testNamesThatAreNeverOverridden(): void
    {
        self::assertFalse(DesignAwareLoader::isOverridable('@WebProfiler/Profiler/layout.html.twig'));
        self::assertFalse(DesignAwareLoader::isOverridable('v2/Layout/base.html.twig'));
        self::assertFalse(DesignAwareLoader::isOverridable('Template/MasterTemplate.html.twig'));
        self::assertFalse(DesignAwareLoader::isOverridable('email/forum_post.html.twig'));
        self::assertTrue(DesignAwareLoader::isOverridable('Frontpage/index.html.twig'));
        self::assertTrue(DesignAwareLoader::isOverridable('Template/Includes/_footer.html.twig'));
    }
}
