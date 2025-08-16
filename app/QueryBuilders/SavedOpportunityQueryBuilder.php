<?php

namespace App\QueryBuilders;

use App\Models\SavedOpportunity;
use App\QueryBuilders\Traits\SearchableQueryBuilderTrait;
use Illuminate\Database\Eloquent\Builder;

class SavedOpportunityQueryBuilder extends BaseQueryBuilder
{
    use SearchableQueryBuilderTrait;

    public function filterByUser(int $id): Builder
    {
        return $this
            ->all()
            ->where('saved_opportunities.user_id', $id);
    }

    public function filterByOpportunity(int $id): Builder
    {
        return $this
            ->all()
            ->where('saved_opportunities.opportunity_id', $id);
    }

    protected function builder(): Builder
    {
        $columns = [
            'saved_opportunities.*',
            'opportunities.name', 'opportunities.currency', 'opportunities.lower_amount',
            'opportunities.upper_amount', 'opportunities.logo', 'opportunities.application_url',
            'opportunities.overview', 'opportunities.closing_at', 'opportunities.status',
            'industries.name as industry_name',
            'opportunity_types.name as opportunity_type_name',
        ];

        return SavedOpportunity::withCreatorJoin($columns, pkColumn: 'user_id')
            ->join('opportunities', 'opportunities.id', 'saved_opportunities.opportunity_id')
            ->join('industries', 'industries.id', 'opportunities.industry_id')
            ->join('opportunity_types', 'opportunity_types.id', 'opportunities.opportunity_type_id');
    }
}
