<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Statuses\PaymentStatus;
use App\Exceptions\MaintenanceException;
use App\Exceptions\ModelNotFoundException;
use App\Exceptions\WarningException;
use App\Helpers\Http\Responder;
use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function __construct(
        private readonly Responder      $responder,
        private readonly PaymentService $service,
    )
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): JsonResponse
    {
        return $this->responder->datatableFilterable(
            builder: $this
                ->service
                ->repository
                ->queryBuilder()
                ->all(),
            responseMessage: 'payment history retrieved'
        );
    }

    /**
     * @param string $reference
     * @return JsonResponse
     * @throws BindingResolutionException
     * @throws GuzzleException
     * @throws MaintenanceException
     * @throws ModelNotFoundException
     * @throws WarningException
     */
    public function verify(string $reference): JsonResponse
    {
        $result = $this->service->verifyTransaction(
            userId: Auth::id(),
            reference: $reference
        );

        if ($result->status == PaymentStatus::PENDING) {
            throw new WarningException('You are yet to make payment or payment is yet to be verified');
        }

        return $this->responder->success($result->payment);
    }
}
