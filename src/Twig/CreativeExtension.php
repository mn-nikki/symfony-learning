<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CreativeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('crt_hello', [$this, 'doHello']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('crt_hello', [$this, 'doHello']),
        ];
    }

    public function doHello(string $value): string
    {
        return \sprintf('Hello, %s!', $value);
    }
}
