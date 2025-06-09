<?php

namespace App\Mail;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProposalSent extends Mailable {
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   */
  public function __construct(
    public Proposal $proposal,
    public string $token,
  ) {
  }

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope {
    return new Envelope(
      subject: 'Your Proposal from ' . config('app.name'),
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content {
    return new Content(
      markdown: 'emails.proposals.sent',
      with: [
        'proposalUrl' => route('proposals.show', ['proposal' => $this->proposal->id, 'token' => $this->token]),
        'proposalTitle' => $this->proposal->title,
        'clientName' => $this->proposal->client->company_name,
      ],
    );
  }

  /**
   * Get the attachments for the message.
   *
   * @return array<int, \Illuminate\Mail\Mailables\Attachment>
   */
  public function attachments(): array {
    return [];
  }
}
