<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QRCodesUtentesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $responsavel;
    public $anexos;
    public $utentes;

    public function __construct($responsavel, $anexos, $utentes)
    {
        $this->responsavel = $responsavel;
        $this->anexos = $anexos;
        $this->utentes = $utentes;
    }

    public function build()
    {
        // 1. Extrair os nomes dos educandos
        $nomesEducandos = collect($this->utentes)->pluck('model.name')->filter()->unique()->implode(', ');

        // 2. Construir o assunto dinamicamente
        $assunto = 'QR Codes para Recolha';
        if (!empty($nomesEducandos)) {
            $assunto .= ' - ' . $nomesEducandos;
        }

        $email = $this->subject($assunto) // Usar o assunto dinâmico aqui
             ->view('emails.qrcodes-html', [
                 'responsavel' => $this->responsavel,
                 'utentes' => $this->utentes,
             ])
             ->text('emails.qrcodes-texto-plain');

        foreach ($this->anexos as $anexo) {
            $email->attachData($anexo['conteudo'], $anexo['nome'], [
                'mime' => $anexo['mime'],
            ]);
        }

        return $email;
    }
}