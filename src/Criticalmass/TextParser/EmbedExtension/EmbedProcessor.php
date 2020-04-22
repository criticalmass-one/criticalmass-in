<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser\EmbedExtension;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Inline\Element\Link;

final class EmbedProcessor
{
    public function __invoke(DocumentParsedEvent $e)
    {
        $walker = $e->getDocument()->walker();

        while ($event = $walker->next()) {
            $node = $event->getNode();

            if ($node->parent() instanceof Link) {
                dump($node);
            }
        }
    }
}
