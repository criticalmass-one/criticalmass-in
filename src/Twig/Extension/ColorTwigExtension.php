<?php declare(strict_types=1);

namespace App\Twig\Extension;

class ColorTwigExtension extends \Twig_Extension
{
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('rgb_to_hex', [$this, 'rgbToHex'], ['is_safe' => ['html']]),
        ];
    }

    public function rgbToHex(int $red, int $green, int $blue): string
    {
        return sprintf('#%02x%02x%02x', $red, $green, $blue);
    }

    public function getName(): string
    {
        return 'color_extension';
    }
}

