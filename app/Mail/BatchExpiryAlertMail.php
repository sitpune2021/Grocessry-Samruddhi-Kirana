<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BatchExpiryAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $batches;

    public function __construct($batches)
    {
        $this->batches = $batches;
    }

    public function build()
    {
        return $this->subject('⚠️ Product Batch Expiry Alert')
            ->view('emails.batch_expiry_alert');
    }
}
