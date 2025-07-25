<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('assets', function (Blueprint $table) {
        $table->boolean('receive_checkin_notifications')->default(true); // Notificações de Check-in
        $table->boolean('receive_checkout_notifications')->default(true); // Notificações de Check-out
        $table->boolean('receive_self_notifications')->default(false); // Notificações feitas por si
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            //
        });
    }
};
