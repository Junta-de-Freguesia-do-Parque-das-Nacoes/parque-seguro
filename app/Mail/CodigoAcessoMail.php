<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CodigoAcessoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $codigo; // Variável para passar o código para a view

    /**
     * Create a new message instance.
     */
    public function __construct($codigo)
    {
        $this->codigo = $codigo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Aponta para a sua view e define o assunto
        return $this->subject('Seu Código de Acesso')
                    ->view('emails.verificationCode');
    }
}