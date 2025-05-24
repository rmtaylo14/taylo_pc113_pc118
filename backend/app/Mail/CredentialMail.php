<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CredentialMail extends Mailable
{
    use Queueable, SerializesModels;


    public $firstname;

    /**
     * Create a new message instance.
     */
    public function __construct($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Welcome to the App')
                    ->view('emails.mail')
                    ->with([
                        'firstname' => $this->firstname
                    ]);
    }
    

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Credential Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         markdown: 'mail',
    //         with: [
    //             'id' => $this->id,
    //             'firstname' => $this->firstname,
    //         ],
            
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
