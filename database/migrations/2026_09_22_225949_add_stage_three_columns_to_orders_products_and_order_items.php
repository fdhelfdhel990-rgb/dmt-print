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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock_on_hand')->default(0)->after('minimum_order');
            $table->integer('stock_minimum')->default(0)->after('stock_on_hand');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_final_price')->nullable()->after('unit_estimate');
            $table->unsignedBigInteger('final_subtotal')->nullable()->after('subtotal');
            $table->timestamp('stock_deducted_at')->nullable()->after('final_subtotal');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('final_priced_at')->nullable()->after('final_total');
            $table->timestamp('revision_requested_at')->nullable()->after('customer_approved_at');
            $table->timestamp('production_started_at')->nullable()->after('assigned_admin_id');
            $table->timestamp('ready_at')->nullable()->after('production_started_at');
            $table->timestamp('completed_at')->nullable()->after('ready_at');
            $table->timestamp('cancelled_at')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['final_priced_at', 'revision_requested_at', 'production_started_at', 'ready_at', 'completed_at', 'cancelled_at']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['unit_final_price', 'final_subtotal', 'stock_deducted_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock_on_hand', 'stock_minimum']);
        });
    }
};
