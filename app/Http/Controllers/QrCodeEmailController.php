<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;

class QrCodeEmailController extends Controller
{
    public function sendQrEmail($id)
{
    // Recuperar o utente pelo ID
    $asset = Asset::findOrFail($id);

    // Buscar o Encarregado de Educação com e-mail válido
    $responsavelEE = DB::table('responsaveis_utentes')
        ->join('responsaveis', 'responsaveis_utentes.responsavel_id', '=', 'responsaveis.id')
        ->where('responsaveis_utentes.utente_id', $id)
        ->where('responsaveis_utentes.tipo_responsavel', 'Encarregado de Educacao')
        ->whereNotNull('responsaveis.email')
        ->where('responsaveis.email', '!=', '')
        ->select('responsaveis.nome_completo', 'responsaveis.email')
        ->first();

    // Se não houver Encarregado de Educação com e-mail válido, não enviar
    if (!$responsavelEE || !filter_var($responsavelEE->email, FILTER_VALIDATE_EMAIL)) {
        return redirect()->back()->with('error', 'Nenhum Encarregado de Educação com e-mail válido encontrado.');
    }

    // Buscar todos os responsáveis relacionados (para exibição na lista do e-mail)
    $responsaveis = DB::table('responsaveis_utentes')
        ->join('responsaveis', 'responsaveis_utentes.responsavel_id', '=', 'responsaveis.id')
        ->where('responsaveis_utentes.utente_id', $id)
        ->select(
            'responsaveis.nome_completo as name',
            'responsaveis.foto as image', // Corrigido para garantir compatibilidade com o template
            'responsaveis_utentes.tipo_responsavel',
            'responsaveis_utentes.grau_parentesco',
            'responsaveis_utentes.estado_autorizacao',
            'responsaveis_utentes.data_inicio_autorizacao',
            'responsaveis_utentes.data_fim_autorizacao'

        )
        ->get();

    // Gerar QR Code e guardar no diretório público
    $fileName = "qrcode_{$asset->id}.png";
    $filePath = public_path("qr_codes/{$fileName}");
    $publicUrl = asset("qr_codes/{$fileName}");

    if (!file_exists(public_path('qr_codes'))) {
        mkdir(public_path('qr_codes'), 0777, true);
    }

    QrCode::format('png')->size(200)->margin(10)->generate(
        "https://parque-seguro.jf-parquedasnacoes.pt:8126/confirmar-check/{$asset->id}",
        $filePath
    );

    // Registrar tentativa de envio no log
    $logId = DB::table('email_logs')->insertGetId([
        'email' => $responsavelEE->email,
        'subject' => 'QR Code para ' . $asset->name,
        'body' => 'Tentando enviar QR Code.',
        'status' => 'pending',
        'sent_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Enviar o e-mail
    try {
        Mail::send('emails.utenteQrCode', compact('asset', 'publicUrl', 'responsaveis'), function ($message) use ($responsavelEE, $filePath, $asset) {
            $message->to($responsavelEE->email)
                ->subject('QR Code para ' . $asset->name)
                ->attach($filePath, [
                    'as' => 'qrcode.png',
                    'mime' => 'image/png',
                ]);
        });

        // Atualizar status no log de e-mails para "success"
        DB::table('email_logs')->where('id', $logId)->update([
            'body' => 'QR Code enviado com sucesso.',
            'status' => 'success',
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'QR Code enviado com sucesso para ' . $responsavelEE->email);
    } catch (\Exception $e) {
        // Atualizar status no log de e-mails para "failed"
        DB::table('email_logs')->where('id', $logId)->update([
            'body' => 'Erro ao enviar: ' . $e->getMessage(),
            'status' => 'failed',
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('error', 'Erro ao enviar o e-mail: ' . $e->getMessage());
    }
}



    public function sendBulkEmails()
{
    // Buscar IDs dos utentes que têm um Encarregado de Educação com e-mail válido
    $utenteIds = DB::table('responsaveis_utentes')
        ->join('responsaveis', 'responsaveis_utentes.responsavel_id', '=', 'responsaveis.id')
        ->where('responsaveis_utentes.tipo_responsavel', 'Encarregado de Educacao')
        ->whereNotNull('responsaveis.email')
        ->where('responsaveis.email', '!=', '')
        ->pluck('responsaveis_utentes.utente_id') // Retorna apenas os IDs dos utentes
        ->unique();

    // Buscar os modelos `Asset` correspondentes
    $assets = Asset::whereIn('id', $utenteIds)->get();

    foreach ($assets as $asset) {
        dispatch(new \App\Jobs\SendQrCodeEmailJob($asset)); // Agora passamos um modelo `Asset`, não um `stdClass`
    }

    return redirect()->back()->with('success', 'Os e-mails de QR Codes estão a ser enviados em segundo plano.');
}



public function sendFilteredEmails(Request $request)
{
    // 1. Validar que o campo 'ids' foi realmente enviado
    $request->validate([
        'ids' => 'required|string',
    ]);

    // 2. Ler o campo 'ids' do request e convertê-lo num array
    // A função explode() divide a string "1,2,3" no array [1, 2, 3]
    $ids = explode(',', $request->input('ids'));

    // 3. Verificar se o array de IDs não está vazio
    if (empty($ids) || (count($ids) === 1 && $ids[0] === '')) {
        return redirect()->back()->with('error', 'Nenhum utente foi selecionado.');
    }

    // 4. Buscar os utentes com base nos IDs recebidos
    $assets = Asset::whereIn('id', $ids)->get();

    // 5. Enviar para a fila de trabalhos
    foreach ($assets as $asset) {
        dispatch(new \App\Jobs\SendQrCodeEmailJob($asset));
    }

    return redirect()->back()->with('success', 'Os e-mails para os utentes selecionados estão a ser enviados em segundo plano.');
}



}
