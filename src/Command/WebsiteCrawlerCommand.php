<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Post;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebsiteCrawlerCommand extends Command
{
    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:website:crawl')
            ->setDescription('Crawl websites from posts');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $postList = $this->registry->getRepository(Post::class)->findAll();

        rsort($postList);
        $progressBar = new ProgressBar($output, count($postList));

        /** @var Post $post */
        foreach ($postList as $post) {
            preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $post->getMessage(), $resultList, PREG_PATTERN_ORDER);

            foreach ($resultList as $result) {
                $url = array_pop($result);

                if ($url) {
                    echo $url;

                    $html = \Sunra\PhpSimple\HtmlDomParser::str_get_html(file_get_contents($url));

                    $titles = $html->find('html > head > title');

                $title = array_pop($titles);

                echo $title->innertext();

                }

            }

            $progressBar->advance();
        }

        $progressBar->finish();
    }
}
