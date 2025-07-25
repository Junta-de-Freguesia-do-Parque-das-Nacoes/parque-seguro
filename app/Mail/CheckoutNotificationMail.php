<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CheckoutNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $utente;
    public $responsavel;
    public $responsavelEE;
    public $manutencao;
    public $action;
    public $dataHoraAcao;
    public $grauParentesco;
    public $utilizadorBackoffice;

    public function __construct($utente, $responsavel, $responsavelEE, $manutencao, $action, $dataHoraAcao, $grauParentesco, $utilizadorBackoffice)
    {
        $this->utente = $utente;
        $this->responsavel = $responsavel;
        $this->responsavelEE = $responsavelEE;
        $this->manutencao = $manutencao;
        $this->action = $action;
        $this->dataHoraAcao = $dataHoraAcao;
        $this->grauParentesco = $grauParentesco;
        $this->utilizadorBackoffice = $utilizadorBackoffice;
    }

    public function build()
    {
        return $this->subject("Notificação de Saída - {$this->utente->name}")
                    ->view('emails.checkout_notification');
    }
}
