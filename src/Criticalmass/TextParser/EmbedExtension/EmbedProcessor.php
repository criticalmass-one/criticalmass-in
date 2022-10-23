<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser\EmbedExtension;

use App\Criticalmass\TextParser\Embedder\EmbedderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Element\Text;

final class EmbedProcessor
{
    public function __construct(protected EmbedderInterface $embedder)
    {
    }

    public function __invoke(DocumentParsedEvent $e)
    {
        $walker = $e->getDocument()->walker();

        while ($event = $walker->next()) {
            $node = $event->getNode();

            // only use embedding for "raw" links, do not embed links if they are already placed in a link with caption
            if ($node instanceof Text && $node->parent() instanceof Link && $node->parent()->getUrl() === $node->getContent()) {
                $htmlNode = $this->embedder->processEmbedInLink($node->parent());

                if ($htmlNode) {
                    $node->parent()->replaceWith($htmlNode);
                }
            }
        }
    }
}
