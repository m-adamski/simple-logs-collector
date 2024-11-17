<?php

namespace App\Model\Tabulator;

use Symfony\Component\HttpFoundation\RequestStack;

class TabulatorFactory {
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {}

    public function create(string $selector, array $options = []): Tabulator {
        return new Tabulator($selector, $options, $this->requestStack->getCurrentRequest());
    }
}
