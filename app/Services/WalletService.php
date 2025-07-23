<?php

namespace App\Services;

use App\Enums\Types\PaymentPurpose;
use App\Enums\Types\WalletAction;
use App\Exceptions\MaintenanceException;
use App\Exceptions\ModelNotFoundException;
use App\Exceptions\WarningException;
use App\Models\Payment;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\UserRepository;
use App\Repositories\WalletHistoryRepository;
use App\Repositories\WalletRepository;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class WalletService extends BasePersistableService
{
    public function __construct(
        private readonly WalletRepository        $repository,
        private readonly WalletHistoryRepository $walletHistoryRepository,
        private readonly MailService             $mailService,
        private readonly UserRepository          $userRepository,
    ) {
    }

    public function create(int $userId): Wallet|Model
    {
        return $this->repository->create($userId);
    }

    /**
     * @param int $userId
     * @param float $amount
     * @param string $narration
     * @param bool $withEmailNotification
     * @param bool $throwOnInsufficientBalance
     * @return Wallet|Model
     */
    public function debit(
        int    $userId,
        float  $amount,
        string $narration,
        bool   $withEmailNotification = true,
        bool   $throwOnInsufficientBalance = true
    ): Wallet|Model {
        return DB::transaction(function () use (
            &$wallet,
            $amount,
            $narration,
            $userId,
            $withEmailNotification,
            $throwOnInsufficientBalance
        ) {
            $wallet = $this->repository->findLockedByUserId($userId);

            if ($amount > $wallet['balance']) {
                if ($withEmailNotification) {
                    $user = $this->userRepository->findRequiredById($userId);

                    $this->mailService
                        ->setSubject('Insufficient Balance')
                        ->setRecipient($user['email'], $user->fullName())
                        ->view('mails.wallet-insufficient-balance', compact('user'))
                        ->send();
                }

                if ($throwOnInsufficientBalance) {
                    throw new WarningException('Insufficient wallet balance, please recharge your account');
                }
            }

            $this->walletHistoryRepository->create(
                wallet: $wallet,
                amount: $amount,
                narration: $narration,
                action: WalletAction::DEBIT
            );

            return $this->repository->debit(
                userId: $userId,
                amount: $amount
            );
        });
    }


    /**
     * @param int $userId
     * @param int $amount
     * @param string $narration
     * @return Wallet|Model
     */
    public function credit(
        int $userId,
        int     $amount,
        string  $narration,
    ): Wallet|Model {
        return DB::transaction(function () use ($amount, $narration, $userId) {
            $wallet = $this->repository->findLockedByUserId($userId);

            $this->walletHistoryRepository->create(
                wallet: $wallet,
                amount: $amount,
                narration: $narration,
                action: WalletAction::CREDIT
            );

            return $this->repository->credit(
                userId: $userId,
                amount: $amount
            );
        });
    }

    /**
     * @throws ModelNotFoundException
     */
    public function computeSummary(int $userId): array
    {
        $walletBalance = $this->repository->fetchBalance(
            userId: $userId,
        );

        return [
            'balance' => $walletBalance,
        ];
    }

    /**
     * @param User|Model $user
     * @param float $amount
     * @param string $ua
     * @param string $ip
     * @return Model|Payment
     * @throws MaintenanceException
     * @throws GuzzleException
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function initFunding(User|Model $user, float $amount, string $ua, string $ip): Model|Payment
    {
        return PaymentService::new()->initPayment(
            payer: $user,
            amount: $amount,
            ipAddress: $ip,
            userAgent: $ua,
            callbackUrlPrefix: 'payments',
            metadata: [
                'user_id' => $user['id'],
            ],
        );
    }

    public function repository(): WalletRepository
    {
        return $this->repository;
    }
}
