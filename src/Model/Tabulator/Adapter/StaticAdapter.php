<?php

namespace App\Model\Tabulator\Adapter;

use App\Model\Tabulator\AdapterRequest;

class StaticAdapter extends AbstractAdapter {
    public function __construct(private array $data = [], array $options = []) {
        parent::__construct($options);
    }

    public function getData(AdapterRequest $adapterRequest): array {
        if ($adapterRequest->isPagination()) {

            if (1 === $adapterRequest->getPaginationSize()) {
                return [
                    "last_page" => 1,
                    "last_row"  => count($this->data),
                    "data"      => $this->data
                ];
            }

            return [
                "last_page" => ceil(count($this->data) / $adapterRequest->getPaginationSize()),
                "last_row"  => count($this->data),
                "data"      => array_slice($this->data, ($adapterRequest->getPaginationPage() * $adapterRequest->getPaginationSize()) - $adapterRequest->getPaginationSize(), $adapterRequest->getPaginationSize()),
            ];
        }

        return $this->data;
    }

    public function setData(array $data): StaticAdapter {
        $this->data = $data;
        return $this;
    }
}
