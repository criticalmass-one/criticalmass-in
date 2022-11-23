<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser\EmbedExtension;

use App\Criticalmass\TextParser\Embedder\EmbedderInterface;
use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ExtensionInterface;

final class EmbedExtension implements ExtensionInterface
{
    protected EmbedderInterface $embedder;

    public function __construct(EmbedderInterface $embedder)
    {
        $this->embedder = $embedder;
    }

    public function register(ConfigurableEnvironmentInterface $environment)
    {
        $environment->addEventListener(DocumentParsedEvent::class, new EmbedProcessor($this->embedder), -1000);
    }
}
