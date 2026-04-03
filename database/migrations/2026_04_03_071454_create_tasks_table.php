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
            $table->id('project_id');
            $table->id('assignee_id');
            $table->timestamps();
            $table->string('title');
            $table->string('description');
            $table->enum('status', ['todo', 'doing', 'done', 'blocked']);
            $table->enum('priority', ['low', 'normal', 'high']);
            $table->date('due_date')->nullable();
            $table->integer('estimate_minutes')->nullable();
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
