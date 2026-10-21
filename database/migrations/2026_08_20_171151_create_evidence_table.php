<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('evidence', function (Blueprint $table) {
        $table->id();

        $table->foreignId('device_id')
            ->constrained('devices')
            ->cascadeOnDelete();

        $table->string('file_path');
        $table->string('file_name');
        $table->string('file_type')->nullable();

        $table->string('evidence_type')->default('condition');

        $table->text('description')->nullable();

        $table->string('file_hash', 64)->nullable();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('evidence');
}
};
