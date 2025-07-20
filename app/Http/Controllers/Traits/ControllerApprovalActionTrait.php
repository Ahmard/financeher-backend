<?php

namespace App\Http\Controllers\Traits;

use App\Exceptions\WarningException;
use App\Http\Requests\ReasonRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

trait ControllerApprovalActionTrait
{
    /**
     * @param string|int $id
     * @return JsonResponse
     * @throws WarningException
     */
    public function approve(string|int $id): JsonResponse
    {
        $model = $this->service->approve(
            id: $id,
            approvedBy: Auth::id(),
        );

        return $this->responder->success($model);
    }

    /**
     * @param string|int $id
     * @param ReasonRequest $request
     * @return JsonResponse
     * @throws WarningException
     */
    public function reject(string|int $id, ReasonRequest $request): JsonResponse
    {
        $model = $this->service->reject(
            id: $id,
            rejectedBy: Auth::id(),
            reason: $request->reason(),
        );

        return $this->responder->success($model);
    }
}
