<?php

namespace PnDev\ContactForm\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $data
    ) {}

    public function envelope(): Envelope
    {
        $siteName = config('contact-form.site_name', config('app.name'));
        $prefix = config('contact-form.email_subject_prefix', "[$siteName]");
        $sujet = $this->data['sujet_label'] ?? 'Contact';
        $nom = $this->data['nom'] ?? 'Inconnu';

        return new Envelope(
            to: [config('contact-form.recipient_email')],
            replyTo: [$this->data['email']],
            subject: "$prefix Nouveau message de $nom — $sujet",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'contact-form::email',
            with: [
                'contactData'  => $this->data,
                'siteName'     => config('contact-form.site_name', config('app.name')),
                'headerColor'  => config('contact-form.email_header_color', '#1d4ed8'),
            ],
        );
    }
}
