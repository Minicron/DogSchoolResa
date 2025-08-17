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
        // Table users avec tous les champs nécessaires
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('firstname');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('user');
            $table->string('invitation_token')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('calendar_view')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        // Table password_reset_tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Table sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Table cache
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        // Table jobs
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        // Table failed_jobs
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // Table clubs
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

        // Ajouter la clé étrangère club_id à la table users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('club_id')->nullable()->constrained()->onDelete('cascade');
        });

        // Table slots avec tous les champs
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
            $table->enum('capacity_type', ['none', 'fixed', 'dynamic'])->default('none');
            $table->boolean('auto_close')->default(false);
            $table->integer('close_duration')->nullable();
            $table->boolean('is_restricted')->default(false);
            $table->boolean('has_groups')->default(false);
            $table->timestamps();
        });

        // Table slot_groups
        Schema::create('slot_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Table slot_occurences avec tous les champs
        Schema::create('slot_occurences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('is_full')->default(false);
            $table->boolean('closing_notification_sent')->default(false);
            $table->timestamps();
        });

        // Table slot_occurence_attendees
        Schema::create('slot_occurence_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('slot_occurence_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Table slot_occurence_monitors
        Schema::create('slot_occurence_monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('slot_occurence_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Table restricted_slot_whitelists
        Schema::create('restricted_slot_whitelists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Table user_invitations
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

        // Table slot_occurence_histos
        Schema::create('slot_occurence_histos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_occurence_id')->constrained('slot_occurences')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action');
            $table->text('details')->nullable();
            $table->timestamps();
        });

        // Table slot_occurence_cancellations
        Schema::create('slot_occurence_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_occurence_id')->constrained('slot_occurences')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('reason');
            $table->timestamps();
        });

        // Table waiting_lists
        Schema::create('waiting_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('slot_occurence_id')->constrained()->onDelete('cascade');
            $table->timestamp('joined_at')->useCurrent();
            $table->boolean('is_notified')->default(false);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['slot_occurence_id', 'joined_at']);
            $table->unique(['user_id', 'slot_occurence_id']); // Un utilisateur ne peut être qu'une fois en liste d'attente pour une occurrence
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waiting_lists');
        Schema::dropIfExists('slot_occurence_cancellations');
        Schema::dropIfExists('slot_occurence_histos');
        Schema::dropIfExists('user_invitations');
        Schema::dropIfExists('restricted_slot_whitelists');
        Schema::dropIfExists('slot_occurence_monitors');
        Schema::dropIfExists('slot_occurence_attendees');
        Schema::dropIfExists('slot_occurences');
        Schema::dropIfExists('slot_groups');
        Schema::dropIfExists('slots');
        Schema::dropIfExists('clubs');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
