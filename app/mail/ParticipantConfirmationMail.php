<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Participant;

class ParticipantConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $participant;
    public $data;
    public $subjectText;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Participant $participant, array $data)
    {
        $this->participant = $participant;
        $this->data = $data;
        $this->subjectText = $data['subject'] ?? 'Konfirmasi Pendaftaran';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject($this->subjectText)
                    ->view('emails.participant-confirmation')
                    ->with([
                        'participant' => $this->participant,
                        'emailType' => $this->data['email_type'],
                        'customMessage' => $this->data['message'],
                        'sendQrcode' => $this->data['send_qrcode'] ?? true,
                        'subject' => $this->subjectText,
                        'eventName' => $this->participant->event->name ?? 'Event',
                        'transactionCode' => $this->participant->transaction_code,
                        'emailSentAt' => now()->format('d F Y H:i'),
                        'systemName' => config('app.name', 'Event Monitoring System')
                    ]);
    }
}