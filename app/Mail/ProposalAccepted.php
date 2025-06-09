<?php

namespace App\Mail;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProposalAccepted extends Mailable {
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   */
  public function __construct(
    public Proposal $proposal,
  ) {
  }

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope {
    return new Envelope(
      subject: 'Congratulations! Your Proposal from ' . config('app.name') . ' has been accepted!',
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content {
    return new Content(
      view: 'emails.proposals.accepted',
      with: [
        'proposalUrl' => route('proposals.show', ['proposal' => $this->proposal->id]),
        'proposalTitle' => $this->proposal->title,
        'clientName' => $this->proposal->client->company_name,
        'managerName' => $this->proposal->project->manager->name
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
