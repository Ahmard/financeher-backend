<?php

namespace App\Http\Controllers;

use App\Enums\SystemSettingDefinition;
use App\Helpers\Http\Responder;
use App\Helpers\SettingHelper;
use App\QueryBuilders\BusinessStageQueryBuilder;
use App\QueryBuilders\BusinessTypeQueryBuilder;
use App\QueryBuilders\GeoCountryQueryBuilder;
use App\QueryBuilders\GeoLocalGovQueryBuilder;
use App\QueryBuilders\GeoStateQueryBuilder;
use App\QueryBuilders\OpportunityTypeQueryBuilder;
use App\Repositories\PlanRepository;
use Illuminate\Http\JsonResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class MiscController extends Controller
{
    public function __construct(
        private readonly Responder $responder,
    )
    {
    }

    /**
     * @param PlanRepository $repository
     * @return JsonResponse
     */
    public function activePlan(PlanRepository $repository): JsonResponse
    {
        $planId = SettingHelper::get(SystemSettingDefinition::ACTIVE_PLAN_ID);
        $plan = $repository->findRequiredById($planId);

        return $this->responder()->success(
            data: $plan->intoMiscData(),
            message: 'Active plan fetched successfully'
        );
    }

    /**
     * @param BusinessTypeQueryBuilder $queryBuilder
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function businessTypes(BusinessTypeQueryBuilder $queryBuilder): JsonResponse
    {
        return $this->responder->selectable(
            builder: $queryBuilder
                ->withSearch($this->getSearchQuery())
                ->all(),
            idColumn: 'id',
            textColumn: 'name',
            select2Limit: 1000,
        );
    }

    /**
     * @param BusinessStageQueryBuilder $queryBuilder
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function businessStages(BusinessStageQueryBuilder $queryBuilder): JsonResponse
    {
        return $this->responder->selectable(
            builder: $queryBuilder
                ->withSearch($this->getSearchQuery())
                ->all(),
            idColumn: 'id',
            textColumn: 'name',
            select2Limit: 1000,
        );
    }

    /**
     * @param OpportunityTypeQueryBuilder $queryBuilder
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function opportunityTypes(OpportunityTypeQueryBuilder $queryBuilder): JsonResponse
    {
        return $this->responder->selectable(
            builder: $queryBuilder
                ->withSearch($this->getSearchQuery())
                ->all(),
            idColumn: 'id',
            textColumn: 'name',
            select2Limit: 1000,
        );
    }

    /**
     * @param GeoCountryQueryBuilder $queryBuilder
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function countries(GeoCountryQueryBuilder $queryBuilder): JsonResponse
    {
        return $this->responder->selectable(
            builder: $queryBuilder
                ->withSearch($this->getSearchQuery())
                ->all(),
            idColumn: 'id',
            textColumn: 'name',
            select2Limit: 1000,
        );
    }

    /**
     * @param string $id
     * @param GeoStateQueryBuilder $queryBuilder
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function states(string $id, GeoStateQueryBuilder $queryBuilder): JsonResponse
    {
        return $this->responder->selectable(
            builder: $queryBuilder
                ->withSearch($this->getSearchQuery())
                ->filterByCountry($id),
            idColumn: 'id',
            textColumn: 'name',
            select2Limit: 1000,
        );
    }

    /**
     * @param string $id
     * @param GeoLocalGovQueryBuilder $queryBuilder
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function localGovs(string $id, GeoLocalGovQueryBuilder $queryBuilder): JsonResponse
    {
        return $this->responder->selectable(
            builder: $queryBuilder
                ->withSearch($this->getSearchQuery())
                ->filterByState($id),
            idColumn: 'id',
            textColumn: 'name',
            select2Limit: 1000,
        );
    }
}
