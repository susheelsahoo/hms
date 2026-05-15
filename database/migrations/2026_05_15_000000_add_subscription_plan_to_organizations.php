<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table): void {
            $table->foreignId('subscription_plan_id')
                ->nullable()
                ->constrained('subscription_plans')
                ->restrictOnDelete()
                ->comment('Default subscription plan for this organization.');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table): void {
            $table->dropForeignIdFor('subscription_plans', 'subscription_plan_id');
            $table->dropColumn('subscription_plan_id');
        });
    }
};
