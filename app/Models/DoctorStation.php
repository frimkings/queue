<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorStation extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'room',
        'specialty',
        'is_active',
        'is_on_break',
        'break_reason',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_on_break' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
