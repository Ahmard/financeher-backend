<?php

namespace App\QueryBuilders;

use App\Models\LoanVc;
use App\Models\Opportunity;
use Illuminate\Database\Eloquent\Builder;

class LoanVcQueryBuilder extends BaseQueryBuilder
{

    protected function builder(): Builder
    {
        $cols = [
            'loan_vcs.*',
            'industries.name as industry_name',
            'opportunity_types.name as opportunity_type_name',
        ];

        return LoanVc::withCreatorJoin($cols)
            ->join('industries', 'industries.id', 'loan_vcs.industry_id')
            ->join('opportunity_types', 'opportunity_types.id', 'loan_vcs.opportunity_type_id');
    }
}
