<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer le compte d'administration principal
        $adminUserId = DB::table('users')->insertGetId([
            'name' => 'Administrateur',
            'firstname' => 'Principal',
            'email' => 'admin@ceccondat.fr',
            'password' => Hash::make('admin'),
            'role' => 'super-admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Créer le club CEC Condat
        $clubId = DB::table('clubs')->insertGetId([
            'name' => 'CEC Condat',
            'city' => 'Condat-Sur-Vienne',
            'admin_id' => $adminUserId,
            'description' => 'Club d\'Éducation Canine de Condat-Sur-Vienne',
            'website' => 'https://ceccondat.e-monsite.com/',
            'facebook' => null,
            'affiliates' => null,
            'logo' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mettre à jour l'utilisateur admin avec le club_id
        DB::table('users')
            ->where('id', $adminUserId)
            ->update([
                'club_id' => $clubId,
                'updated_at' => now(),
            ]);

        // Créer un compte admin-club pour la gestion quotidienne
        $adminClubUserId = DB::table('users')->insertGetId([
            'name' => 'Montuy',
            'firstname' => 'Alexis',
            'email' => 'montuy.alexis@gmail.com',
            'password' => Hash::make('admin'),
            'club_id' => $clubId,
            'role' => 'admin-club',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Créer un compte utilisateur de test
        $testUserId = DB::table('users')->insertGetId([
            'name' => 'Test',
            'firstname' => 'Utilisateur',
            'email' => 'test@ceccondat.fr',
            'password' => Hash::make('test'),
            'club_id' => $clubId,
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer les données dans l'ordre inverse des dépendances
        DB::table('users')->whereIn('email', [
            'admin@ceccondat.fr',
            'montuy.alexis@gmail.com',
            'test@ceccondat.fr'
        ])->delete();
        DB::table('clubs')->delete();
    }
};
