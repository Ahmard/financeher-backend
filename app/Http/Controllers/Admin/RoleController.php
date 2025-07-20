<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Http\Responder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authorization\PermissionPostRequest;
use App\Http\Requests\Authorization\RolePostRequest;
use App\Http\Requests\ReasonRequest;
use App\QueryBuilders\PermissionQueryBuilder;
use App\Repositories\PermissionRepository;
use App\Services\CacheService;
use Illuminate\Http\JsonResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        private readonly Responder $responder,
    )
    {
    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): JsonResponse
    {
        return $this->responder->datatableFilterable(Role::query());
    }

    public function store(RolePostRequest $request): JsonResponse
    {
        $role = Role::query()->create($request->validated());
        return $this->responder->success($role);
    }

    public function update(int $id, RolePostRequest $request): JsonResponse
    {
        $role = Role::query()->find($id);
        $role->update(['name' => $request->validated('name')]);
        return $this->responder->success($role);
    }

    public function destroy(int $id, ReasonRequest $request): JsonResponse
    {
        $role = Role::query()->find($id);
        $role->delete();
        return $this->responder->successMessage('RolePermission deleted successfully');
    }

    public function show(int $id): JsonResponse
    {
        return $this->responder->success(Role::query()->find($id));
    }

    /**
     * @param int $id
     * @param PermissionQueryBuilder $queryBuilder
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function permissions(int $id, PermissionQueryBuilder $queryBuilder): JsonResponse
    {
        $builder = $queryBuilder->filterRolePermissions($id);
        return $this->responder->datatableFilterable($builder);
    }

    /**
     * @param int $id
     * @param PermissionRepository $repository
     * @return JsonResponse
     */
    public function permissionsAssignable(int $id, PermissionRepository $repository): JsonResponse
    {
        $permissions = $repository->fetchRoleAssignable(
            roleId: $id,
            searchQuery: $this->getSearchQuery()
        );

        return $this->responder->success($permissions);
    }

    /**
     * @param int $id
     * @param PermissionPostRequest $request
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function permissionsAssign(int $id, PermissionPostRequest $request): JsonResponse
    {
        CacheService::clear();

        $role = Role::findById($id);
        $role->givePermissionTo($request->permissions());
        return $this->permissions(
            id: $id,
            queryBuilder: PermissionQueryBuilder::new()
        );
    }

    /**
     * @param int $id
     * @param string $permName
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function permissionsUnassign(int $id, string $permName): JsonResponse
    {
        CacheService::clear();

        $role = Role::findById($id);
        $role->revokePermissionTo($permName);

        return $this->permissions(
            id: $id,
            queryBuilder: PermissionQueryBuilder::new()
        );
    }
}
