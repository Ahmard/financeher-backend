<?php

namespace App\Http\Controllers\Traits;

use App\Http\Requests\ReasonRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait ControllerListShowDeleteTrait
{
    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): JsonResponse
    {
        return $this->responder->listFromService($this->service);
    }

    /**
     * @param string|int $id
     * @return JsonResponse
     */
    public function show(string|int $id): JsonResponse
    {
        $model = $this->service->repository()->findRequiredById($id);
        return $this->responder->success($model);
    }

    /**
     * @param string|int $id
     * @param ReasonRequest $request
     * @return JsonResponse
     */
    public function destroy(string|int $id, ReasonRequest $request): JsonResponse
    {
        $this->service->delete(
            id: $id,
            deletedBy: Auth::id(),
            reason: $request->reason(),
        );

        return $this->responder->successMessage('deleted');
    }
}
