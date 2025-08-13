<?php

namespace App\Mail;

use App\Models\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CartaoUtenteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $asset;
    public $data;
    public $type;

    public function __construct(Asset $asset, $data, $type = 'pdf')
    {
        $this->asset = $asset;
        $this->data = $data;
        $this->type = $type;
    }

    public function build()
    {
        $fileName = str_replace([' ', '.', ','], '_', $this->asset->nome_apelido ?? $this->asset->name);
        $fileName = preg_replace('/[^A-Za-z0-9_-]/', '', $fileName);
        $fileName = "cartão_{$fileName}.pdf"; 
        $mime = $this->type === 'png' ? 'image/png' : 'application/pdf';

        return $this->subject("Cartão de Utente - Parque Seguro para {$this->asset->name}")
                    ->view('emails.cartao')
                    ->attachData($this->data, $fileName, ['mime' => $mime]);
                    
    }
}