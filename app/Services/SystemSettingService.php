<?php

namespace App\Services;

use App\Enums\SystemSettingDefinition;
use App\Helpers\SettingHelper;
use App\Models\SystemSetting;
use App\Repositories\SystemSettingRepository;
use Illuminate\Database\Eloquent\Model;

class SystemSettingService extends BaseService
{
    public function __construct(
        public readonly SystemSettingRepository $repository,
    ) {
    }

    public function updateItem(SystemSettingDefinition $definition, string|int $value): void
    {
        $setting = $this->repository->getActive();
        $setting->update([$definition->value => $value]);
    }

    /**
     * @param int $createdBy
     * @param array $data
     * @param bool $allowUpdatingStandalone Whether to allow updating settings that have standalone endpoints
     * @return Model|SystemSetting
     */
    public function create(
        int   $createdBy,
        array $data,
        bool  $allowUpdatingStandalone = false
    ): Model|SystemSetting {
        $settings = defined('IS_MIGRATING')
            ? $data
            : array_merge($this->repository->getActive()->toArray(), $data);

        unset(
            $settings['id'],
            $settings['updated_at'],
            $settings['created_at'],
            $settings['deleted_at'],
        );

        if (!$allowUpdatingStandalone) {
            unset($settings[SystemSettingDefinition::WALLET_BILLABLE_ENTITY->value]);
        }

        if (!$settings['wallet_module_status'] && SettingHelper::isTrue(SystemSettingDefinition::WALLET_MODULE_STATUS)) {
            $settings['payment_module_status'] = false;
        }

        return $this->repository->create(
            createdBy: $createdBy,
            data: $settings
        );
    }
}
