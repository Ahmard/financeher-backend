<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Types\BillingCycleKind;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerListShowDeleteTrait;
use App\Http\Requests\Admin\LoanVcUpdateRequest;
use App\Http\Requests\Admin\PlanCreateRequest;
use App\Services\PlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class PlanController extends Controller
{
    use ControllerListShowDeleteTrait;

    public function __construct(
        private readonly PlanService $service,
    )
    {
    }

    public function pageMetrics(): JsonResponse
    {
        return $this->responder()->success(
            data: $this->service->pageMetrics(),
            message: 'Plan metrics retrieved successfully'
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

    public function store(PlanCreateRequest $request): JsonResponse
    {
        $opp = $this->service->create(
            createdBy: Auth::id(),
            name: $request->validated('name'),
            price: $request->validated('price'),
            features: $request->validated('features'),
            billingCycle: BillingCycleKind::fromName($request->validated('billing_cycle')),
        );

        return $this->responder()->success(
            data: $opp,
            message: 'Plan created successfully'
        );
    }

    public function update(string $id, PlanCreateRequest $request): JsonResponse
    {
        $opp = $this->service->update(
            id: $id,
            updatedBy: Auth::id(),
            name: $request->validated('name'),
            price: $request->validated('price'),
            features: $request->validated('features'),
            billingCycle: BillingCycleKind::fromName($request->validated('billing_cycle')),
        );

        return $this->responder()->success(
            data: $opp,
            message: 'Plan updated successfully'
        );
    }
}
