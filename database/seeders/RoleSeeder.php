<?php

namespace Database\Seeders;

use App\Enums\EnumHelper\PermissionInterface;
use App\Enums\Permissions\UserPermission;
use App\Enums\UserRole;
use App\Helpers\RoleHelper;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allPermissions = RoleHelper::getPermissionNames();
        foreach (UserRole::cases() as $role) {
            Role::create(['name' => $role->name]);
        }

        $permissions = [];
        foreach ($allPermissions as $permission) {
            $permissions[] = [
                'name' => $permission,
                'guard_name' => 'api',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        Permission::query()->insert($permissions);

        // SUPER ADMIN
        $superAdmin = Role::findByName(UserRole::SUPER_ADMIN->name);

        $superAdmin->syncPermissions($allPermissions);

        $this->syncAdminPermissions();
    }

    protected function syncAdminPermissions(): void
    {
        $this->syncPermissions(
            role: UserRole::ADMIN,
            permissions: array_merge(
                [
                    UserPermission::USER_LIST,
                    UserPermission::USER_READ,
                    UserPermission::USER_ROLE_LIST,
                    UserPermission::USER_PERMISSION_LIST,
                ],
            )
        );
    }

    /**
     * @param UserRole $role
     * @param PermissionInterface[] $permissions
     * @return void
     */
    protected function syncPermissions(UserRole $role, array $permissions): void
    {
        $role = Role::query()
            ->where(['name' => $role->name])
            ->first();

        foreach ($permissions as &$permission) {
            $permission = $permission->toLowercase();
        }

        $role->syncPermissions($permissions);
    }
}
