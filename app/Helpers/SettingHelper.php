<?php

namespace App\Helpers;

use App\Enums\SystemSettingDefinition;
use App\Exceptions\ConfigItemNotFoundException;
use App\Exceptions\MaintenanceException;
use App\Repositories\SystemSettingRepository;

class SettingHelper
{
    public static function getFloat(SystemSettingDefinition $definition): float
    {
        return (float)self::get($definition);
    }

    public static function get(SystemSettingDefinition $definition): string|int
    {
        $setting = SystemSettingRepository::new()->getActive();
        $result = $setting[$definition->value];

        if (null === $result) {
            throw new ConfigItemNotFoundException("No settings/configuration with key '$definition->value' found");
        }

        return $result;
    }

    /**
     * @param SystemSettingDefinition $definition
     * @return void
     * @throws MaintenanceException
     */
    public static function ensureModuleIsActive(SystemSettingDefinition $definition): void
    {
        if (!self::isTrue($definition)) {
            $expModuleName = explode('_MODULE_STATUS', $definition->name);
            $moduleName = ucfirst(strtolower($expModuleName[0]));
            throw new MaintenanceException(self::getModuleMaintenanceMessage($moduleName));
        }
    }

    public static function isTrue(SystemSettingDefinition $definition): bool
    {
        return self::getInt($definition) === 1;
    }

    public static function getInt(SystemSettingDefinition $definition): int
    {
        return intval(self::get($definition));
    }

    public static function getModuleMaintenanceMessage(string $moduleName): array|string
    {
        $message = self::get(SystemSettingDefinition::MODULE_MAINTENANCE_MESSAGE);
        return str_replace('{{module}}', $moduleName, $message);
    }
}
