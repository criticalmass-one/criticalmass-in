<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser\Embedder;

use App\Criticalmass\Embed\LinkCache\LinkCacheInterface;
use App\Criticalmass\Embed\LinkFinder\LinkFinderInterface;
use Embed\Embed;
use Embed\EmbedCode;

class Embedder implements EmbedderInterface
{
    protected Embed $embed;
    protected LinkFinderInterface $linkFinder;
    protected LinkCacheInterface $linkCache;

    public function __construct(LinkFinderInterface $linkFinder, LinkCacheInterface $linkCache)
    {
        $this->embed = new Embed();
        $this->linkFinder = $linkFinder;
        $this->linkCache = $linkCache;
    }

    public function processEmbedsInText(string $text): string
    {
        $links = $this->linkFinder->findInText($text);

        foreach ($links as $link) {
            $embedCode = null;

            if ($this->linkCache->has($link)) {
                $embedCode = $this->linkCache->get($link);
            } else {
                $info = $this->embed->get(trim($link));

                $code = $info->code;

                if ($code instanceof EmbedCode) {
                    $embedCode = $info->code->html;
                    $this->linkCache->set($link, $embedCode);
                }
            }

            if ($embedCode) {
                $text = str_replace($link, $embedCode, $text);
            }
        }

        return $text;
    }
}