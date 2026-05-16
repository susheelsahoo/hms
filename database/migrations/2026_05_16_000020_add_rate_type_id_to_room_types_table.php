<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_types', function (Blueprint $table): void {
            $table->foreignId('rate_type_id')
                ->nullable()
                ->after('hotel_id')
                ->constrained('rate_types')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('rate_type_id');
        });
    }
};

