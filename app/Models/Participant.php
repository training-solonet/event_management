<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'event_id',
        'full_name',
        'email',
        'phone',
        'gender',
        'nik',
        'address',
        'payment_method',
        'payment_status',
        'payment_proof',
        'notes',
        'wa_notification_sent',
        'email_notification_sent',
        'email_sent_at',
        'email_notes'
    ];

    protected $casts = [
        'wa_notification_sent' => 'boolean',
        'email_notification_sent' => 'boolean',
        'email_sent_at' => 'datetime'
    ];

    // Add this accessor for the view
    protected $appends = ['payment_proof_url'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function getStatusBadgeAttribute()
    {
        switch ($this->payment_status) {
            case 'verified':
                return '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Terverifikasi</span>';
            case 'paid':
                return '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Sudah Bayar</span>';
            default:
                return '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Menunggu</span>';
        }
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Bayar',
            'verified' => 'Terverifikasi'
        ];
        
        return $statuses[$this->payment_status] ?? $this->payment_status;
    }

    // PERBAIKAN: Helper method untuk mendapatkan URL bukti pembayaran yang benar
    public function getPaymentProofUrlAttribute()
    {
        if ($this->payment_proof) {
            // Cek apakah payment_proof sudah mengandung path lengkap
            if (strpos($this->payment_proof, 'payment_proofs/') === 0) {
                return Storage::url($this->payment_proof);
            }
            
            // Jika hanya nama file, tambahkan path payment_proofs/
            return Storage::url('payment_proofs/' . $this->payment_proof);
        }
        return null;
    }

    // Check if email was sent
    public function getEmailSentAttribute()
    {
        return $this->email_notification_sent && $this->email_sent_at;
    }
}