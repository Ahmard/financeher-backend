<?php

namespace App\QueryBuilders\Traits;

trait SearchableQueryBuilderTrait
{
    protected ?string $searchQuery;


    public function withSearch(string|int|float|null $q): static
    {
        if (!empty($q)) {
            $this->searchQuery = $q;
        }

        return $this;
    }

    public function getSearchQuery(): ?string
    {
        return $this->searchQuery;
    }
}
