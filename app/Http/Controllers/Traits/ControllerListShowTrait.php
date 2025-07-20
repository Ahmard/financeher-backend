<?php

namespace App\Http\Controllers\Traits;

use App\Http\Requests\ReasonRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait ControllerListShowTrait
{
    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): JsonResponse
    {
        $qb = $this
            ->service
            ->repository()
            ->queryBuilder();

        if (method_exists($qb, 'withSearch')) {
            $qb = $qb->withSearch($this->getSearchQuery());
        }

        return $this->responder->datatableQueryBuilder($qb);
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
}
