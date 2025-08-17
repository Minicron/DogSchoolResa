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
        // Ajouter les champs manquants à la table users existante
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'firstname')) {
                $table->string('firstname')->after('name');
            }
            if (!Schema::hasColumn('users', 'club_id')) {
                $table->foreignId('club_id')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('club_id');
            }
            if (!Schema::hasColumn('users', 'invitation_token')) {
                $table->string('invitation_token')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(false)->after('invitation_token');
            }
            if (!Schema::hasColumn('users', 'calendar_view')) {
                $table->boolean('calendar_view')->default(false)->after('is_active');
            }
        });

        // Table clubs
        if (!Schema::hasTable('clubs')) {
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
        }

        // Ajouter la contrainte de clé étrangère pour club_id
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
        });

        // Table slots avec tous les champs
        if (!Schema::hasTable('slots')) {
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
        }

        // Table slot_groups
        if (!Schema::hasTable('slot_groups')) {
            Schema::create('slot_groups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('slot_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        // Table slot_occurences avec tous les champs
        if (!Schema::hasTable('slot_occurences')) {
            Schema::create('slot_occurences', function (Blueprint $table) {
                $table->id();
                $table->foreignId('slot_id')->constrained()->onDelete('cascade');
                $table->date('date');
                $table->boolean('is_cancelled')->default(false);
                $table->boolean('is_full')->default(false);
                $table->boolean('closing_notification_sent')->default(false);
                $table->timestamps();
            });
        }

        // Table slot_occurence_attendees
        if (!Schema::hasTable('slot_occurence_attendees')) {
            Schema::create('slot_occurence_attendees', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('slot_occurence_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }

        // Table slot_occurence_monitors
        if (!Schema::hasTable('slot_occurence_monitors')) {
            Schema::create('slot_occurence_monitors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('slot_occurence_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }

        // Table restricted_slot_whitelists
        if (!Schema::hasTable('restricted_slot_whitelists')) {
            Schema::create('restricted_slot_whitelists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('slot_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }

        // Table user_invitations
        if (!Schema::hasTable('user_invitations')) {
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
        }

        // Table slot_occurence_histos
        if (!Schema::hasTable('slot_occurence_histos')) {
            Schema::create('slot_occurence_histos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('slot_occurence_id')->constrained('slot_occurences')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('action');
                $table->text('details')->nullable();
                $table->timestamps();
            });
        }

        // Table slot_occurence_cancellations
        if (!Schema::hasTable('slot_occurence_cancellations')) {
            Schema::create('slot_occurence_cancellations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('slot_occurence_id')->constrained('slot_occurences')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('reason');
                $table->timestamps();
            });
        }

        // Table waiting_lists
        if (!Schema::hasTable('waiting_lists')) {
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
        
        // Supprimer les colonnes ajoutées à la table users
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
            $table->dropColumn(['firstname', 'club_id', 'role', 'invitation_token', 'is_active', 'calendar_view']);
        });
    }
};
