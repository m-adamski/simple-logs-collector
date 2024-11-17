<?php

namespace App\Model\Tabulator;

use Symfony\Component\HttpFoundation\InputBag;

class AdapterRequest {
    private bool $pagination = false;
    private ?int $paginationPage = null;
    private ?int $paginationSize = null;
    private ?InputBag $payload = null;

    public function isPagination(): bool {
        return $this->pagination;
    }

    public function setPagination(bool $pagination): AdapterRequest {
        $this->pagination = $pagination;
        return $this;
    }

    public function getPaginationPage(): ?int {
        return $this->paginationPage;
    }

    public function setPaginationPage(?int $paginationPage): AdapterRequest {
        $this->paginationPage = $paginationPage;
        return $this;
    }

    public function getPaginationSize(): ?int {
        return $this->paginationSize;
    }

    public function setPaginationSize(?int $paginationSize): AdapterRequest {
        $this->paginationSize = $paginationSize;
        return $this;
    }

    public function getPayload(): ?InputBag {
        return $this->payload;
    }

    public function setPayload(?InputBag $payload): AdapterRequest {
        $this->payload = $payload;
        return $this;
    }
}
