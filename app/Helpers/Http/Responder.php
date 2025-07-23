<?php

namespace App\Helpers\Http;

use App\Enums\HttpRequestPurpose;
use App\Exceptions\ModelNotFoundException;
use App\Models\BaseModel;
use App\QueryBuilders\BaseQueryBuilder;
use App\Services\BasePersistableService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class Responder
{
    public function __construct()
    {
        if (config('app.mimic_remote_env')) {
            sleep(intval(config('app.mimic_response_sleep')));
        }
    }

    public static function new(): Responder
    {
        return new Responder();
    }

    public function created(BaseModel|Model|array $data, ?string $message = null): JsonResponse
    {
        return $this->customResponse(201, $message, true, $data);
    }

    public function success(array|object|null $data, ?string $message = null): JsonResponse
    {
        if ($data instanceof stdClass) {
            $data = (array)$data;
        }

        return $this->customResponse(200, $message, true, $data);
    }

    public function successMessage(string $message): JsonResponse
    {
        return $this->customResponse(200, $message, true);
    }

    public function serverError(string $message): JsonResponse
    {
        return $this->error(500, $message);
    }

    public function error(int $code, mixed $message): JsonResponse
    {
        return $this->customResponse($code, $message, false);
    }

    public function redirect(string $url): RedirectResponse
    {
        return redirect($url);
    }

    public function json(mixed $data, int $code = 200): JsonResponse
    {
        return response()->json(
            data: $data,
            status: $code
        );
    }

    public function customResponse(
        int               $code,
        ?string           $message,
        bool              $success,
        array|object|null $data = []
    ): JsonResponse {
        return response()->json(
            data: [
                'status' => $code,
                'success' => $success,
                'message' => $message,
                'data' => $data,
            ],
            status: $code
        );
    }

    public function errorMessage(mixed $message, int $status = 500): JsonResponse
    {
        return $this->error($status, $message);
    }

    public function forbidden(mixed $message): JsonResponse
    {
        return $this->customResponse(403, $message, false);
    }

    public function unauthorized(mixed $message): JsonResponse
    {
        return $this->customResponse(401, $message, false);
    }

    public function warningMessage(string $message, array $metadata = []): JsonResponse
    {
        return $this->customResponse(400, $message, false, $metadata);
    }

    public function view(string $view, array $data = []): View
    {
        return view($view, $data);
    }

    public function renderView(string $view, int $status, array $data = []): Response
    {
        return new Response($this->view($view, $data), $status);
    }

    public function model(Model|null $model, string $notFoundMessage, array $data = []): JsonResponse
    {
        if (null == $model) {
            throw new ModelNotFoundException($notFoundMessage);
        }

        if (!empty($data)) {
            return $this->success(data: array_merge(
                ['model' => $model],
                $data
            ));
        }

        return $this->success(data: $model);
    }

    public function notFound(string $message): JsonResponse
    {
        return $this->customResponse(404, $message, false);
    }

    public function validationError(array $errors): JsonResponse
    {
        $first = $errors[array_key_first($errors)][0];
        return response()->json(
            data: [
                'status' => 400,
                'success' => false,
                'errors' => $errors,
                'message' => $first,
            ],
            status: 400
        );
    }

    /**
     * @param BaseQueryBuilder $queryBuilder
     * @param string|null $responseMessage
     * @param array $additionalStatuses
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function datatableQueryBuilder(
        BaseQueryBuilder $queryBuilder,
        ?string          $responseMessage = null,
        array            $additionalStatuses = []
    ): JsonResponse {
        return $this->datatableFilterable(
            builder: $queryBuilder->all(),
            additionalStatuses: $additionalStatuses,
            columnFilter: $queryBuilder->datatableColumnFilter(),
            responseMessage: $responseMessage,
        );
    }

    /**
     * @param BasePersistableService $service
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function listFromService(BasePersistableService $service): JsonResponse
    {
        $queryBuilder = $service->repository()->queryBuilder();

        if (method_exists($queryBuilder, 'withSearch')) {
            $queryBuilder = $queryBuilder->withSearch($this->getSearchQuery());
        }

        return $this->datatableFilterable(
            builder: $queryBuilder->all(),
            responseMessage: 'list retrieved successfully'
        );
    }


    private function getSearchQuery(): ?string
    {
        $params = request()->get('filter') ?? [];
        $search = $params['search'] ?? null;
        if (!$search || $search == 'undefined') {
            return null;
        }

        return $search;
    }

    /**
     * @param EloquentBuilder $builder
     * @param array $additionalStatuses
     * @param TableFilter|null $tableFilter
     * @param TableColumnFilter|null $columnFilter
     * @param callable|null $manipulate
     * @param bool $withoutLimit
     * @param string|null $responseMessage
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function datatableFilterable(
        Builder            $builder,
        array              $additionalStatuses = [],
        ?TableFilter       $tableFilter = null,
        ?TableColumnFilter $columnFilter = null,
        ?callable          $manipulate = null,
        bool               $withoutLimit = false,
        ?string            $responseMessage = null,
    ): JsonResponse {
        if (!$tableFilter) {
            $tableName = $builder->getModel()->getTable();
            $tableFilter = TableFilter::useDateBasic(
                status: "$tableName.status",
                date: "$tableName.created_at"
            )->useAdditionalStatuses($additionalStatuses);
        }

        return $this->datatable(
            builder: $builder,
            filter: $tableFilter,
            columnFilter: $columnFilter,
            manipulate: $manipulate,
            withoutLimit: $withoutLimit,
            responseMessage: $responseMessage,
        );
    }

    /**
     * @param EloquentBuilder $builder
     * @param TableFilter|array $filter
     * @param TableColumnFilter|null $columnFilter
     * @param callable|null $manipulate
     * @param bool $withoutLimit
     * @param string|null $responseMessage
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function datatable(
        Builder            $builder,
        TableFilter|array  $filter = [],
        ?TableColumnFilter $columnFilter = null,
        ?callable          $manipulate = null,
        bool               $withoutLimit = false,
        ?string            $responseMessage = null,
    ): JsonResponse {
        if ($filter instanceof TableFilter) {
            $filter = $filter->getFilters();
        }

        $additionalStatuses = $filter['additional_statuses'] ?? [];

        $clientFilter = request()->get('filter') ?? [];

        if (isset($filter['status']) && !empty($clientFilter['status']) && 'all' != $clientFilter['status']) {
            if (!empty($additionalStatuses)) {  // Ability to use multiple statuses, Eg: active & pending
                $additionalStatuses[] = $clientFilter['status'];
                $builder->whereIn($filter['status'], $additionalStatuses);
            } else {
                $builder->where($filter['status'], $clientFilter['status']);
            }
        }

        if (isset($clientFilter['stage'])) {
            $builder->where('stage', $clientFilter['stage']);
        }

        if (isset($filter['start_date']) && !empty($clientFilter['start_date'])) {
            $builder->whereDate($filter['start_date'], '>=', $clientFilter['start_date']);
            if (isset($filter['end_date']) && !empty($clientFilter['end_date'])) {
                $builder->whereDate($filter['end_date'], '<=', $clientFilter['end_date']);
            }
        }

        if ($withoutLimit) {
            $limit = 10000;
        } else {
            $limit = request()->get('limit') ?? 10;
            if ($limit > 50) {
                $limit = 50;
            }
        }

        // ensure it's not datatable calling
        if ($limit && !request()->has('start') && !request()->has('length')) {
            $builder->limit($limit);
        }

        if (request()->has('page')) {
            $builder->paginate(perPage: $limit, page: request()->get('page'));
        }

        return match (request()->get('purpose')) {
            'form' => $this->success($builder->get()),
            'pagination' => $this->success($builder->paginate()),
            default => (function () use ($builder, $manipulate, $columnFilter, $responseMessage) {
                $datatable = DataTables::of($builder);

                if ($columnFilter) {
                    foreach ($columnFilter->getFilters() as $column => $filterData) {
                        /**@phpstan-ignore-next-line * */
                        $datatable->filterColumn($column, function (Builder $builder, string $keyword) use ($filterData) {
                            // Call bindings callback
                            $bindings = $filterData['binding']($keyword);
                            // Bind query
                            $builder->whereRaw($filterData['sql'], $bindings);
                        });
                    }
                }

                $data = $datatable->toArray();

                if ($manipulate) {
                    $data['data'] = $manipulate($data['data']);
                }

                return $this->success(data: $data, message: $responseMessage);
            })()
        };
    }

    /**
     * @param EloquentBuilder $builder
     * @param string $idColumn
     * @param string|array $textColumn
     * @param callable|null $callback
     * @param int $select2Limit
     * @param TableFilter|null $tableFilter
     * @param TableColumnFilter|null $columnFilter
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function selectable(
        Builder            $builder,
        string             $idColumn,
        string|array       $textColumn,
        ?callable          $callback = null,
        int                $select2Limit = 15,
        ?TableFilter       $tableFilter = null,
        ?TableColumnFilter $columnFilter = null,
    ): JsonResponse {
        if (HttpRequestPurpose::FORM_SELECT->lowercase() == request()->get('purpose')) {
            return $this->select2(
                data: $builder,
                idColumn: $idColumn,
                textColumn: $textColumn,
                limit: $select2Limit,
                callback: $callback,
            );
        }

        return $this->datatableFilterable(
            builder: $builder,
            tableFilter: $tableFilter,
            columnFilter: $columnFilter,
        );
    }

    public function select2(
        array|Collection|Builder $data,
        string                   $idColumn,
        string|array             $textColumn,
        int                      $limit = 15,
        ?callable                $callback = null
    ): JsonResponse {
        return Select2::render(
            data: $data,
            idColumn: $idColumn,
            textColumn: $textColumn,
            limit: $limit,
            callback: $callback
        );
    }
}
