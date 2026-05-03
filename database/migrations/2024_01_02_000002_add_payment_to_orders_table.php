<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['tunai', 'qris'])->nullable()->after('status');
            $table->string('midtrans_order_id')->nullable()->after('payment_method');
            $table->string('qris_url')->nullable()->after('midtrans_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'midtrans_order_id', 'qris_url']);
        });
    }
};
