<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AuthSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'users.viewAny',
            'users.manage',
            'categories.viewAny',
            'categories.manage',
            'products.viewAny',
            'products.manage',
            'settings.viewAny',
            'settings.manage',
            'pages.viewAny',
            'pages.manage',
            'seo.manage',
        ];

        $permissionModels = collect($permissions)
            ->mapWithKeys(fn (string $permission) => [
                $permission => Permission::findOrCreate($permission, 'web'),
            ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $adminRole = Role::findOrCreate('admin', 'web');
        $adminRole->syncPermissions($permissionModels->values()->all());

        Role::query()
            ->whereIn('name', ['site_manager', 'feedback_manager', 'content_manager', 'seo_specialist'])
            ->delete();

        Permission::query()
            ->whereIn('name', ['feedback.viewAny', 'feedback.manage'])
            ->delete();

        $admin = User::query()->updateOrCreate(
            ['email' => 'atlantic@atlantic.ru'],
            [
                'name' => 'atlantic',
                'password' => 'atlantic',
            ]
        );
        $admin->syncRoles(['admin']);
    }
}
