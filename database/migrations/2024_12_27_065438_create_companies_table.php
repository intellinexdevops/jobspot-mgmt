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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('industry_id')->constrained('industries');
            $table->foreignId('location_id')->constrained('locations');
            $table->string('profile')->nullable();
            $table->string('bio')->nullable();
            $table->string('employee_count')->nullable();
            $table->date('since');
            $table->string('website')->nullable();
            $table->bigInteger('follower')->nullable()->default(0);
            $table->text('gallery_images')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
