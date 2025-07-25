<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CheckinNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $utente;
    public $responsavel;
    public $responsavelEE;
    public $manutencao;
    public $utilizadorBackoffice;
    public $action;
    public $dataHoraAcao;
    
    

    public function __construct($utente, $responsavel, $responsavelEE, $manutencao, $utilizadorBackoffice, $action, $dataHoraAcao)
    {
        $this->utente = $utente;
        $this->responsavel = $responsavel;
        $this->responsavelEE = $responsavelEE;
        $this->manutencao = $manutencao;
        $this->utilizadorBackoffice = $utilizadorBackoffice;
        $this->action = $action;
        $this->dataHoraAcao = $dataHoraAcao;
        
        
    }

    public function build()
    {
        return $this->subject("Notificação de Entrada - {$this->utente->name}")
                    ->view('emails.checkin_notification');
    }
}
