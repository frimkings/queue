<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('insurance_type')->default('NHIS')->after('priority'); // NHIS, Cash/Self-Pay, Acacia, Enterprise, Glico
            $table->string('nhis_number')->nullable()->after('insurance_type');
            $table->string('vitals_bp')->nullable()->after('nhis_number'); // e.g. 120/80
            $table->string('vitals_temp')->nullable()->after('vitals_bp'); // e.g. 36.8
            $table->string('vitals_pulse')->nullable()->after('vitals_temp'); // e.g. 72
            $table->string('vitals_weight')->nullable()->after('vitals_pulse'); // e.g. 68kg
            $table->tinyInteger('satisfaction_rating')->nullable()->after('served_at'); // 1 - 5
            $table->text('feedback_note')->nullable()->after('satisfaction_rating');
        });

        Schema::table('doctor_stations', function (Blueprint $table) {
            $table->boolean('is_on_break')->default(false)->after('is_active');
            $table->string('break_reason')->nullable()->after('is_on_break');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'insurance_type',
                'nhis_number',
                'vitals_bp',
                'vitals_temp',
                'vitals_pulse',
                'vitals_weight',
                'satisfaction_rating',
                'feedback_note',
            ]);
        });

        Schema::table('doctor_stations', function (Blueprint $table) {
            $table->dropColumn(['is_on_break', 'break_reason']);
        });
    }
};
