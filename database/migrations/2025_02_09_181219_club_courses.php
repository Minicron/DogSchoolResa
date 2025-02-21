<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->text('affiliates')->nullable();
            $table->text('logo')->nullable();
            $table->text('description')->nullable();
            $table->text('website')->nullable();
            $table->text('facebook')->nullable();
            $table->timestamps();
        });

        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('location')->nullable();
            $table->string('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('capacity')->nullable();
            $table->timestamps();
        });

        Schema::create('slot_occurences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('is_full')->default(false);
            $table->timestamps();
        });

        Schema::create('slot_occurence_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('slot_occurence_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
        
        Schema::create('slot_occurence_monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('slot_occurence_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('user_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('token');
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('firstname');
            $table->string('role');
            $table->boolean('is_sent')->default(false);
            $table->boolean('is_accepted')->default(false);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('club_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('firstname');
            $table->string('role')->default('user');
            $table->string('invitation_token')->nullable();
            $table->boolean('is_active')->default(false);
        });

        DB::table('users')->insert([
            'name' => 'Montuy',
            'firstname' => 'Alexis',
            'email' => 'montuy.alexis@gmail.com',
            'password' => bcrypt('mlknbv%%59BB'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
            'is_active' => true,
        ]);

        DB::table('clubs')->insert([
            'name' => 'CEC Condat',
            'city' => 'Condat',
            'admin_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Montuy',
            'firstname' => 'Alexis',
            'email' => 'montuy.alexis+adminclub@gmail.com',
            'password' => bcrypt('mlknbv%%59BB'),
            'role' => 'admin-club',
            'created_at' => now(),
            'updated_at' => now(),
            'club_id' => 1,
            'is_active' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'Montuy',
            'firstname' => 'Alexis',
            'email' => 'montuy.alexis+monitor@gmail.com',
            'password' => bcrypt('mlknbv%%59BB'),
            'role' => 'monitor',
            'created_at' => now(),
            'updated_at' => now(),
            'club_id' => 1,
            'is_active' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'Vieu',
            'firstname' => 'Lauriane',
            'email' => 'lauriane.vieu@gmail.com',
            'password' => bcrypt('mlknbv%%59BB'),
            'role' => 'member',
            'created_at' => now(),
            'updated_at' => now(),
            'club_id' => 1,
            'is_active' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
        Schema::dropIfExists('slots');
        Schema::dropIfExists('slot_occurences');
        Schema::dropIfExists('slot_occurence_attendees');
        Schema::dropIfExists('slot_occurence_monitors');
        Schema::dropIfExists('user_invitations');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
            $table->dropColumn('club_id');
        });
    }
};
