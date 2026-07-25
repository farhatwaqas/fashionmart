<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->unique();
            $table->string('legacy_id')->nullable()->unique();
            $table->longText('description')->nullable();
            $table->string('short_description', 500)->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('old_price', 12, 2)->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->boolean('featured')->default(false)->index();
            $table->boolean('hot_selling')->default(false)->index();
            $table->boolean('recommended')->default(false)->index();
            $table->string('status', 20)->default('active')->index(); // active, draft, archived
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->index(['status', 'featured']);
            $table->index(['status', 'hot_selling']);
            $table->index(['status', 'recommended']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
