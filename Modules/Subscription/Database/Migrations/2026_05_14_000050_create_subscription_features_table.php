<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_features', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscription_plan_id')->constrained()->cascadeOnDelete();
            $table->string('feature_key', 100)->comment('Feature identifier (e.g., advanced_reporting).');
            $table->string('feature_name', 100)->comment('Human-readable feature name.');
            $table->text('description')->nullable();
            $table->boolean('is_included')->default(true)->comment('Feature included in this plan?');
            $table->jsonb('limits')->default('{}')->comment('Feature-specific limits.');
            $table->jsonb('metadata')->default('{}')->comment('Additional feature metadata.');
            $table->timestampsTz();

            $table->unique(['subscription_plan_id', 'feature_key']);
            $table->index(['subscription_plan_id', 'is_included'], 'sfeat_plan_included_idx');
            $table->index('feature_key', 'sfeat_key_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_features');
    }
};
