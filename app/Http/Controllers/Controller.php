<?php

namespace App\Http\Controllers;

use App\Helpers\Http\Responder;
use Illuminate\Contracts\Container\BindingResolutionException;

abstract class Controller
{
    private array $filters;

    public function getFilter(string $name): mixed
    {
        $this->setFilters();
        return $this->filters[$name] ?? null;
    }

    public function hasFilter(string $name): bool
    {
        $this->setFilters();
        return isset($this->filters[$name]);
    }

    private function setFilters()
    {
        if (!isset($this->filters)) {
            $this->filters = request()->get('filter') ?? [];
        }
    }

    public function getSearchQuery(): ?string
    {
        $search = $this->getFilter('search');
        if (empty($search) || $search == 'undefined') {
            return null;
        }

        return $search;
    }

    public function getPageNumber(): int
    {
        return request()->get('page') ?? 1;
    }

    public function getPagingStart(): int
    {
        return request()->get('start') ?? 0;
    }

    public function getPagingLength(): int
    {
        return (request()->get('length') ?? request()->get('limit')) ?? 10;
    }

    public function startDate(): ?string
    {
        return request()->get('start_date') ?? null;
    }

    public function endDate(): ?string
    {
        return request()->get('end_date') ?? null;
    }

    public function responder(): Responder
    {
        return app()->make(Responder::class);
    }
}
