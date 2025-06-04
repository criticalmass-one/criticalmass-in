<?php declare(strict_types=1);

namespace App\Command;

use App\Criticalmass\Website\Crawler\CrawlerInterface;
use App\Criticalmass\Website\Parser\ParserInterface;
use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'criticalmass:website:crawl',
    description: 'Crawl websites from posts',
)]
class WebsiteCrawlerCommand extends Command
{
    public function __construct(protected ManagerRegistry $registry, protected CrawlerInterface $crawler, protected ParserInterface $parser)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('limit', InputArgument::OPTIONAL, 'Number of posts to crawl per command call');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$limit = (int) $input->getArgument('limit')) {
            $limit = null;
        }

        $postList = $this->registry->getRepository(Post::class)->findByCrawled(false, $limit);

        rsort($postList);
        $progressBar = new ProgressBar($output, count($postList));

        $table = new Table($output);

        $table->setHeaders([
            'Post Id',
            'url',
            'title',
            'description',
            'imageUrl',
        ]);

        /** @var Post $post */
        foreach ($postList as $post) {
            $urlList = $this->crawler->crawlUrls($post);

            foreach ($urlList as $url) {
                $crawledWebsite = $this->parser->parse($url);

                if (!$crawledWebsite) {
                    continue 2;
                }

                $this->registry->getManager()->persist($crawledWebsite);

                $table->addRow([
                    $post->getId(),
                    $url,
                    $crawledWebsite->getTitle(),
                    $crawledWebsite->getDescription(),
                    $crawledWebsite->getImageUrl(),
                ]);
            }

            $post->setCrawled(true);

            $progressBar->advance();
        }

        $this->registry->getManager()->flush();

        $progressBar->finish();
        $table->render();

        return Command::SUCCESS;
    }
}
