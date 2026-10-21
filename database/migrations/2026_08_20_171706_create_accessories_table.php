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
    Schema::create('accessories', function (Blueprint $table) {
        $table->id();

        $table->foreignId('device_id')
            ->constrained('devices')
            ->cascadeOnDelete();

        $table->string('name');
        $table->string('condition')->nullable();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('accessories');
}
};
