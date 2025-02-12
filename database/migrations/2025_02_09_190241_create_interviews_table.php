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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('recruiter_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('application_id')->constrained('application')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('interview_date');
            $table->time('interview_time');
            $table->string('location');
            $table->enum('status', [0, 1])->default(0);
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
