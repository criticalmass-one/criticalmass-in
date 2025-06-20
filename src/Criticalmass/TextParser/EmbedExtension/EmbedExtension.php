<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser\EmbedExtension;

use App\Criticalmass\TextParser\Embedder\EmbedderInterface;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ExtensionInterface;

final class EmbedExtension implements ExtensionInterface
{
    public function __construct(private EmbedderInterface $embedder)
    {

    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addEventListener(DocumentParsedEvent::class, new EmbedProcessor($this->embedder), -1000);
    }
}
