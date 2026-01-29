<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Participant;

class PaymentVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $participant;

    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
    }

    public function build()
    {
        return $this->subject('Verifikasi Pembayaran Event - ' . $this->participant->event->name)
            ->view('emails.payment_verified')
            ->with([
                'participant' => $this->participant
            ]);
    }
}