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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('receptionist')->after('email'); // admin, doctor, receptionist, pharmacist, lab_tech, billing
            $table->foreignId('department_id')->nullable()->after('role')->constrained('departments')->onDelete('set null');
            $table->foreignId('doctor_station_id')->nullable()->after('department_id')->constrained('doctor_stations')->onDelete('set null');
            $table->foreignId('counter_id')->nullable()->after('doctor_station_id')->constrained('counters')->onDelete('set null');
            $table->string('status')->default('active')->after('counter_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['doctor_station_id']);
            $table->dropForeign(['counter_id']);
            $table->dropColumn(['role', 'department_id', 'doctor_station_id', 'counter_id', 'status']);
        });
    }
};
