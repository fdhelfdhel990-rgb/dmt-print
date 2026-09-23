<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('description');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->string('image_path')->nullable()->after('description');
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->timestamp('archived_at')->nullable()->after('sort_order');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('cancelled_at');
            $table->foreignId('archived_by')->nullable()->after('archived_at')->constrained('users')->nullOnDelete();
        });

        Schema::table('cashbook_entries', function (Blueprint $table) {
            $table->date('entry_date')->nullable()->after('id');
            $table->string('source')->default('manual')->after('category');
            $table->string('payment_method')->nullable()->after('source');
            $table->timestamp('archived_at')->nullable()->after('reversed_at');
        });

        DB::table('cashbook_entries')->whereNotNull('payment_id')->update(['source' => 'automatic_legacy']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashbook_entries', function (Blueprint $table) {
            $table->dropColumn(['entry_date', 'source', 'payment_method', 'archived_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('archived_by');
            $table->dropColumn('archived_at');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['description', 'image_path', 'is_featured', 'archived_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
