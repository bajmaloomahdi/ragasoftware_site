<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Polymorphic "feature / advantage / benefit" blocks, reusable across
     * products, services, pages and the homepage.
     */
    public function up(): void
    {
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->morphs('featureable');
            $table->string('kind', 20)->default('feature'); // feature | advantage | benefit
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['featureable_type', 'featureable_id', 'kind', 'sort_order'], 'features_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
