<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_activity_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('organization_unit');
            $table->string('sub_organization_unit')->nullable();
            $table->string('program_code')->nullable();
            $table->string('program_name');
            $table->string('activity_code')->nullable();
            $table->string('activity_name');
            $table->string('sub_activity_code');
            $table->string('sub_activity_name');
            $table->string('spm')->nullable();
            $table->string('service_type')->nullable();
            $table->string('funding_source')->nullable();
            $table->string('location')->nullable();
            $table->string('implementation_time')->nullable();
            $table->string('target_group')->nullable();
            $table->string('output')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['year', 'sub_activity_code']);
        });

        Schema::create('plan_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_activity_plan_id')->constrained()->cascadeOnDelete();
            $table->string('category');
            $table->string('indicator_before');
            $table->text('benchmark_before');
            $table->string('target_before');
            $table->string('indicator_after');
            $table->text('benchmark_after');
            $table->string('target_after');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('plan_budget_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_activity_plan_id')->constrained()->cascadeOnDelete();
            $table->string('account_code')->nullable();
            $table->text('description');
            $table->decimal('coefficient_before', 12, 2)->default(0);
            $table->string('unit_before')->nullable();
            $table->decimal('unit_price_before', 15, 2)->default(0);
            $table->decimal('tax_before', 15, 2)->default(0);
            $table->decimal('coefficient_after', 12, 2)->default(0);
            $table->string('unit_after')->nullable();
            $table->decimal('unit_price_after', 15, 2)->default(0);
            $table->decimal('tax_after', 15, 2)->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_budget_lines');
        Schema::dropIfExists('plan_indicators');
        Schema::dropIfExists('sub_activity_plans');
    }
};
