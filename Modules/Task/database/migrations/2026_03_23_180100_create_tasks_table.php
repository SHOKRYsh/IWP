<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('icon_key')->nullable();
            $table->text('description')->nullable();
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->foreignId('life_element_id')->nullable()->constrained('life_elements')->onDelete('set null');
            $table->foreignId('life_task_type_id')->nullable()->constrained('life_task_types')->onDelete('set null');
            $table->datetime('due_date')->nullable();
            $table->datetime('reminder_at')->nullable();
            $table->json('extra_data')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
