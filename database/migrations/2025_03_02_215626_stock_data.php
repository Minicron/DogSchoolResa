<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Insertion de l'administrateur global (user #1)
        DB::table('users')->insert([
            [
                'name'       => 'Montuy',
                'firstname'  => 'Alexis',
                'email'      => 'montuy.alexis@gmail.com',
                'password'   => bcrypt('admin123'),
                'role'       => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
                'is_active'  => true,
            ],
        ]);

        // 2. Création du club "CEC Condat"
        DB::table('clubs')->insert([
            [
                'name'       => 'CEC Condat',
                'city'       => 'Condat',
                'admin_id'   => 1, // l'administrateur global
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 3. Insertion d'utilisateurs pour le club (club_id = 1)
        DB::table('users')->insert([
            // Admin-club générique
            [
                'name'       => 'AdminClub',
                'firstname'  => 'Générique',
                'email'      => 'adminclub.generic@gmail.com',
                'password'   => bcrypt('adminclub123'),
                'role'       => 'admin-club',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'   => 1,
                'is_active'  => true,
            ],
            [
                'name'       => 'Montuy',
                'firstname'  => 'Alexis',
                'email'      => 'montuy.alexis+adminclub@gmail.com',
                'password'   => bcrypt('adminclub123'),
                'role'       => 'admin-club',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'   => 1,
                'is_active'  => true,
            ],
            // Monitors
            [
                'name'       => 'Montuy',
                'firstname'  => 'Alexis',
                'email'      => 'montuy.alexis+monitor@gmail.com',
                'password'   => bcrypt('monitor123'),
                'role'       => 'monitor',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'   => 1,
                'is_active'  => true,
            ],
            [
                'name'       => 'Martin',
                'firstname'  => 'Paul',
                'email'      => 'paul.martin@gmail.com',
                'password'   => bcrypt('monitor456'),
                'role'       => 'monitor',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'   => 1,
                'is_active'  => true,
            ],
            [
                'name'       => 'Durand',
                'firstname'  => 'Emma',
                'email'      => 'emma.durand@gmail.com',
                'password'   => bcrypt('monitor789'),
                'role'       => 'monitor',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'   => 1,
                'is_active'  => true,
            ],
            // Membres
            [
                'name'       => 'Vieu',
                'firstname'  => 'Lauriane',
                'email'      => 'lauriane.vieu@gmail.com',
                'password'   => bcrypt('member123'),
                'role'       => 'member',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'   => 1,
                'is_active'  => true,
            ],
            [
                'name'       => 'Dupont',
                'firstname'  => 'Jean',
                'email'      => 'jean.dupont@gmail.com',
                'password'   => bcrypt('member456'),
                'role'       => 'member',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'   => 1,
                'is_active'  => true,
            ],
            [
                'name'       => 'Petit',
                'firstname'  => 'Clara',
                'email'      => 'clara.petit@gmail.com',
                'password'   => bcrypt('member789'),
                'role'       => 'member',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'   => 1,
                'is_active'  => true,
            ],
            [
                'name'       => 'Roux',
                'firstname'  => 'Pierre',
                'email'      => 'pierre.roux@gmail.com',
                'password'   => bcrypt('member321'),
                'role'       => 'member',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'   => 1,
                'is_active'  => true,
            ],
            [
                'name'       => 'Lemoine',
                'firstname'  => 'Sophie',
                'email'      => 'sophie.lemoine@gmail.com',
                'password'   => bcrypt('member654'),
                'role'       => 'member',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'   => 1,
                'is_active'  => true,
            ],
        ]);

        // 4. Création de quelques slots pour le club "CEC Condat" (club_id = 1)
        DB::table('slots')->insert([
            [
                'club_id'        => 1,
                'name'           => 'Agility Débutant',
                'description'    => 'Cours pour initier votre chien à l\'agility',
                'location'       => 'Terrain 1',
                'day_of_week'    => 6, // Samedi
                'start_time'     => '09:00:00',
                'end_time'       => '10:30:00',
                'capacity'       => 10,
                'is_restricted'  => false,
                'auto_close'     => false,
                'close_duration' => null,
                'alert_monitors' => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'club_id'        => 1,
                'name'           => 'Agility Confirmé',
                'description'    => 'Cours avancé pour chiens habitués à l\'agility',
                'location'       => 'Terrain 2',
                'day_of_week'    => 7, // Dimanche
                'start_time'     => '10:00:00',
                'end_time'       => '11:30:00',
                'capacity'       => 12,
                'is_restricted'  => true,    // Slot restreint
                'auto_close'     => true,
                'close_duration' => 48,      // Clôture 48h avant
                'alert_monitors' => 2,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'club_id'        => 1,
                'name'           => 'Education',
                'description'    => 'Education tout niveaux',
                'location'       => 'Terrain principal',
                'day_of_week'    => 6, // Samedi
                'start_time'     => '14:00:00',
                'end_time'       => '15:00:00',
                'capacity'       => 40,
                'is_restricted'  => false,
                'auto_close'     => true,
                'close_duration' => 24,
                'alert_monitors' => 3,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'club_id'        => 1,
                'name'           => 'Education 2',
                'description'    => 'Education tout niveaux',
                'location'       => 'Terrain principal',
                'day_of_week'    => 7, // Lundi
                'start_time'     => '09:00:00',
                'end_time'       => '10:00:00',
                'capacity'       => 40,
                'is_restricted'  => false,
                'auto_close'     => true,
                'close_duration' => 24,
                'alert_monitors' => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Attention, cette méthode vide les tables concernées. 
        DB::table('slots')->truncate();
        DB::table('clubs')->truncate();
        DB::table('users')->truncate();
    }
};
