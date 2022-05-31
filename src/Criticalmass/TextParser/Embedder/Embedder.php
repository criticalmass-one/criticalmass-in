<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser\Embedder;

use App\Criticalmass\TextParser\LinkCache\LinkCacheInterface;
use Embed\Embed;
use Embed\EmbedCode;
use League\CommonMark\Inline\Element\HtmlInline;
use League\CommonMark\Inline\Element\Link;

class Embedder implements EmbedderInterface
{
    protected Embed $embed;
    protected LinkCacheInterface $linkCache;

    public function __construct(LinkCacheInterface $linkCache)
    {
        $this->embed = new Embed();
        $this->linkCache = $linkCache;
    }

    public function processEmbedInLink(Link $link): ?HtmlInline
    {
        $embedCode = null;
        $url = $link->getUrl();

        if ($this->linkCache->has($url)) {
            $embedCode = $this->linkCache->get($url);
        } else {
            $info = $this->embed->get($url);

            $code = $info->code;

            if ($code instanceof EmbedCode) {
                $embedCode = $info->code->html;
                $this->linkCache->set($url, $embedCode);
            }
        }

        if ($embedCode) {
            return new HtmlInline($embedCode);
        }

        return null;
    }
}