<?php

namespace App\Mail;

use Elegantly\Invoices\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceSent extends Mailable {
  use Queueable, SerializesModels;

  /**
   * The invoice instance.
   */
  public Invoice $invoice;

  /**
   * Create a new message instance.
   */
  public function __construct(Invoice $invoice) {
    $this->invoice = $invoice;
  }

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope {
    return new Envelope(
      subject: 'Invoice #' . $this->invoice->serial_number . ' from ' . ($this->project->organization->name ?? config('app.name')),
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content {
    return new Content(
      markdown: 'emails.invoices.sent',
      with: [
        'invoice' => $this->invoice,
      ],
    );
  }

  /**
   * Get the attachments for the message.
   *
   * @return array<int, \Illuminate\Mail\Mailables\Attachment>
   */
  public function attachments(): array {
    return [
      $this->invoice->toMailAttachment()
    ];
  }
}
