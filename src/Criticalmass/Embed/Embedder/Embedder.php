<?php declare(strict_types=1);

namespace App\Criticalmass\Embed\Embedder;

use App\Criticalmass\Embed\LinkFinder\LinkFinderInterface;
use Embed\Embed;
use Embed\EmbedCode;

class Embedder implements EmbedderInterface
{
    protected Embed $embed;
    protected LinkFinderInterface $linkFinder;

    public function __construct(LinkFinderInterface $linkFinder)
    {
        $this->embed = new Embed();
        $this->linkFinder = $linkFinder;
    }

    public function processEmbedsInText(string $text): string
    {
        $links = $this->linkFinder->findInText($text);

        foreach ($links as $link) {
            $info = $this->embed->get(trim($link));

            $code = $info->code;

            if ($code instanceof EmbedCode) {
                $text = str_replace($link, $info->code->html, $text);
            }
        }

        return $text;
    }
}