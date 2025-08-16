<?php

namespace App\Http\Controllers;

use App\Enums\Statuses\OpportunityStatus;
use App\Enums\SystemSettingDefinition;
use App\Helpers\Http\Responder;
use App\Helpers\SettingHelper;
use App\QueryBuilders\BusinessStageQueryBuilder;
use App\QueryBuilders\GeoCountryQueryBuilder;
use App\QueryBuilders\GeoLocalGovQueryBuilder;
use App\QueryBuilders\GeoStateQueryBuilder;
use App\QueryBuilders\IndustryQueryBuilder;
use App\QueryBuilders\OpportunityTypeQueryBuilder;
use App\Repositories\BusinessStageRepository;
use App\Repositories\GeoCountryRepository;
use App\Repositories\IndustryRepository;
use App\Repositories\OpportunityTypeRepository;
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

    public function opportunityFilters(
        IndustryRepository        $industryRepository,
        BusinessStageRepository   $businessStageRepository,
        OpportunityTypeRepository $opportunityTypeRepository,
        GeoCountryRepository      $geoCountryRepository,
    )
    {
        return $this->responder->success(
            data: [
                'industries' => $industryRepository
                    ->withSelect(['industries.id', 'industries.name'])
                    ->all(),
                'business_stages' => $businessStageRepository
                    ->withSelect(['business_stages.id', 'business_stages.name'])
                    ->all(),
                'opportunity_types' => $opportunityTypeRepository
                    ->withSelect(['opportunity_types.id', 'opportunity_types.name'])
                    ->all(),
                'countries' => $geoCountryRepository
                    ->withSelect(['geo_countries.id', 'geo_countries.name'])
                    ->all(),
                'statuses' => array_map(
                    callback: fn(string $s) => ['id' => $s, 'name' => ucwords(str_replace('_', ' ', $s))],
                    array: OpportunityStatus::getDBCompatibleEnum()
                ),
                'amounts' => [
                    ['id' => '1', 'name' => 'Less than $5,000', 'value' => '0-5000'],
                    ['id' => '2', 'name' => '$5,000 - $10,000', 'value' => '5000-10000'],
                    ['id' => '3', 'name' => '$10,000 - $30,000', 'value' => '10000-30000'],
                    ['id' => '4', 'name' => '$30,000 - $100,000', 'value' => '30000-100000'],
                    ['id' => '5', 'name' => '$100,000+', 'value' => '100000+'],
                ]
            ]
        );
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
     * @param IndustryQueryBuilder $queryBuilder
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function industries(IndustryQueryBuilder $queryBuilder): JsonResponse
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

    public function accountSetupFinaliseData(
        OpportunityTypeRepository $opportunityTypeRepository,
        BusinessStageRepository   $businessStageRepository,
        IndustryRepository        $businessTypeRepository,
    ): JsonResponse
    {
        return $this->responder->success(
            data: [
                'opportunity_types' => $opportunityTypeRepository
                    ->withSelect(['id', 'name'])
                    ->all(),
                'business_stages' => $businessStageRepository
                    ->withSelect(['id', 'name'])
                    ->all(),
                'business_types' => $businessTypeRepository
                    ->withSelect(['id', 'name'])
                    ->all(),
            ],
            message: 'opportunity types, business stages and business types fetched'
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
