<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('icon_key')->nullable();
            $table->integer('daily_goal')->nullable()->comment('times per day to make streak');
            $table->string('frequency')->default('daily');
            $table->time('daily_reminder_at')->nullable();
            $table->timestamp('last_completed_at')->nullable();
            $table->integer('streak_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
