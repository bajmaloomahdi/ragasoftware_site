<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 5)->default('fa');
            $table->uuid('translation_group')->nullable();
            $table->string('title');
            $table->string('slug');
            $table->string('client_name')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->text('summary')->nullable();
            $table->longText('body')->nullable();
            $table->foreignId('cover_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('project_url')->nullable();
            $table->date('completed_on')->nullable();
            $table->boolean('featured')->default(false);
            $table->string('status', 12)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['locale', 'slug']);
            $table->index(['status', 'published_at']);
            $table->index(['featured', 'sort_order']);
            $table->index('translation_group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
