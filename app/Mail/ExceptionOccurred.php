<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExceptionOccurred extends Mailable
{
    use Queueable, SerializesModels;

    private array $content;

    /**
     * Create a new message instance.
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $emailsTo = config('exceptions.emailExceptionsTo', false) ?
            str_getcsv(config('exceptions.emailExceptionsTo')) :
            null;
        $emailsCc = config('exceptions.emailExceptionCCto', false) ?
            str_getcsv(config('exceptions.emailExceptionCCto')) :
            null;
        $emailsBcc = config('exceptions.emailExceptionBCCto', false) ?
            str_getcsv(config('exceptions.emailExceptionBCCto')) :
            null;
        $fromSender = config('exceptions.emailExceptionFrom');
        $subject = config('exceptions.emailExceptionSubject');

        return new Envelope(
            from: $fromSender,
            to: $emailsTo,
            cc: $emailsCc,
            bcc: $emailsBcc,
            subject: $subject
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view = config('exceptions.emailExceptionView');

        return new Content(
            view: $view,
            with: [
                'content' => $this->content,
            ]
        );
    }
}
