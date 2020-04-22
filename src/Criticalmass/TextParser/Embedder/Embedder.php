<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser\Embedder;

use App\Criticalmass\TextParser\LinkCache\LinkCacheInterface;
use Embed\Embed;
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

    public function processEmbedsInLink(Link $link): Link
    {
        return $link;
        /*
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

                return $text;*/
    }
}