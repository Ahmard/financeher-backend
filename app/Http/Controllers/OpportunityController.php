<?php

namespace App\Http\Controllers;

use App\Helpers\Http\Responder;
use App\Http\Requests\Customer\SavedOpportunityCreateRequest;
use App\QueryBuilders\SavedOpportunityQueryBuilder;
use App\Services\OpportunityService;
use App\Services\SavedOpportunityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OpportunityController extends Controller
{
    public function __construct(
        private readonly Responder          $responder,
        private readonly OpportunityService $service,
    )
    {
    }

    public function index(): JsonResponse
    {
        $queryBuilder = $this->service->repository()->queryBuilder();

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

    public function show(string $id): JsonResponse
    {
        return $this->responder->success(
            data: $this->service->repository()->findDetailed(
                id: $id,
                userId: Auth::id(),
            ),
            message: 'Opportunity fetched successfully'
        );
    }

    public function savedItems(SavedOpportunityQueryBuilder $queryBuilder): JsonResponse
    {
        return $this->responder->datatableFilterable(
            builder: $queryBuilder
                ->withSearch($this->getSearchQuery())
                ->all(),
            responseMessage: 'Saved opportunities fetched successfully'
        );
    }

    public function saveItem(SavedOpportunityCreateRequest $request, SavedOpportunityService $service): JsonResponse
    {
        $item = $service->create(
            userId: Auth::id(),
            oppId: $request->validated('id')
        );

        Log::debug($item->toArray());

        return $this->responder->created(
            data: $this->service->repository()->findDetailed(
                id: $item['opportunity_id'],
                userId: Auth::id(),
            ),
            message: 'Opportunity saved successfully'
        );
    }

    public function removeItem(string $oid, SavedOpportunityService $service): JsonResponse
    {
        $service->remove(
            ownerId: Auth::id(),
            oppId: $oid,
        );

        return $this->responder->successMessage('Saved opportunity removed successfully');
    }
}
