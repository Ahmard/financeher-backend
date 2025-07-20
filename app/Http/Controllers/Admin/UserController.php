<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Statuses\UserStatus;
use App\Helpers\Http\Responder;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerStatusManipulatorTrait;
use App\Http\Requests\Authorization\UserRoleAssignPostRequest;
use App\Http\Requests\ReasonRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserController extends Controller
{
    use ControllerStatusManipulatorTrait;

    public function __construct(
        private readonly Responder   $responder,
        private readonly UserService $service
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
        $builder = $this->service
            ->repository()
            ->queryBuilder()
            ->withSearch($this->getSearchQuery())
            ->all();

        return $this->responder->datatableFilterable(
            builder: $builder,
            responseMessage: 'user list fetched successfully'
        );
    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function active(): JsonResponse
    {
        $builder = $this->service
            ->repository()
            ->queryBuilder()
            ->withStatus(UserStatus::ACTIVE)
            ->withSearch($this->getSearchQuery())
            ->all();

        return $this->responder->datatableFilterable(
            builder: $builder,
            responseMessage: 'Active user list fetched successfully'
        );
    }


    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function suspended(): JsonResponse
    {
        $builder = $this->service
            ->repository()
            ->queryBuilder()
            ->withStatus(UserStatus::INACTIVE)
            ->withSearch($this->getSearchQuery())
            ->all();

        return $this->responder->datatableFilterable(
            builder: $builder,
            responseMessage: 'Suspended user list fetched successfully'
        );
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->service->repository()->findRequiredById($id);
        return $this->responder->success(
            $user,
            'user info fetched successfully'
        );
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function roles(int $id): JsonResponse
    {
        return $this->responder->success($this->service->repository()->roles($id));
    }

    /**
     * @param int $id
     * @param UserRoleAssignPostRequest $request
     * @param UserService $userService
     * @return JsonResponse
     */
    public function assignRoles(int $id, UserRoleAssignPostRequest $request, UserService $userService): JsonResponse
    {
        $userService->assignRole($id, intval($request->post('role_id')));
        return $this->responder->successMessage('role assigned to user');
    }

    /**
     * @param int $id
     * @param int $roleId
     * @param UserService $userService
     * @return JsonResponse
     */
    public function unassignRole(int $id, int $roleId, UserService $userService): JsonResponse
    {
        $userService->unassignRole($id, $roleId);
        return $this->responder->successMessage('role assigned to user');
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function permissions(int $id): JsonResponse
    {
        return $this->responder->success($this->service->repository()->permissions($id));
    }

    /**
     * @param int|string $id
     * @param ReasonRequest $request
     * @return JsonResponse
     */
    public function activate(int|string $id, ReasonRequest $request): JsonResponse
    {
        $model = $this->service->activate(
            id: $id,
            activatedBy: Auth::id(),
            reason: $request->reason(),
        );

        return $this->responder->success($model);
    }

    /**
     * @param int|string $id
     * @param ReasonRequest $request
     * @return JsonResponse
     */
    public function deactivate(int|string $id, ReasonRequest $request): JsonResponse
    {
        $model = $this->service->deactivate(
            id: $id,
            deactivatedBy: Auth::id(),
            reason: $request->reason(),
        );

        return $this->responder->success($model);
    }

    public function resendVerificationEmail(int $id): JsonResponse
    {
        $user = $this->service->repository()->findRequiredById($id);
        $this->service->sendAccountVerificationEmail($user);
        return $this->responder->successMessage('Account verification email sent');
    }

    public function accountVerificationUrl(int $id): JsonResponse
    {
        return $this->responder->success(
            data: [
                'link' => $this->service->getUserAccountVerificationUrl($id),
            ],
            message: 'Account verification url fetched successfully'
        );
    }
}
