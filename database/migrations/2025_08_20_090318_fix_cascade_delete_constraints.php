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
        // Supprimer les contraintes dangereuses
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
        });

        // Recréer les contraintes avec des règles de suppression sécurisées
        Schema::table('clubs', function (Blueprint $table) {
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('restrict');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer les nouvelles contraintes
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
        });

        // Remettre les anciennes contraintes (dangereuses)
        Schema::table('clubs', function (Blueprint $table) {
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
        });
    }
};
