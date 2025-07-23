<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function getFilter(string $name): ?string
    {
        $params = request()->get('filter') ?? [];
        return $params[$name] ?? null;
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
}
