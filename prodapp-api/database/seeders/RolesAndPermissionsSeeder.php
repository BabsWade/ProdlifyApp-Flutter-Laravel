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

        // Créer les permissions si elles n'existent pas déjà
        Permission::firstOrCreate(['name' => 'create product']);
        Permission::firstOrCreate(['name' => 'view product']);
        Permission::firstOrCreate(['name' => 'edit product']);
        Permission::firstOrCreate(['name' => 'delete product']);

        // Créer les rôles et leur assigner les permissions
        Permission::create(['name' => 'create category']); // Ajoute la permission manquante
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            'create product',
            'view product',
            'edit product',
            'delete product',
            'create category',
        ]);

        $user = Role::firstOrCreate(['name' => 'user']);
        $user->givePermissionTo([
            'view product',
        ]);
    }
}
