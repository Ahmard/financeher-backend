<?php

namespace App\Http\Controllers;

use App\Helpers\Http\Responder;
use App\Http\Requests\Authentication\EmailRequest;
use App\QueryBuilders\GeoCountryQueryBuilder;
use App\QueryBuilders\GeoLocalGovQueryBuilder;
use App\QueryBuilders\GeoStateQueryBuilder;
use App\Repositories\UserRepository;
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

    public function userLookup(EmailRequest $request, UserRepository $repository): JsonResponse
    {
        $user = $repository->findByEmail($request->validated('email'));
        return $this->responder->success(
            data: [
                'found' => $user != null,
                'user' => $user != null ? [
                    'id' => $user['id'],
                    'full_name' => sprintf('%s %s', $user['first_name'], $user['last_name']),
                ] : null
            ],
            message: $user == null ? 'user not found' : 'user found'
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
     * @param int $id
     * @param GeoStateQueryBuilder $queryBuilder
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function states(int $id, GeoStateQueryBuilder $queryBuilder): JsonResponse
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
     * @param int $id
     * @param GeoLocalGovQueryBuilder $queryBuilder
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function localGovs(int $id, GeoLocalGovQueryBuilder $queryBuilder): JsonResponse
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
