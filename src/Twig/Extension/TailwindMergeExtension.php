<?php

namespace App\Twig\Extension;

use TailwindMerge\TailwindMerge;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TailwindMergeExtension extends AbstractExtension {
    public function getFilters(): array {
        return [
            new TwigFilter("tailwind_merge", $this->merge(...)),
        ];
    }

    public function merge($value): string {
        return (TailwindMerge::instance())
            ->merge($value);
    }
}
