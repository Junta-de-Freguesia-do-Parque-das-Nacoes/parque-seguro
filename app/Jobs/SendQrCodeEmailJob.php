<?php

namespace App\Jobs;

use App\Models\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;

class SendQrCodeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $asset;

    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    public function handle()
{
    $asset = $this->asset;
    \Log::info("🔍 Buscando responsáveis para o utente ID: " . $asset->id);

    // Buscar todos os responsáveis do utente
    $responsaveis = DB::table('responsaveis_utentes')
        ->join('responsaveis', 'responsaveis_utentes.responsavel_id', '=', 'responsaveis.id')
        ->where('responsaveis_utentes.utente_id', $asset->id)
        ->select(
            'responsaveis.id',
            'responsaveis.nome_completo as name',
            'responsaveis.email',
            'responsaveis_utentes.grau_parentesco',
            'responsaveis_utentes.tipo_responsavel',
            'responsaveis.foto as image',
            'responsaveis_utentes.estado_autorizacao',
            'responsaveis_utentes.data_inicio_autorizacao',
            'responsaveis_utentes.data_fim_autorizacao'
        )
        ->get();

    if ($responsaveis->isEmpty()) {
        \Log::error("⚠️ Nenhum responsável encontrado para o utente ID: " . $asset->id);
        return;
    }

    // Buscar apenas o Encarregado de Educação que tenha e-mail válido
    $responsavelEE = $responsaveis->firstWhere('tipo_responsavel', 'Encarregado de Educacao');

    // **Se não houver Encarregado de Educação com e-mail válido, simplesmente retorna (sem log "Failed")**
    if (!$responsavelEE || empty($responsavelEE->email) || !filter_var($responsavelEE->email, FILTER_VALIDATE_EMAIL)) {
        return;
    }

    \Log::info("📨 Enviar QR Code para o Encarregado de Educação: " . $responsavelEE->email);

    // Gerar QR Code
    $fileName = "qrcode_{$asset->id}.png";
    $filePath = public_path("qr_codes/{$fileName}");
    $publicUrl = asset("qr_codes/{$fileName}");

    if (!file_exists(public_path('qr_codes'))) {
        mkdir(public_path('qr_codes'), 0777, true);
    }

    QrCode::format('png')->size(200)->margin(10)
        ->generate("https://parque-seguro.jf-parquedasnacoes.pt:8126/confirmar-check/{$asset->id}", $filePath);

    // **Registrar tentativa de envio no log de e-mails**
    $logId = DB::table('email_logs')->insertGetId([
        'email' => $responsavelEE->email,
        'subject' => 'QR Code para ' . $asset->name,
        'body' => 'Tentando enviar QR Code.',
        'status' => 'pending',
        'sent_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    try {
        // **Enviar o e-mail apenas para o Encarregado de Educação**
        Mail::send('emails.utenteQrCode', [
            'asset' => $asset,
            'publicUrl' => $publicUrl,
            'responsaveis' => $responsaveis, // Todos os responsáveis aparecem na lista do e-mail
        ], function ($message) use ($responsavelEE, $filePath, $asset) {
            $message->to($responsavelEE->email)
                ->subject('QR Code para ' . $asset->name)
                ->attach($filePath, [
                    'as' => 'qrcode.png',
                    'mime' => 'image/png',
                ]);
        });

        \Log::info("✅ QR Code enviado com sucesso para " . $responsavelEE->email);

        // **Atualizar status no log de e-mails**
        DB::table('email_logs')->where('id', $logId)->update([
            'body' => 'QR Code enviado com sucesso.',
            'status' => 'success',
            'updated_at' => now(),
        ]);

    } catch (\Exception $e) {
        \Log::error("❌ Erro ao enviar QR Code para " . $responsavelEE->email . ": " . $e->getMessage());

        // **Atualizar status para "Failed" no log de e-mails**
        DB::table('email_logs')->where('id', $logId)->update([
            'body' => 'Erro ao enviar: ' . $e->getMessage(),
            'status' => 'failed',
            'updated_at' => now(),
        ]);
    }
}
}