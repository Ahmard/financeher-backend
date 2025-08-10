<?php

namespace App\QueryBuilders;

use App\Models\Opportunity;
use Illuminate\Database\Eloquent\Builder;

class OpportunityQueryBuilder extends BaseQueryBuilder
{

    protected function builder(): Builder
    {
        $cols = [
            'opportunities.*',
            'business_types.name as business_type_name',
            'opportunity_types.name as opportunity_type_name',
        ];

        return Opportunity::withCreatorJoin($cols)
            ->join('business_types', 'business_types.id', 'opportunities.business_type_id')
            ->join('opportunity_types', 'opportunity_types.id', 'opportunities.opportunity_type_id');
    }
}