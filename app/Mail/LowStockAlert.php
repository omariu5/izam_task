<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockAlert extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public array $data
    ) {}

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->markdown('emails.low-stock-alert')
                    ->subject('Low Stock Alert - ' . $this->data['item']);
    }
}
