<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('sub_activity_plan_id')
                ->nullable()
                ->unique()
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['sub_activity_plan_id']);
            $table->dropUnique(['sub_activity_plan_id']);
            $table->dropColumn('sub_activity_plan_id');
        });
    }
};
