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
            'feedback.viewAny',
            'feedback.manage',
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
        $siteManagerRole = Role::findOrCreate('site_manager', 'web');
        $feedbackManagerRole = Role::findOrCreate('feedback_manager', 'web');
        $contentManagerRole = Role::findOrCreate('content_manager', 'web');
        $seoSpecialistRole = Role::findOrCreate('seo_specialist', 'web');

        $adminRole->syncPermissions($permissionModels->values()->all());
        $siteManagerRole->syncPermissions([
            $permissionModels['categories.viewAny'],
            $permissionModels['categories.manage'],
            $permissionModels['products.viewAny'],
            $permissionModels['products.manage'],
        ]);
        $feedbackManagerRole->syncPermissions([
            $permissionModels['feedback.viewAny'],
            $permissionModels['feedback.manage'],
        ]);
        $contentManagerRole->syncPermissions([
            $permissionModels['pages.viewAny'],
            $permissionModels['pages.manage'],
        ]);
        $seoSpecialistRole->syncPermissions([
            $permissionModels['seo.manage'],
        ]);

        $admin = User::query()->updateOrCreate(
            ['email' => 'atlantic@atlantic.ru'],
            [
                'name' => 'atlantic',
                'password' => 'atlantic',
            ]
        );
        $admin->syncRoles(['admin']);

        $siteManager = User::query()->updateOrCreate(
            ['email' => 'site.manager@atlantic.ru'],
            [
                'name' => 'site_manager',
                'password' => 'atlantic',
            ]
        );
        $siteManager->syncRoles(['site_manager']);

        $feedbackManager = User::query()->updateOrCreate(
            ['email' => 'feedback.manager@atlantic.ru'],
            [
                'name' => 'feedback_manager',
                'password' => 'atlantic',
            ]
        );
        $feedbackManager->syncRoles(['feedback_manager']);

        $contentManager = User::query()->updateOrCreate(
            ['email' => 'content.manager@atlantic.ru'],
            [
                'name' => 'content_manager',
                'password' => 'atlantic',
            ]
        );
        $contentManager->syncRoles(['content_manager']);

        $seoSpecialist = User::query()->updateOrCreate(
            ['email' => 'seo@atlantic.ru'],
            [
                'name' => 'seo_specialist',
                'password' => 'atlantic',
            ]
        );
        $seoSpecialist->syncRoles(['seo_specialist']);
    }
}
