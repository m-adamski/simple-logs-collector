<?php

namespace App\Model\Tabulator;

use App\Model\Tabulator\Adapter\AbstractAdapter;
use App\Model\Tabulator\Column\AbstractColumn;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Tabulator {
    private ?AbstractAdapter $adapter = null;
    private array $columns = [];
    private array $defaultOptions = [
        "ajaxConfig"             => Request::METHOD_POST,
        "ajaxContentType"        => "json",
        "ajaxParams"             => ["generator" => "tabulator"],
        "layout"                 => "fitColumns",
        "pagination"             => true,
        "paginationMode"         => "remote",
        "paginationSize"         => 25,
        "paginationButtonCount"  => 3,
        "paginationSizeSelector" => [10, 25, 50],
        "paginationCounter"      => "rows"
    ];

    public function __construct(
        private string   $selector,
        private array    $options = [],
        private ?Request $request = null
    ) {}

    public function getSelector(): string {
        return $this->selector;
    }

    public function setSelector(string $selector): Tabulator {
        $this->selector = $selector;
        return $this;
    }

    public function getOptions(bool $mergeDefaults = false): array {
        return $mergeDefaults ? array_merge($this->getDefaultOptions(), $this->options) : $this->options;
    }

    public function setOptions(array $options): Tabulator {
        $this->options = $options;
        return $this;
    }

    public function getDefaultOptions(): array {
        return array_merge([
            "ajaxURL" => $this->request?->getRequestUri(),
        ], $this->defaultOptions);
    }

    public function getColumns(): array {
        return $this->columns;
    }

    public function addColumn(string $name, string $columnClass, array $options = []): self {
        if (array_key_exists($name, $this->columns)) {
            throw new \InvalidArgumentException("Table already contains a column with name '$name'.");
        }

        $this->columns[$name] = new $columnClass($options);

        return $this;
    }

    public function getAdapter(): ?AbstractAdapter {
        return $this->adapter;
    }

    public function setAdapter(?AbstractAdapter $adapter): Tabulator {
        $this->adapter = $adapter;
        return $this;
    }

    public function getRequest(): ?Request {
        return $this->request;
    }

    public function setRequest(?Request $request): Tabulator {
        $this->request = $request;
        return $this;
    }

    public function getConfig(): array {
        $tableOptions = $this->getOptions(true);

        if (!array_key_exists("ajaxURL", $tableOptions) || null === $tableOptions["ajaxURL"]) {
            throw new \InvalidArgumentException("The ajaxURL option must be set.");
        }

        return [
            "selector" => $this->getSelector(),
            "options"  => array_merge($tableOptions, [
                "columns" => array_map(function (AbstractColumn $column) {
                    return $column->getOptions();
                }, array_values($this->getColumns()))
            ]),
        ];
    }

    public function handleRequest(Request $request): ?JsonResponse {
        if (
            "tabulator" === $request->query->get("generator") ||
            "tabulator" === $request->headers->get("X-Request-Generator")
        ) {
            if (null === $this->getAdapter()) {
                throw new \InvalidArgumentException("Missing Adapter to handle request.");
            }

            return new JsonResponse(
                $this->getAdapter()->getData(
                    (new AdapterRequest())
                        ->setPagination($this->getOptions(true)["pagination"])
                        ->setPaginationSize($request->query->get("size") ?? $request->getPayload()->get("size"))
                        ->setPaginationPage($request->query->get("page") ?? $request->getPayload()->get("page"))
                        ->setPayload($request->getPayload())
                )
            );
        }

        return null;
    }
}
