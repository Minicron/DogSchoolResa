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

        DB::table('users')->insert([
            
            // Erika
            [
                'name'       => 'Montuy',
                'firstname'  => 'Alexis',
                'email'      => 'montuy.alexis@gmail.com',
                'password'   => bcrypt('test123'),
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
            
            // Erika
            [
                'name'       => 'Aguiton',
                'firstname'  => 'Erika',
                'email'      => 'erikaaguiton@hotmail.fr',
                'password'   => bcrypt('test123'),
                'role'       => 'admin-club',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'    => 1,
                'is_active'  => true,
            ],
            [
                'name'       => 'Aguiton',
                'firstname'  => 'Erika',
                'email'      => 'erikaaguiton+user@hotmail.fr',
                'password'   => bcrypt('test123'),
                'role'       => 'member',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'    => 1,
                'is_active'  => true,
            ],

            // Nadege
            [
                'name'       => 'Gauvin',
                'firstname'  => 'Nadege',
                'email'      => 'nadegegauvin@hotmail.fr',
                'password'   => bcrypt('test123'),
                'role'       => 'monitor',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'    => 1,
                'is_active'  => true,
            ],
            [
                'name'       => 'Gauvin',
                'firstname'  => 'Nadege',
                'email'      => 'nadegegauvin+user@hotmail.fr',
                'password'   => bcrypt('test123'),
                'role'       => 'member',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'    => 1,
                'is_active'  => true,
            ],

            // Nathalie G.
            [
                'name'       => 'Gendre',
                'firstname'  => 'Nathalie',
                'email'      => 'pascalge87@gmail.com',
                'password'   => bcrypt('test123'),
                'role'       => 'monitor',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'    => 1,
                'is_active'  => true,
            ],
            [
                'name'       => 'Gendre',
                'firstname'  => 'Nathalie',
                'email'      => 'pascalge87+user@gmail.com',
                'password'   => bcrypt('test123'),
                'role'       => 'member',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'    => 1,
                'is_active'  => true,
            ],

            // Nathalie M.
            [
                'name'       => 'Maillet',
                'firstname'  => 'Nathalie',
                'email'      => 'nn.maillet@gmail.com',
                'password'   => bcrypt('test123'),
                'role'       => 'monitor',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'    => 1,
                'is_active'  => true,
            ],
            [
                'name'       => 'Maillet',
                'firstname'  => 'Nathalie',
                'email'      => 'nn.maillet+user@gmail.com',
                'password'   => bcrypt('test123'),
                'role'       => 'member',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'    => 1,
                'is_active'  => true,
            ],

            // Antoine
            [
                'name'       => 'Lamotte',
                'firstname'  => 'Antoine',
                'email'      => 'antoine.lamotte01@gmail.com',
                'password'   => bcrypt('test123'),
                'role'       => 'monitor',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'    => 1,
                'is_active'  => true,
            ],
            [
                'name'       => 'Lamotte',
                'firstname'  => 'Antoine',
                'email'      => 'antoine.lamotte01+user@gmail.com',
                'password'   => bcrypt('test123'),
                'role'       => 'member',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'    => 1,
                'is_active'  => true,
            ],

            // Caroline
            [
                'name'       => 'Magnesse',
                'firstname'  => 'Caroline',
                'email'      => 'caroline.magnesse@gmail.com',
                'password'   => bcrypt('test123'),
                'role'       => 'monitor',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'    => 1,
                'is_active'  => true,
            ],
            [
                'name'       => 'Magnesse',
                'firstname'  => 'Caroline',
                'email'      => 'caroline.magnesse+user@gmail.com',
                'password'   => bcrypt('test123'),
                'role'       => 'member',
                'created_at' => now(),
                'updated_at' => now(),
                'club_id'    => 1,
                'is_active'  => true,
            ],

            // Valérie (aucun mail fourni)
            // Tu pourras l'ajouter manuellement plus tard si nécessaire
        ]);

        // 4. Création de quelques slots pour le club "CEC Condat" (club_id = 1)
        /*DB::table('slots')->insert([
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
        ]);*/
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
