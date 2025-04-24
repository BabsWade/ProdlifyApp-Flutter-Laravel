<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Réinitialiser le cache de permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        Permission::create(['name' => 'create product']);
        Permission::create(['name' => 'view product']);
        Permission::create(['name' => 'edit product']);
        Permission::create(['name' => 'delete product']);

        // Créer les rôles et leur assigner les permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'create product',
            'view product',
            'edit product',
            'delete product',
        ]);

        $user = Role::create(['name' => 'user']);
        $user->givePermissionTo([
            'view product',
        ]);
    }
}
