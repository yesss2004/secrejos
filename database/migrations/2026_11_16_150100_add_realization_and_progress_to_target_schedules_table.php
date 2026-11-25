<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('target_schedules', function (Blueprint $table) {
            $table->text('realization')->nullable()->after('description');
            $table->unsignedTinyInteger('progress')->default(0)->after('priority');
        });
    }

    public function down(): void
    {
        Schema::table('target_schedules', function (Blueprint $table) {
            $table->dropColumn(['realization', 'progress']);
        });
    }
};
