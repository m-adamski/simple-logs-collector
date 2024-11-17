<?php

namespace App\Model\Tabulator\Column;

abstract class AbstractColumn {
    public function __construct(
        private readonly array $options = [],
    ) {}

    public function getOptions(): array {
        return $this->options;
    }
}
