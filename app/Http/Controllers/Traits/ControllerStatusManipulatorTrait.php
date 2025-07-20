<?php

namespace App\Http\Controllers\Traits;

use App\Http\Requests\ReasonRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

trait ControllerStatusManipulatorTrait
{
    /**
     * @param int|string $id
     * @param ReasonRequest $request
     * @return JsonResponse
     */
    public function activate(int|string $id, ReasonRequest $request): JsonResponse
    {
        $model = $this->service->activate(
            id: $id,
            activatedBy: Auth::id(),
            reason: $request->reason(),
            ownerId: $this->ownerId ?? null,
        );

        return $this->responder->success($model);
    }

    /**
     * @param int|string $id
     * @param ReasonRequest $request
     * @return JsonResponse
     */
    public function deactivate(int|string $id, ReasonRequest $request): JsonResponse
    {
        $model = $this->service->deactivate(
            id: $id,
            deactivatedBy: Auth::id(),
            reason: $request->reason(),
            ownerId: $this->ownerId ?? null,
        );

        return $this->responder->success($model);
    }
}
