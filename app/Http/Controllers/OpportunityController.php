<?php

namespace App\Http\Controllers;

use App\Helpers\Http\Responder;
use App\QueryBuilders\OpportunityQueryBuilder;
use App\Repositories\OpportunityRepository;
use Illuminate\Http\JsonResponse;

class OpportunityController extends Controller
{
    public function __construct(
        private readonly Responder $responder,
    )
    {
    }

    public function index(OpportunityQueryBuilder $queryBuilder): JsonResponse
    {
        if ($this->hasFilter('location_ids')) {
            $queryBuilder->withLocationIds($this->getFilter('location_ids'));
        }

        if ($this->hasFilter('industry_ids')) {
            $queryBuilder->withIndustryIds($this->getFilter('industry_ids'));
        }

        if ($this->hasFilter('opportunity_type_ids')) {
            $queryBuilder->withOpportunityTypeIds($this->getFilter('opportunity_type_ids'));
        }

        if ($this->hasFilter('amount_range')) {
            $queryBuilder->withAmountRange($this->getFilter('amount_range'));
        }

        if ($this->hasFilter('statuses')) {
            $queryBuilder->withStatuses($this->getFilter('statuses'));
        }

        return $this->responder->datatableFilterable(
            builder: $queryBuilder
                ->withSearch($this->getSearchQuery())
                ->all(),
            responseMessage: 'Opportunities fetched successfully'
        );
    }

    public function show(string $id, OpportunityRepository $repository)
    {
        return $this->responder->success(
            data: $repository->findRequiredById($id),
            message: 'Opportunity fetched successfully'
        );
    }
}
