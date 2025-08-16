<?php

namespace App\QueryBuilders;

use App\Models\Opportunity;
use App\QueryBuilders\Traits\SearchableQueryBuilderTrait;
use Illuminate\Database\Eloquent\Builder;

class OpportunityQueryBuilder extends BaseQueryBuilder
{
    use SearchableQueryBuilderTrait;

    private array $industryIds;
    private array $locationIds;
    private array $opportunityTypeIds;
    private array $amountRange;
    private array $statuses;

    public function withIndustryIds(array $ids): static
    {
        $this->industryIds = $ids;
        return $this;
    }

    public function withLocationIds(array $ids): static
    {
        $this->locationIds = $ids;
        return $this;
    }

    public function withOpportunityTypeIds(array $ids): static
    {
        $this->opportunityTypeIds = $ids;
        return $this;
    }

    public function withAmountRange(array $range): static
    {
        $this->amountRange = $range;
        return $this;
    }

    public function withStatuses(array $statuses): static
    {
        $this->statuses = $statuses;
        return $this;
    }

    public function all(): Builder
    {
        $builder = parent::all();

        if (isset($this->industryIds)) {
            $builder->whereIn('opportunities.industry_id', $this->industryIds);
        }

        if (isset($this->locationIds)) {
            $builder->whereIn('opportunities.location_id', $this->locationIds);
        }

        if (isset($this->opportunityTypeIds)) {
            $builder->whereIn('opportunities.opportunity_type_id', $this->opportunityTypeIds);
        }

        if (isset($this->amountRange)) {
            $builder->where('opportunities.lower_amount', '>=', $this->amountRange[0]);
            $builder->where('opportunities.upper_amount', '<=', $this->amountRange[1]);
        }

        if (isset($this->statuses)) {
            $builder->whereIn('opportunities.status', $this->statuses);
        }

        return $builder;
    }

    protected function builder(): Builder
    {
        $cols = [
            'opportunities.*',
            'industries.name as industry_name',
            'opportunity_types.name as opportunity_type_name',
        ];

        return Opportunity::withCreatorJoin($cols)
            ->join('industries', 'industries.id', 'opportunities.industry_id')
            ->join('opportunity_types', 'opportunity_types.id', 'opportunities.opportunity_type_id');
    }
}
