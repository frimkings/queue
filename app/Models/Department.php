<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'prefix',
        'avg_service_time',
        'capacity',
        'status',
        'color',
        'last_ticket_number',
    ];

    public function counters(): HasMany
    {
        return $this->hasMany(Counter::class);
    }

    public function doctorStations(): HasMany
    {
        return $this->hasMany(DoctorStation::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function waitingTickets(): HasMany
    {
        return $this->hasMany(Ticket::class)->where('status', 'waiting');
    }

    public function generateNextTicketNumber(): string
    {
        $this->increment('last_ticket_number');
        $num = str_pad((string)$this->last_ticket_number, 3, '0', STR_PAD_LEFT);
        return "{$this->prefix}-{$num}";
    }
}
