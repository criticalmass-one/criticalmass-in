<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\CrawledWebsite;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \simplehtmldom_1_5\simple_html_dom as HtmlDomElement;

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
            ->setDescription('Crawl websites from posts')
            ->addArgument('limit', InputArgument::OPTIONAL, 'Number of posts to crawl per command call');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
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
            preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $post->getMessage(), $resultList, PREG_PATTERN_ORDER);

            foreach ($resultList as $result) {
                $url = array_pop($result);

                if ($url) {
                    $html = \Sunra\PhpSimple\HtmlDomParser::str_get_html(file_get_contents($url));

                    $cw = $this->parse($html, $url);

                    $table->addRow([
                        $post->getId(),
                        $url,
                        $cw->getTitle(),
                        $cw->getDescription(),
                        $cw->getImageUrl(),
                    ]);

                    $this->registry->getManager()->persist($cw);
                }

                $post->setCrawled(true);
            }

            $progressBar->advance();
        }

        $this->registry->getManager()->flush();

        $progressBar->finish();
        $table->render();
    }

    protected function parse(HtmlDomElement $element, string $url): ?CrawledWebsite
    {
        $cw = new CrawledWebsite();

        $cw->setUrl($url);

        $this->findFirstMatchingElement($element, $cw, 'title', 'title', 'innertext');
        $this->findFirstMatchingElement($element, $cw, 'description', 'meta[name*="description"]', 'content');
        $this->findFirstMatchingElement($element, $cw, 'imageUrl', 'meta[name="twitter:image"],meta[property="og:image"]', 'content');

        return $cw;
    }

    protected function findFirstMatchingElement(HtmlDomElement $element, CrawledWebsite $crawledWebsite, string $propertyName, string $selector, string $accessMethod): CrawledWebsite
    {
        $list = $element->find($selector);

        $item = array_pop($list);

        if ($item) {
            $setMethodName = sprintf('set%s', ucfirst($propertyName));

            $crawledWebsite->$setMethodName($item->$accessMethod);
        }

        return $crawledWebsite;
    }
}
