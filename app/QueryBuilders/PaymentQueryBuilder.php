<?php

namespace App\QueryBuilders;

use App\Helpers\Http\TableColumnFilter;
use App\Models\Payment;
use App\Models\User;
use App\QueryBuilders\Traits\SearchableQueryBuilderTrait;
use Illuminate\Database\Eloquent\Builder;

class PaymentQueryBuilder extends BaseQueryBuilder
{
    use SearchableQueryBuilderTrait;

    public function filterById(int|string $id): Builder
    {
        return $this->displayable()->where('payments.id', $id);
    }

    public function filterByPayerId(int $userId): Builder
    {
        return $this->displayable()->where('payments.payer_id', $userId);
    }

    public function datatableColumnFilter(): TableColumnFilter
    {
        return parent::datatableColumnFilter()->withFullName();
    }

    public function displayable(): Builder
    {
        return Payment::query()
            ->select([
                'payments.amount', 'payments.charges', 'payments.computed_amount',
                'payments.created_at', 'payments.is_manual_capture', $this->useFullName(),
                'payments.paid_at', 'payments.payer_id', 'payments.gateway',
                'payments.id', 'payments.method', 'payments.checkout_url',
                'payments.purpose', 'payments.gateway_reference', 'payments.status',
                'payments.paid_amount', 'is_direct_transfer', 'reference',
            ])
            ->join('users', 'users.id', 'payments.payer_id')
            ->orderByDesc('payments.created_at');
    }

    protected function builder(): Builder
    {
        return Payment::query()
            ->select([
                'payments.*',
                User::getFullNameColumn(),
            ])
            ->join('users', 'users.id', 'payments.payer_id');
    }
}
