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

        // Créer quelques créneaux d'exemple pour le club
        $slots = [
            [
                'club_id' => $clubId,
                'name' => 'Agility Débutant',
                'description' => 'Cours d\'agility pour débutants',
                'location' => 'Condat',
                'day_of_week' => 1, // Lundi
                'start_time' => '18:00:00',
                'end_time' => '19:30:00',
                'capacity_type' => 'dynamic',
                'capacity' => null,
                'auto_close' => true,
                'close_duration' => 72,
                'is_restricted' => false,
                'has_groups' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'club_id' => $clubId,
                'name' => 'Agility Confirmé',
                'description' => 'Cours d\'agility pour confirmés',
                'location' => 'Condat',
                'day_of_week' => 1, // Lundi
                'start_time' => '19:30:00',
                'end_time' => '21:00:00',
                'capacity_type' => 'dynamic',
                'capacity' => null,
                'auto_close' => true,
                'close_duration' => 72,
                'is_restricted' => false,
                'has_groups' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'club_id' => $clubId,
                'name' => 'Éducation de Base',
                'description' => 'Cours d\'éducation canine de base',
                'location' => 'Condat',
                'day_of_week' => 3, // Mercredi
                'start_time' => '18:00:00',
                'end_time' => '19:30:00',
                'capacity_type' => 'fixed',
                'capacity' => 8,
                'auto_close' => true,
                'close_duration' => 48,
                'is_restricted' => false,
                'has_groups' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'club_id' => $clubId,
                'name' => 'Obéissance',
                'description' => 'Cours d\'obéissance avancée',
                'location' => 'Condat',
                'day_of_week' => 5, // Vendredi
                'start_time' => '19:00:00',
                'end_time' => '20:30:00',
                'capacity_type' => 'fixed',
                'capacity' => 6,
                'auto_close' => true,
                'close_duration' => 48,
                'is_restricted' => true,
                'has_groups' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($slots as $slot) {
            $slotId = DB::table('slots')->insertGetId($slot);

            // Générer les occurrences pour les 6 prochains mois
            $startDate = now()->startOfWeek();
            $endDate = now()->addMonths(6);

            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                // Trouver le prochain jour de la semaine correspondant
                while ($currentDate->dayOfWeek != $slot['day_of_week']) {
                    $currentDate->addDay();
                }

                // Créer l'occurrence
                DB::table('slot_occurences')->insert([
                    'slot_id' => $slotId,
                    'date' => $currentDate->format('Y-m-d'),
                    'is_cancelled' => false,
                    'is_full' => false,
                    'closing_notification_sent' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Passer à la semaine suivante
                $currentDate->addWeek();
            }
        }

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
        DB::table('slot_occurences')->delete();
        DB::table('slots')->delete();
        DB::table('users')->whereIn('email', [
            'admin@ceccondat.fr',
            'montuy.alexis@gmail.com',
            'test@ceccondat.fr'
        ])->delete();
        DB::table('clubs')->delete();
    }
};
