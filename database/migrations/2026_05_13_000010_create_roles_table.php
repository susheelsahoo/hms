<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->comment('Stable authorization key.');
            $table->text('description')->nullable();
            $table->timestampsTz();
        });

        DB::table('roles')->insert([
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'description' => 'Platform administrator with access to all organizations and hotels.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotel Admin',
                'slug' => 'hotel_admin',
                'description' => 'Organization administrator with access to assigned hotels.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotel Manager',
                'slug' => 'hotel_manager',
                'description' => 'Operational manager for assigned hotel workflows.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
