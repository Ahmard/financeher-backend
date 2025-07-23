<?php

namespace App\Http\Controllers;

use App\Helpers\Http\Responder;
use App\Http\Requests\Authorization\UserRoleAssignPostRequest;
use App\QueryBuilders\ModelHasRoleQueryBuilder;
use App\Repositories\ModelHasRoleRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserRoleController extends Controller
{
    public function __construct(
        private readonly Responder      $responder,
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function roles(int $id): JsonResponse
    {
        $roles = AuthService::new()->getUserRoles($id);
        return $this->responder->success($roles);
    }

    /**
     * @param int $id
     * @param UserRoleAssignPostRequest $request
     * @param ModelHasRoleRepository $modelHasRoleRepository
     * @param RoleRepository $repository
     * @return JsonResponse
     */
    public function assign(
        int                       $id,
        UserRoleAssignPostRequest $request,
        ModelHasRoleRepository    $modelHasRoleRepository,
        RoleRepository            $repository
    ): JsonResponse {
        if ($modelHasRoleRepository->userHasRole(userId: $id, roleId: $request->validated('role_id'))) {
            return $this->responder->warningMessage('UserPermission has this role assigned already');
        }

        $user = $this->userRepository->findRequiredById($id);
        $role = $repository->findRequiredById($request->validated('role_id'));
        $user->assignRole($role['name']);

        UserService::clearCache($id);

        return $this->responder->successMessage('RolePermission Added');
    }

    /**
     * @param int $id
     * @param int $roleId
     * @return JsonResponse
     */
    public function remove(int $id, int $roleId): JsonResponse
    {
        $user = $this->userRepository->findRequiredById($id);
        $user->removeRole($roleId);

        UserService::clearCache($id);

        return $this->responder->successMessage('RolePermission Removed');
    }

    /**
     * @param int $id
     * @param ModelHasRoleQueryBuilder $queryBuilder
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function assignable(int $id, ModelHasRoleQueryBuilder $queryBuilder): JsonResponse
    {
        return $this->responder->datatable(
            builder: $queryBuilder->filterAssignable($id)
        );
    }
}
