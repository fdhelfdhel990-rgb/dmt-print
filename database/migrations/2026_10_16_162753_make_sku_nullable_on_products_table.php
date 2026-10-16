<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make sku nullable on products table.
     *
     * SQLite does not support ALTER COLUMN, so we rebuild the table.
     * On other databases, we use the standard Laravel column change.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            // Rebuild the products table with sku as nullable
            DB::statement('PRAGMA foreign_keys = OFF');

            DB::statement('
                CREATE TABLE products_rebuild (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    category_id INTEGER NOT NULL REFERENCES categories(id),
                    name VARCHAR NOT NULL,
                    slug VARCHAR NOT NULL,
                    sku VARCHAR NULL,
                    short_description VARCHAR NULL,
                    description TEXT NULL,
                    base_price INTEGER UNSIGNED NOT NULL,
                    unit VARCHAR(50) NOT NULL,
                    minimum_order INTEGER UNSIGNED NOT NULL DEFAULT 1,
                    stock_on_hand INTEGER NOT NULL DEFAULT 0,
                    stock_minimum INTEGER NOT NULL DEFAULT 0,
                    production_estimate VARCHAR NULL,
                    tone VARCHAR(30) NOT NULL DEFAULT \'blue\',
                    tag VARCHAR(50) NULL,
                    is_active BOOLEAN NOT NULL DEFAULT 1,
                    is_featured BOOLEAN NOT NULL DEFAULT 0,
                    sort_order INTEGER UNSIGNED NOT NULL DEFAULT 0,
                    image_path VARCHAR NULL,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL
                )
            ');

            DB::statement('INSERT INTO products_rebuild SELECT * FROM products');
            DB::statement('DROP TABLE products');
            DB::statement('ALTER TABLE products_rebuild RENAME TO products');

            // Recreate indexes
            DB::statement('CREATE UNIQUE INDEX products_slug_unique ON products (slug)');
            DB::statement('CREATE UNIQUE INDEX products_sku_unique ON products (sku)');
            DB::statement('CREATE INDEX products_is_active_sort_order_index ON products (is_active, sort_order)');
            DB::statement('CREATE INDEX products_category_id_is_active_index ON products (category_id, is_active)');

            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            Schema::table('products', function ($table) {
                $table->string('sku')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting nullable sku is intentionally skipped as it may break existing data
    }
};
