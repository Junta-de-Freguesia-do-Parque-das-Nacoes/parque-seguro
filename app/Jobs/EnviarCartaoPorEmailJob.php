<?php

namespace App\Jobs;

use App\Mail\CartaoUtenteMail;
use App\Models\Asset;
use App\Models\CartaoEnviado;
use App\Models\Labels\CartaoparqueseguroMaiorEmail;
use App\Libraries\LabelsGenerator;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB; // Adicione esta linha
use Illuminate\Support\Facades\Log; // Adicione esta linha

class EnviarCartaoPorEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $utenteId;

    public function __construct($utenteId)
    {
        $this->utenteId = $utenteId;
    }

    public function handle()
    {
        $utente = Asset::with([
            'responsaveis' => function ($q) {
                $q->wherePivot('tipo_responsavel', 'Encarregado de Educação');
            }
        ])->find($this->utenteId);

        if (!$utente) {
            Log::error("❌ Erro no envio de cartão: Utente não encontrado. ID {$this->utenteId}");
            return;
        }

        $ee = $utente->responsaveis->first();

        if (!$ee || !filter_var($ee->email, FILTER_VALIDATE_EMAIL)) {
            Log::error("❌ Erro no envio de cartão: Encarregado de Educação inválido ou sem email. Utente ID {$this->utenteId}");
            return;
        }

        Log::info('Asset Data:', [
            'id' => $utente->id,
            'barcode2d' => $utente->barcode2d,
            'asset_tag' => $utente->asset_tag,
            'company' => $utente->company ? $utente->company->name : null,
        ]);

        $labelModel = new CartaoparqueseguroMaiorEmail();
        $generator = new LabelsGenerator($labelModel);
        $generator->addAsset($utente);
        $pdf = $generator->output('S');

        // Lógica de envio e registro de log
        try {
            Mail::to($ee->email)->send(new CartaoUtenteMail($utente, $pdf));

            // Registrar sucesso no log personalizado (assumindo que existe a tabela 'email_logs')
            DB::table('email_logs')->insert([
                'email' => $ee->email,
                'subject' => "Cartão de Utente - Parque Seguro para {$utente->name}",
                'body' => "O cartão do utente foi enviado com sucesso.",
                'status' => 'success',
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Registrar sucesso na tabela de controle de envio
            CartaoEnviado::create([
                'asset_id' => $utente->id,
                'responsavel_id' => $ee->id,
                'enviado_em' => Carbon::now(),
            ]);

        } catch (\Exception $e) {
            // Registrar erro no log do Laravel
            Log::error("❌ Erro ao enviar o e-mail do cartão para o utente {$utente->id}: " . $e->getMessage());
            
            // Registrar erro no log personalizado
            DB::table('email_logs')->insert([
                'email' => $ee->email,
                'subject' => "Cartão de Utente - Parque Seguro para {$utente->name}",
                'body' => "Erro ao enviar: " . $e->getMessage(),
                'status' => 'error',
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}