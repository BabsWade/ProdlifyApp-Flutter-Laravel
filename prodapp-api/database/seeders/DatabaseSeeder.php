<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Appeler le seeder des rôles et des permissions
        $this->call(RolesAndPermissionsSeeder::class);

        // Créer les rôles admin et user si ce n'est pas déjà fait
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleUser = Role::firstOrCreate(['name' => 'user']);

        // Créer un utilisateur admin
        $adminUser = User::create([
            'name' => 'AdminUser',
            'email' => 'admine@example.com',
            'password' => Hash::make('password1234'), // Mot de passe sécurisé
        ]);

        // Assigner le rôle admin à cet utilisateur
        $adminUser->assignRole('admin');

        // Créer un utilisateur par défaut (user) si nécessaire
        $defaultUser = User::create([
            'name' => 'DefaultUser',
            'email' => 'usere@example.com',
            'password' => Hash::make('password1235'), // Mot de passe sécurisé
        ]);

        // Assigner le rôle user à cet utilisateur
        $defaultUser->assignRole('user');

        // Créer un utilisateur de test (test@example.com)
        User::factory()->create([
            'name' => 'TestUser',
            'email' => 'testtest@example.com',
        ]);
    }
}
