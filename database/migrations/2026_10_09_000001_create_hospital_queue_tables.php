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
        // 1. Departments
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('prefix', 10);
            $table->integer('avg_service_time')->default(15); // in minutes
            $table->integer('capacity')->default(30);
            $table->string('status')->default('open'); // open, closed
            $table->string('color')->default('bg-blue-600');
            $table->integer('last_ticket_number')->default(0);
            $table->timestamps();
        });

        // 2. Counters / Windows / Booths
        Schema::create('counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->string('name'); // e.g. Room 101, Window 1, Booth A
            $table->string('status')->default('active'); // active, break, closed
            $table->timestamps();
        });

        // 3. Doctor Consultation Stations
        Schema::create('doctor_stations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->string('name'); // Dr. Kwesi Mensah
            $table->string('room'); // Consultation Room 101
            $table->string('specialty')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Tickets
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->index(); // e.g. OPD-059
            $table->string('patient_name');
            $table->string('patient_phone')->nullable();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('counter_id')->nullable()->constrained('counters')->onDelete('set null');
            $table->foreignId('doctor_station_id')->nullable()->constrained('doctor_stations')->onDelete('set null');
            $table->string('priority')->default('normal'); // normal, priority, emergency
            $table->string('status')->default('waiting'); // waiting, called, serving, served, skipped, cancelled
            $table->text('clinical_notes')->nullable();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamps();
        });

        // 5. Multi-Stage Visit Journey
        Schema::create('ticket_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->integer('order')->default(1);
            $table->string('status')->default('pending'); // pending, active, completed, skipped
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 6. Activity Log Stream
        Schema::create('queue_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->onDelete('cascade');
            $table->string('type'); // issued, called, served, transfer, skipped
            $table->string('text');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->string('station_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_activities');
        Schema::dropIfExists('ticket_stages');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('doctor_stations');
        Schema::dropIfExists('counters');
        Schema::dropIfExists('departments');
    }
};
