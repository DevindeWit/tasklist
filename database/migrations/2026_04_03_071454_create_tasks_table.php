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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignee_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['todo', 'doing', 'done', 'blocked']);
            $table->enum('priority', ['low', 'normal', 'high']);
            $table->date('due_date')->nullable();
            $table->integer('estimate_minutes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
