<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use App\Models\Invitation; // Import the Invitation model
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL; // Import URL facade for signed routes

// Implement ShouldQueue for better performance
class EmployeeInvitationMail extends Mailable implements ShouldQueue {
  use Queueable, SerializesModels;

  /**
   * The invitation instance.
   *
   * @var \App\Models\Invitation
   */
  public $invitation;

  /**
   * Create a new message instance.
   *
   * @param \App\Models\Invitation $invitation
   * @return void
   */
  public function __construct(Invitation $invitation) {
    $this->invitation = $invitation;
  }

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope {
    // Dynamically set the subject using the organization name
    return new Envelope(
      subject: 'You are invited to join ' . $this->invitation->organization->name,
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content {
    // Generate a temporary signed URL for registration
    // This adds security by making the link valid only for a limited time
    // and ensures it hasn't been tampered with.
    // Assumes you have a route named 'register'. Adjust if needed.
    $registrationUrl = URL::temporarySignedRoute(
        'register', // Route name for registration page
        now()->addDays(7), // Make the link valid for the same duration as the token
        ['token' => $this->invitation->token] // Pass the token as a parameter
    );

    return new Content(
      view: 'emails.employee_invitation', // Use a Blade view instead of Markdown
      with: [
          'invitation' => $this->invitation,
          'registrationUrl' => $registrationUrl, // Pass the URL to the view
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
