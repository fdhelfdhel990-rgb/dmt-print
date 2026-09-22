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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->string('public_token', 64)->unique();
            $table->string('status')->default('pending_review')->index();
            $table->unsignedBigInteger('estimated_subtotal');
            $table->unsignedBigInteger('shipping_cost')->default(0);
            $table->unsignedBigInteger('estimated_total');
            $table->unsignedBigInteger('final_total')->nullable();
            $table->unsignedBigInteger('amount_paid')->default(0);
            $table->string('payment_scheme')->nullable();
            $table->string('fulfillment_method');
            $table->text('shipping_address')->nullable();
            $table->string('shipping_region')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('address_note')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('customer_note')->nullable();
            $table->text('internal_note')->nullable();
            $table->timestamp('customer_approved_at')->nullable();
            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
