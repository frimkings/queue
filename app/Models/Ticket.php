<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'patient_name',
        'patient_phone',
        'department_id',
        'counter_id',
        'doctor_station_id',
        'priority',
        'insurance_type',
        'nhis_number',
        'vitals_bp',
        'vitals_temp',
        'vitals_pulse',
        'vitals_weight',
        'satisfaction_rating',
        'feedback_note',
        'status',
        'clinical_notes',
        'called_at',
        'served_at',
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'served_at' => 'datetime',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class);
    }

    public function doctorStation(): BelongsTo
    {
        return $this->belongsTo(DoctorStation::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(TicketStage::class)->orderBy('order');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(QueueActivity::class);
    }
}
