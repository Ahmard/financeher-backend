<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\WarningException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerListShowDeleteTrait;
use App\Http\Requests\Admin\LoanVcCreateRequest;
use App\Http\Requests\Admin\LoanVcUpdateRequest;
use App\Http\Requests\Admin\OpportunityCreateRequest;
use App\Http\Requests\Admin\OpportunityUpdateRequest;
use App\Http\Requests\ImageUploadRequest;
use App\Services\LoanVcService;
use App\Services\OpportunityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class LoanVcController extends Controller
{
    use ControllerListShowDeleteTrait;

    public function __construct(
        private readonly LoanVcService $service,
    )
    {
    }

    public function pageMetrics(): JsonResponse
    {
        return $this->responder()->success(
            data: $this->service->pageMetrics(),
            message: 'Loan/VC metrics retrieved successfully'
        );
    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): JsonResponse
    {
        return $this->responder()->datatableFilterable(
            builder: $this->service->queryBuilder()->allDesc(),
            responseMessage: 'Items retrieved successfully'
        );
    }

    public function store(LoanVcCreateRequest $request): JsonResponse
    {
        $opp = $this->service->create(
            createdBy: Auth::id(),
            countryIds: $request->validated('country_ids'),
            industryId: $request->validated('industry_id'),
            opportunityTypeId: $request->validated('opportunity_type_id'),
            organisation: $request->validated('organisation'),
            lowerAmount: $request->validated('min_amount'),
            upperAmount: $request->validated('max_amount'),
            description: $request->validated('description'),
            applicationUrl: $request->validated('application_url'),
            closingAt: $request->validated('closing_at'),
        );

        return $this->responder()->success(
            data: $opp,
            message: 'Loan/VC created successfully'
        );
    }

    public function update(string $id, LoanVcUpdateRequest $request): JsonResponse
    {
        $opp = $this->service->update(
            id: $id,
            updatedBy: Auth::id(),
            businessTypeId: $request->validated('business_type_id'),
            opportunityTypeId: $request->validated('opportunity_type_id'),
            organisation: $request->validated('organisation'),
            lowerAmount: $request->validated('lower_amount'),
            upperAmount: $request->validated('upper_amount'),
            description: $request->validated('description'),
            applicationUrl: $request->validated('application_url'),
            closingAt: $request->validated('closing_at'),
        );

        return $this->responder()->success(
            data: $opp,
            message: 'Loan/VC created successfully'
        );
    }

    /**
     * @throws WarningException
     */
    public function changeLogo(string $id, ImageUploadRequest $request): JsonResponse
    {
        $this->service->changeLogo($id, Auth::id());
        return $this->responder()->successMessage(
            message: 'Loan/VC logo changed successfully'
        );
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function close(string $id): JsonResponse
    {
        $this->service->close($id, Auth::id());
        return $this->responder()->successMessage(
            message: 'Loan/VC closed successfully'
        );
    }
}
