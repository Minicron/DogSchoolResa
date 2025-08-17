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

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->whereIn('email', [
            'montuy.alexis@gmail.com',
        ])->delete();
        DB::table('clubs')->delete();
    }
};
