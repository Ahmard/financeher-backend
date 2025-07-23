<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Types\PaymentGateway;
use App\Helpers\Http\Responder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SystemSettingRequest;
use App\Services\SystemSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SystemSettingController extends Controller
{
    public function __construct(
        private readonly Responder            $responder,
        private readonly SystemSettingService $service,
    ) {
    }

    public function store(SystemSettingRequest $request): JsonResponse
    {
        $this->service->create(
            createdBy: Auth::id(),
            data: $request->validated()
        );

        return $this->index();
    }

    public function index(): JsonResponse
    {
        return $this->responder->success(
            data: [
                'current' => $this->service->repository->getActive(forceReload: true),
                'options' => [
                    'payment_gateways' => PaymentGateway::getDBCompatibleEnum(),
                ],
            ],
        );
    }
}
