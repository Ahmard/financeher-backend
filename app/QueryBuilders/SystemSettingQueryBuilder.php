<?php

namespace App\QueryBuilders;

use App\Helpers\Http\TableColumnFilter;
use App\Models\SystemSetting;
use Illuminate\Database\Eloquent\Builder;

class SystemSettingQueryBuilder extends BaseQueryBuilder
{
    public function pluck(string $column): Builder
    {
        return SystemSetting::query()
            ->select($column)
            ->orderByDesc('system_setting_id');
    }

    public function datatableColumnFilter(): TableColumnFilter
    {
        return parent::datatableColumnFilter()->withCreatorFullName();
    }

    protected function builder(): Builder
    {
        return SystemSetting::withCreatorJoin(['system_settings.*']);
    }
}
