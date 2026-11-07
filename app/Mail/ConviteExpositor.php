<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ConviteExpositor extends Mailable
{
    public function __construct(public User $user, public string $link)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Seu acesso ao 9º Festival de Gastronomia e Cultura do Aracati');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.convite',
            with: [
                'nome' => $this->user->name,
                'link' => $this->link,
                'dias' => (int) config('festival.convite_dias', 14),
                'stands' => $this->user->stands()->pluck('nome')->all(),
            ],
        );
    }
}
