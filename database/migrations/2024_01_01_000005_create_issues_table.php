<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('issue_categories')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('state');
            $table->string('city');
            $table->string('address')->nullable();
            $table->enum('urgency', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'fixed'])->default('pending');
            $table->foreignId('assigned_worker_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('estimated_fix_days')->nullable();
            $table->timestamp('reported_at')->useCurrent();
            $table->timestamp('fixed_at')->nullable();
            $table->integer('upvotes')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};

