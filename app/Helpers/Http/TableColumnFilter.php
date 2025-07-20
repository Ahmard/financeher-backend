<?php

namespace App\Helpers\Http;

use App\Models\User;

class TableColumnFilter
{
    protected array $filters = [];

    public static function new(): TableColumnFilter
    {
        return new TableColumnFilter();
    }

    public function withCreatorFullName(string $table = 'creator'): static
    {
        return $this->withFullName(table: $table, column: 'creator_full_name');
    }

    public function withFullName(string $table = 'users', string $column = 'full_name', ?string $prefix = null): static
    {
        return $this->add(
            column: $column,
            query: User::getDatatableFilterFullNameColumn(table: $table, prefix: $prefix),
            binding: fn (string $keyword) => [["%$keyword%"]],
        );
    }

    public function add(string $column, string $query, callable $binding): static
    {
        $this->filters[$column] = ['sql' => $query, 'binding' => $binding];
        return $this;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }
}
