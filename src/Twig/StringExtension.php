<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class StringExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('ellipsify', [$this, 'filter']),
        ];
    }

    public function getFunctions(): array
    {
        return [
        ];
    }

    public function filter(string $value, int $length)
    {
        return substr($value, 0, $length).'...';
    }
}
