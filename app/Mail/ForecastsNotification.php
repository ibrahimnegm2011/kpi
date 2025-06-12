<?php

namespace App\Mail;

use App\Models\Account;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ForecastsNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public int $year,
        public int $month,
        public array $accounts // [['account' => 'Account Name', 'count' => 5], ...]
    ) {}


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'KPIs Assigned for ' . now()->create()->month($this->month)->format('F') . " {$this->year}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.forecast_notification',
            with: [
                'user' => $this->user,
                'year' => $this->year,
                'month' => $this->month,
                'accounts' => $this->accounts,
            ]

        );
    }
}
