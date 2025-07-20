<?php

namespace App\Repositories;

use App\Enums\SystemSettingDefinition;
use App\Models\SystemSetting;
use App\QueryBuilders\SystemSettingQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class SystemSettingRepository extends BaseRepository
{
    private static SystemSetting|Model $setting;


    public function __construct(
        protected readonly SystemSettingQueryBuilder $queryBuilder,
    )
    {
    }

    public function pluck(SystemSettingDefinition $item): mixed
    {
        return self::$setting[$item->value] ?? null;
    }

    public function getActive(bool $forceReload = false): Model|SystemSetting
    {
        if (!isset(self::$setting) || $forceReload) {
            self::$setting = SystemSetting::query()
                ->orderByDesc('id')
                ->first();
        }

        return self::$setting;
    }

    public function getUserFrontendSettings(): Model|SystemSetting
    {
        return SystemSetting::query()
            ->select([
                'payment_gateway',
                'moniepoint_vat', 'moniepoint_card_charges',
                'moniepoint_transfer_charges', 'app_version',
            ])
            ->orderByDesc('id')
            ->first();
    }

    public function create(int $createdBy, array $data): Model|SystemSetting
    {
        return SystemSetting::query()->create(array_merge($data, [
            'created_by' => $createdBy,
        ]));
    }

    public function queryBuilder(): SystemSettingQueryBuilder
    {
        return $this->queryBuilder;
    }
}
