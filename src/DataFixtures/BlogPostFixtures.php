<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Blog;
use App\Entity\BlogPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class BlogPostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->createBlogPost(
            $this->getReference('blog-enabled-test-blog'),
            $this->getReference('user-maltehuebner'),
            'testeintrag',
            'Testeintrag',
            'Testintro',
            'Testtext',
            new \DateTime('2011-06-24'),
            true
        ));

        $manager->persist($this->createBlogPost(
            $this->getReference('blog-enabled-test-blog'),
            $this->getReference('user-maltehuebner'),
            'unsichtbarer-testeintrag',
            'Unsichtbarer Testeintrag',
            'Unsichtbares Testintro',
            'Unsichtbarer Testtext',
            new \DateTime('2011-06-25'),
            false
        ));

        $manager->persist($this->createBlogPost(
            $this->getReference('blog-enabled-test-blog'),
            $this->getReference('user-maltehuebner'),
            'testeintrag-ohne-intro',
            'Testeintrag ohne Intro',
            null,
            'Testtext ohne Intro',
            new \DateTime('2011-06-26'),
            true
        ));

        $manager->flush();
    }

    protected function createBlogPost(Blog $blog, User $user, string $slug, string $title, string $intro = null, string $text, \DateTime $createdAt, bool $enabled = true): BlogPost
    {
        $blogPost = new BlogPost();
        $blogPost
            ->setBlog($blog)
            ->setUser($user)
            ->setSlug($slug)
            ->setTitle($title)
            ->setIntro($intro)
            ->setText($text)
            ->setCreatedAt($createdAt)
            ->setEnabled($enabled);

        return $blogPost;
    }

    public function getDependencies(): array
    {
        return [
            BlogFixtures::class,
            UserFixtures::class,
        ];
    }
}
