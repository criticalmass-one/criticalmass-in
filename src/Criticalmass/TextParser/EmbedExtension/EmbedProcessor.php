<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser\EmbedExtension;

use App\Criticalmass\TextParser\Embedder\EmbedderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Inline\Element\Link;

final class EmbedProcessor
{
    protected EmbedderInterface $embedder;

    public function __construct(EmbedderInterface $embedder)
    {
        $this->embedder = $embedder;
    }

    public function __invoke(DocumentParsedEvent $e)
    {
        $walker = $e->getDocument()->walker();

        while ($event = $walker->next()) {
            $node = $event->getNode();

            if ($node instanceof Link) {
                $htmlNode = $this->embedder->processEmbedInLink($node);

                if ($htmlNode) {
                    $node->replaceWith($htmlNode);
                }
            }
        }
    }
}
