<?php

namespace App\Http\Controllers;

use App\Helpers\Http\Responder;
use App\Helpers\RoleHelper;
use App\Http\Requests\Authorization\PermissionPostRequest;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;

class UserPermissionController extends Controller
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
    public function permissions(int $id): JsonResponse
    {
        $permissions = AuthService::new()->getUserPermissions($id);
        return $this->responder->success($permissions);
    }

    /**
     * @param int $id
     * @param PermissionPostRequest $request
     * @return JsonResponse
     */
    public function addPermissions(int $id, PermissionPostRequest $request): JsonResponse
    {
        $permissions = array_intersect(
            array_map(fn (string $p) => strtolower($p), (array)$request->post('permissions')),
            RoleHelper::getPermissionNames()
        );

        $user = $this->userRepository->findRequiredById($id);
        if ([] != $permissions) {
            $user->givePermissionTo($permissions);
        }

        UserService::clearCache($id);

        return $this->responder->successMessage('Permissions added');
    }

    /**
     * @param int $id
     * @param string $permission
     * @return JsonResponse
     */
    public function removePermission(int $id, string $permission): JsonResponse
    {
        $user = $this->userRepository->findRequiredById($id);
        $user->revokePermissionTo($permission);

        UserService::clearCache($id);

        return $this->responder->successMessage('PermissionPermission revoked');
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function assignablePermissions(int $id): JsonResponse
    {
        $user = $this->userRepository->findRequiredById($id);

        $allPermissions = RoleHelper::getPermissions();
        $userPermissions = $user->getAllPermissions()
            ->map(fn ($p) => $p['name'])
            ->toArray();

        $assignable = [];
        foreach ($allPermissions as $permission) {
            foreach ($permission::cases() as $case) {
                if (!in_array(strtolower($case->name), $userPermissions)) {
                    $assignable[] = $case->name;
                }
            }
        }

        return $this->responder->success($assignable);
    }
}
