<?php declare(strict_types=1);

namespace App\Twig\Extension;

use Sonata\SeoBundle\Seo\SeoPageInterface;
use Sonata\SeoBundle\Twig\Extension\SeoExtension;

class SeoTwigExtension
{
    public function __construct(private readonly SeoPageInterface $page)
    {

    }

    public function getMetadatas(): string
    {
        $html = '';
        foreach ($this->page->getMetas() as $type => $metas) {
            foreach ((array) $metas as $name => $meta) {
                list($content, $extras) = $meta;

                if (!empty($content)) {
                    $html .= sprintf("<meta %s=\"%s\" content=\"%s\" />\n",
                        $type,
                        $this->normalize($name),
                        array_key_exists('escape', $extras) && $extras['escape'] === false ? $content : $this->normalize($content)
                    );
                } else {
                    $html .= sprintf("<meta %s=\"%s\" />\n",
                        $type,
                        $this->normalize($name)
                    );
                }
            }
        }

        return $html;
    }

    private function normalize($string): string
    {
        return htmlentities(strip_tags($string), ENT_COMPAT, $this->encoding);
    }
}
