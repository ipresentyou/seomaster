<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Permissions
        $permissions = [
            // SEO Tools
            'seo.products.view',
            'seo.products.generate',
            'seo.products.save',
            'seo.categories.view',
            'seo.categories.generate',
            'seo.categories.save',
            'seo.alttext.view',
            'seo.alttext.generate',
            'seo.alttext.save',
            'seo.gsc.view',

            // API Credentials
            'credentials.view',
            'credentials.create',
            'credentials.delete',

            // Subscriptions
            'subscriptions.view',
            'subscriptions.cancel',

            // Admin
            'admin.users.view',
            'admin.users.manage',
            'admin.plans.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Rollen
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $user = Role::firstOrCreate(['name' => 'user']);
        $user->givePermissionTo([
            'seo.products.view',
            'seo.products.generate',
            'seo.products.save',
            'seo.categories.view',
            'seo.categories.generate',
            'seo.categories.save',
            'seo.alttext.view',
            'seo.alttext.generate',
            'seo.alttext.save',
            'seo.gsc.view',
            'credentials.view',
            'credentials.create',
            'credentials.delete',
            'subscriptions.view',
            'subscriptions.cancel',
        ]);

        $agency = Role::firstOrCreate(['name' => 'agency']);
        $agency->givePermissionTo($user->permissions->merge([
            // Agencies bekommen dieselben Rechte wie User
            // Erweiterbar für Multi-Client-Features
        ]));

        // Subscription Plans
        SubscriptionPlan::firstOrCreate(['slug' => 'starter'], [
            'name'                   => 'Starter',
            'description'            => 'Perfekt für einen Shop',
            'price_monthly'          => 19.00,
            'price_yearly'           => 190.00,
            'features'               => ['seo_products', 'seo_categories', 'alt_text'],
            'max_shops'              => 1,
            'max_api_calls_per_day'  => 50,
            'is_active'              => true,
            'sort_order'             => 1,
        ]);

        SubscriptionPlan::firstOrCreate(['slug' => 'pro'], [
            'name'                   => 'Pro',
            'description'            => 'Bis zu 3 Shops + GSC',
            'price_monthly'          => 49.00,
            'price_yearly'           => 490.00,
            'features'               => ['seo_products', 'seo_categories', 'alt_text', 'gsc_dashboard', 'bulk_generate'],
            'max_shops'              => 3,
            'max_api_calls_per_day'  => 300,
            'is_active'              => true,
            'sort_order'             => 2,
        ]);

        SubscriptionPlan::firstOrCreate(['slug' => 'agency'], [
            'name'                   => 'Agency',
            'description'            => 'Unlimitiert + CSV Export',
            'price_monthly'          => 149.00,
            'price_yearly'           => 1490.00,
            'features'               => ['seo_products', 'seo_categories', 'alt_text', 'gsc_dashboard', 'bulk_generate', 'export_csv'],
            'max_shops'              => 20,
            'max_api_calls_per_day'  => 2000,
            'is_active'              => true,
            'sort_order'             => 3,
        ]);

        // Admin User
        $adminUser = User::firstOrCreate(['email' => 'admin@lavarell.com'], [
            'name'     => 'Admin',
            'password' => bcrypt('changeme123!'),
            'status'   => 'active',
        ]);
        $adminUser->assignRole('admin');

        $this->command->info('✅ Rollen, Permissions, Pläne und Admin-User angelegt.');
        $this->command->warn('⚠️  Bitte Passwort von admin@lavarell.com sofort ändern!');
    }
}
