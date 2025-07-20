<?php

namespace App\Helpers\Http;

use JetBrains\PhpStorm\ArrayShape;

class TableFilter
{
    protected array $filters = [
        'additional_statuses' => [],
        'status' => null,
        'stage' => null,
        'start_date' => null,
        'end_date' => null,
    ];

    public static function useDateBasic(string $status, string $date): TableFilter
    {
        return self::useBasic(
            status: $status,
            startDate: $date,
            endDate: $date
        );
    }

    public static function useBasic(string $status, string $startDate, string $endDate): TableFilter
    {
        return self::create()
            ->useStatus($status)
            ->useStartDate($startDate)
            ->useEndDate($endDate);
    }

    public function useEndDate(string $column): static
    {
        $this->filters['end_date'] = $column;
        return $this;
    }

    public function useStartDate(string $column): static
    {
        $this->filters['start_date'] = $column;
        return $this;
    }

    public function useStatus(string $column): static
    {
        $this->filters['status'] = $column;
        return $this;
    }

    public static function create(): TableFilter
    {
        return new TableFilter();
    }

    public function useAdditionalStatuses(array $statuses): static
    {
        $this->filters['additional_statuses'] = array_merge(
            $this->filters['additional_statuses'],
            $statuses
        );

        return $this;
    }

    #[ArrayShape([
        'status' => 'string',
        'start_date' => 'string',
        'end_date' => 'string',
        'additional_statuses' => 'string[]',
    ])]
    public function getFilters(): array
    {
        return $this->filters;
    }
}
