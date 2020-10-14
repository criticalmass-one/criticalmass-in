<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Blog;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class BlogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->createBlog('Disabled Test Blog', 'disabled-test-blog', 'Dieses Blog wurde abgeschaltet.', 'Abgeschaltetes Blog aber tolle Inhalte', 11, false));
        $manager->persist($this->createBlog('Enabled Test Blog', 'enabled-test-blog', 'Dieses Blog dreht sich um die Critical Mass.', 'Tolle Inhalte'));
        $manager->flush();
    }

    protected function createBlog(string $title, string $slug, string $description, string $intro, int $postNumber = 42, bool $enabled = true): Blog
    {
        $blog = new Blog();
        $blog
            ->setTitle($title)
            ->setSlug($slug)
            ->setDescription($description)
            ->setIntro($intro)
            ->setPostNumber($postNumber)
            ->setEnabled($enabled);

        $this->setReference(sprintf('blog-%s', $slug), $blog);

        return $blog;
    }
}
