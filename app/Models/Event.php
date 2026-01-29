<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_code',
        'name',
        'date',
        'price',
        'location',
        'type',
        'description',
        'is_active',
        'available_slots'
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'available_slots' => 'integer'
    ];

    protected $appends = ['participants_count', 'formatted_date'];

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function getParticipantsCountAttribute()
    {
        // Hitung semua peserta tanpa filter payment_status
        return $this->participants()->count();
    }

    public function getVerifiedParticipantsCountAttribute()
    {
        // Hitung peserta dengan status verified
        return $this->participants()->where('payment_status', 'verified')->count();
    }

    public function getPaidParticipantsCountAttribute()
    {
        // Hitung peserta dengan status paid
        return $this->participants()->where('payment_status', 'paid')->count();
    }

    public function getPendingParticipantsCountAttribute()
    {
        // Hitung peserta dengan status pending
        return $this->participants()->where('payment_status', 'pending')->count();
    }

    public function getFormattedDateAttribute()
    {
        return $this->date ? $this->date->format('d M Y') : null;
    }

    public function canRegister()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->available_slots === null) {
            return true;
        }

        return $this->participants_count < $this->available_slots;
    }

    public function isFull()
    {
        if ($this->available_slots === null) {
            return false;
        }

        return $this->participants_count >= $this->available_slots;
    }

    // Method baru untuk mengambil semua peserta dengan relasi lengkap
    public function getAllParticipants()
    {
        return $this->participants()
            ->orderBy('created_at', 'desc')
            ->get();
    }
}