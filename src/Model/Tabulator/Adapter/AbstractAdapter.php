<?php

namespace App\Model\Tabulator\Adapter;

use App\Model\Tabulator\AdapterRequest;

abstract class AbstractAdapter {
    public function __construct(
        private array $options = [],
    ) {}

    public function getOptions(): array {
        return $this->options;
    }

    public function setOptions(array $options): AbstractAdapter {
        $this->options = $options;
        return $this;
    }

    public abstract function getData(AdapterRequest $adapterRequest): array;
}
