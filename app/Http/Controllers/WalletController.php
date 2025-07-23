<?php

namespace App\Http\Controllers;

use App\Exceptions\MaintenanceException;
use App\Exceptions\ModelNotFoundException;
use App\Helpers\Http\Responder;
use App\Http\Requests\Wallet\WalletFundRequest;
use App\Models\Payment;
use App\QueryBuilders\WalletHistoryQueryBuilder;
use App\Services\WalletService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class WalletController extends Controller
{
    public function __construct(
        protected readonly Responder   $responder,
        private readonly WalletService $service,
    ) {
    }

    /**
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function index(): JsonResponse
    {
        $wallet = $this->service->repository()->findRequiredByUserId(Auth::id());
        return $this->responder->model(
            model: $wallet,
            notFoundMessage: 'Something went wrong, failed to find wallet'
        );
    }

    /**
     * @param WalletHistoryQueryBuilder $historyQueryBuilder
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function history(WalletHistoryQueryBuilder $historyQueryBuilder): JsonResponse
    {
        $builder = $historyQueryBuilder->filterByUserId(Auth::id());
        return $this->responder->datatableFilterable(builder: $builder);
    }

    /**
     * @throws MaintenanceException
     * @throws Throwable
     * @throws BindingResolutionException
     * @throws GuzzleException
     */
    public function initFunding(WalletFundRequest $request): JsonResponse
    {
        $payment = $this->service->initFunding(
            user: Auth::user(),
            amount: $request->validated('amount'),
            ua: $request->header('User-Agent'),
            ip: $request->ip()
        );

        return $this->responder->success(
            data: $payment,
            message: 'Payment initialized successfully'
        );
    }
}
