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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade'); // If delete company, this post will also delete
            $table->foreignId('workspace_id')->constrained('workspaces');
            $table->foreignId('location_id')->constrained('locations');
            $table->foreignId('employment_type_id')->constrained('employment_type'); // Job type
            $table->string('title', 200);
            $table->text('description');
            $table->text('requirement')->nullable();
            $table->text('facilities')->nullable();
            $table->date('deadline');
            $table->enum('status', ['active','inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
