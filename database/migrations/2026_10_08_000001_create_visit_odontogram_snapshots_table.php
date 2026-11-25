<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_odontogram_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('teeth');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_odontogram_snapshots');
    }
};
