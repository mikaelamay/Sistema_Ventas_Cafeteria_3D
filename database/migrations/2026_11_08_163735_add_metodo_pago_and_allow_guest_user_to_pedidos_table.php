<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });

        if (! Schema::hasColumn('pedidos', 'metodo_pago')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->string('metodo_pago')->default('efectivo')->after('estado');
            });
        }

        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Los pedidos de invitados no tienen usuario, por lo que no es posible
     * restaurar el NOT NULL sin perder información. En ese caso la migración
     * se omite de forma no destructiva y queda registrada en el log.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('pedidos', 'metodo_pago')) {
            return;
        }

        if (DB::table('pedidos')->whereNull('user_id')->exists()) {
            Log::warning(
                'No se revierte add_metodo_pago_and_allow_guest_user_to_pedidos_table: '
                .'existen pedidos sin usuario (invitados).'
            );

            return;
        }

        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn('metodo_pago');
        });

        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
    }
};
