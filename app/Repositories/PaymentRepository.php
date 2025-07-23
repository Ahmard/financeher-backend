<?php

namespace App\Repositories;

use App\Enums\Statuses\PaymentStatus;
use App\Enums\Types\PaymentGateway;
use App\Enums\Types\PaymentPurpose;
use App\Models\Payment;
use App\QueryBuilders\PaymentQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class PaymentRepository extends BaseRepository
{
    public function __construct(
        protected readonly PaymentQueryBuilder $queryBuilder,
    ) {
    }

    public function findByReference(string $ref): Payment|Model|null
    {
        return $this->queryBuilder->all()
            ->where('payments.reference', $ref)
            ->first();
    }

    public function findByGatewayReference(string $ref, ?PaymentStatus $status = null): Payment|Model|null
    {
        $builder = $this->queryBuilder->all()
            ->where('payments.gateway_reference', $ref);

        if ($status) {
            $builder->where('payments.status', $status->lowercase());
        }

        return $builder->first();
    }

    public function update(string $id, array $data): Payment|Model
    {
        $payment = $this->findById($id);
        $payment->update($data);

        return $payment;
    }

    public function findById(int|string $id): Payment|Model|null
    {
        return $this->queryBuilder->filterById($id)->first();
    }

    public function init(
        int            $payerId,
        float          $amount,
        float          $charges,
        float          $computedAmount,
        string         $ipAddress,
        string         $userAgent,
        string         $reference,
        PaymentPurpose $purpose,
        array          $metadata = [],
        PaymentStatus $status = PaymentStatus::PENDING,
        PaymentGateway $paymentGateway = PaymentGateway::MONNIFY,
    ): Model|Payment {
        return $this->create([
            'payer_id' => $payerId,
            'amount' => $amount,
            'purpose' => $purpose->lowercase(),
            'metadata' => json_encode($metadata),
            'charges' => $charges,
            'reference' => $reference,
            'computed_amount' => $computedAmount,
            'status' => $status->lowercase(),
            'gateway' => $paymentGateway->lowercase(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    public function create(array $data): Model|Payment
    {
        return Payment::query()->create($data);
    }

    public function queryBuilder(): PaymentQueryBuilder
    {
        return $this->queryBuilder;
    }
}
