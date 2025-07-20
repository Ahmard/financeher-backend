<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Http\Responder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authorization\PermissionPostRequest;
use Illuminate\Http\JsonResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct(
        private readonly Responder $responder
    ) {
    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): JsonResponse
    {
        return $this->responder->datatableFilterable(Permission::query());
    }

    /**
     * @param PermissionPostRequest $request
     * @return JsonResponse
     */
    public function store(PermissionPostRequest $request): JsonResponse
    {
        $permissions = [];
        foreach ($request->permissions() as $permission) {
            $permissions[] = [
                'name' => $permission,
                'guard_name' => 'api',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        Permission::query()->insert($permissions);
        return $this->responder->successMessage('created');
    }
}
