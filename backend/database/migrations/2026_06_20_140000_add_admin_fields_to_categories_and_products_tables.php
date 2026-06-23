<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'name')) {
                $table->string('name')->after('id');
            }

            if (! Schema::hasColumn('categories', 'dec')) {
                $table->text('dec')->after('name');
            }

            if (! Schema::hasColumn('categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('dec');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')->after('id')->constrained()->cascadeOnDelete();
            }

            if (! Schema::hasColumn('products', 'name')) {
                $table->string('name')->after('category_id');
            }

            if (! Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable()->after('name');
            }

            if (! Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 10, 2)->after('image');
            }

            if (! Schema::hasColumn('products', 'qty')) {
                $table->unsignedInteger('qty')->default(0)->after('price');
            }

            if (! Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('qty');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }

            foreach (['name', 'image', 'price', 'qty', 'is_active'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            foreach (['name', 'dec', 'is_active'] as $column) {
                if (Schema::hasColumn('categories', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
