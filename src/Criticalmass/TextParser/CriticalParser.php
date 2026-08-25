<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser;

use App\Criticalmass\TextParser\TextCache\TextCacheInterface;
use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\MarkdownConverter;

class CriticalParser implements TextParserInterface
{
    private readonly ConverterInterface $converter;

    public function __construct(
        private readonly TextCacheInterface $textCache
    ) {
        $this->configure();
    }

    protected function configure(): void
    {
        $config = [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ];

        $environment = new Environment($config);

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new AutolinkExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    public function parse(string $text): string
    {
        if ($this->textCache->has($text)) {
            $cached = $this->textCache->get($text);
            return is_string($cached) ? $cached : $cached->getContent();
        }

        $parsed = $this->converter->convert($text);
        $content = $parsed->getContent();

        $this->textCache->set($text, $content);

        return $content;
    }
}
