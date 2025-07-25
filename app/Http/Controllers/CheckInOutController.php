<?php

namespace App\Http\Controllers;

use App\Mail\CheckoutDirectNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use Illuminate\Support\Facades\DB;
use App\Models\EmailLog;
use App\Mail\CheckoutNotificationMail;
use App\Mail\CheckinNotificationMail;
use Carbon\Carbon;
use App\Models\Responsavel;


class CheckInOutController extends Controller
{
    const STATUS_CHECKED_IN = 'Checked In';
    const STATUS_CHECKED_OUT = 'Checked Out';

    public function showUtente($id)
    {
        $utente = Asset::find($id);
    
        if ($utente) {
            $dataAtual = now()->toDateString();
            $manutencao = AssetMaintenance::where('asset_id', $utente->id)
                ->whereDate('created_at', $dataAtual)
                ->first();
    
            $action = $utente->status === self::STATUS_CHECKED_IN ? 'Check-out' : 'Check-in';
    
            // Carregar os responsáveis com o estado "Autorizado"
            $responsaveis = $utente->responsaveis()
                ->wherePivot('estado_autorizacao', 'Autorizado')
                ->where(function ($query) use ($dataAtual) {
                    $query->whereDate('responsaveis_utentes.data_fim_autorizacao', '>=', $dataAtual)
                          ->orWhereNull('responsaveis_utentes.data_fim_autorizacao');
                })
                ->get();
    
            // 📌 Buscar programas ativos (licenças)
            $programas = DB::table('license_seats')
    ->join('licenses', 'license_seats.license_id', '=', 'licenses.id')
    ->where('license_seats.asset_id', $utente->id)
    ->whereNull('license_seats.deleted_at')
    ->whereNotNull('licenses.purchase_date')
    ->whereDate('licenses.purchase_date', '<=', $dataAtual)
    ->where(function ($query) use ($dataAtual) {
        $query->whereNull('licenses.termination_date')
              ->orWhereDate('licenses.termination_date', '>=', $dataAtual);
    })
    ->select('licenses.name', 'licenses.termination_date', 'licenses.purchase_date') // ⬅️ adiciona aqui
    ->get();

    
            // 🧪 Log para garantir que vem algo (podes remover depois)
            \Log::debug('Programas encontrados:', $programas->toArray());
    
            return view('confirmar-check', compact('utente', 'action', 'manutencao', 'responsaveis', 'programas'));
        }
    
        return redirect()->back()->withErrors('Utente não encontrado.');
    }
    
    
    
    



private function traduzirAcao($acao)
{
    return match (strtolower($acao)) {
        'checkin' => 'Entrada',
        'checkout' => 'Saída',
        default => ucfirst($acao), // Caso algum termo diferente seja usado
    };
}

public function checkIn(Request $request, $id)
{
    // Buscar o utente
    $utente = Asset::findOrFail($id);
    $utilizadorBackoffice = auth()->user();

    \Log::info("📌 Iniciando check-in para utente ID: {$utente->id}, realizado por {$utilizadorBackoffice->name}");

    // Atualizar status do utente
    $utente->update([
        'status' => self::STATUS_CHECKED_IN,
        'status_id' => 23, // Em Atividade
    ]);
    \Log::info("🔄 Status atualizado para 'Entrada' - Utente ID: {$utente->id}");

    // Log de ação
    DB::table('action_logs')->insert([
        'user_id' => $utilizadorBackoffice->id,
        'action_type' => 'checkin',
        'target_id' => $utente->id,
        'target_type' => 'asset',
        'note' => "Entrada realizada.",
        'action_source' => 'qr-public',
        'item_type' => 'asset',
        'item_id' => $utente->id,
        'created_at' => now(),
        'updated_at' => now(),
        'action_date' => now(),
    ]);
    \Log::info("📝 Log de ação inserido no banco de dados - Utente ID: {$utente->id}");

    // Verificar manutenção associada
    $manutencao = AssetMaintenance::where('asset_id', $utente->id)
        ->whereDate('created_at', now()->toDateString())
        ->first();

    if ($manutencao) {
        \Log::info("🔍 Incidente encontrada para utente ID: {$utente->id}");
    } else {
        \Log::info("❗ Nenhuma incidente encontrado para utente ID: {$utente->id}");
    }

    // Buscar o Encarregado de Educação para notificação
    $responsavelEE = DB::table('responsaveis_utentes')
        ->join('responsaveis', 'responsaveis_utentes.responsavel_id', '=', 'responsaveis.id')
        ->where('responsaveis_utentes.utente_id', $utente->id)
        ->where('responsaveis_utentes.tipo_responsavel', 'Encarregado de Educacao')
        ->whereNotNull('responsaveis.email')
        ->whereRaw("LENGTH(responsaveis.email) > 5")
        ->where('responsaveis_utentes.estado_autorizacao', 'Autorizado')
        ->select('responsaveis.id', 'responsaveis.nome_completo as name', 'responsaveis.email')
        ->first();

    // Se não encontrar o Encarregado de Educação
    if (!$responsavelEE) {
        \Log::warning("⚠️ Nenhum Encarregado de Educação encontrado para o utente ID: {$utente->id}");
    } else {
        \Log::info("📩 Notificação será enviada para o Encarregado de Educação: {$responsavelEE->email}");
    }

    // Verifica as preferências de notificação na tabela 'assets'
    $deveReceberNotificacao = false;

    // Verifica se o Encarregado de Educação ativou a preferência de receber notificações de check-in
    $deveReceberNotificacao = false;

if ($utente->receive_checkin_notifications) {
    if (!empty($responsavelEE)) {
        // Se for o próprio EE a fazer o check-in
        if ($utilizadorBackoffice->id === $responsavelEE->id) {
            $deveReceberNotificacao = $utente->receive_self_notifications;
        } else {
            $deveReceberNotificacao = true;
        }
    } else {
        \Log::warning("⚠️ Preferência de notificação ativa, mas EE não encontrado — utente ID: {$utente->id}");
    }
}


    // Enviar e-mail de notificação para o Encarregado de Educação, se as preferências permitirem
    if ($deveReceberNotificacao && $responsavelEE) {
        \Log::info("📤 Preparando o envio do e-mail de notificação para o Encarregado de Educação.");

        try {
            // Enviar o e-mail para a fila
            Mail::to($responsavelEE->email)
                ->queue(new CheckinNotificationMail(
                    $utente,
                    null,  // Sem responsável
                    $responsavelEE, 
                    $manutencao, 
                    'Entrada',  // Ação em português
                    now(),  // Data e hora da ação
                    'Desconhecido',  // Grau de parentesco (sem responsável)
                    $utilizadorBackoffice
                ));

            \Log::info("✅ Notificação enviada com sucesso para o Encarregado de Educação ({$responsavelEE->email})");

            // Registrar o envio no banco de dados
            DB::table('email_logs')->insert([
                'email' => $responsavelEE->email,
                'subject' => "Notificação de Entrada",
                'body' => "Enviada com sucesso para o Encarregado de Educação.",
                'status' => 'success',
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error("❌ Erro ao enviar o e-mail: " . $e->getMessage());

            // Registrar falha no envio de e-mail
            DB::table('email_logs')->insert([
                'email' => $responsavelEE->email,
                'subject' => "Notificação de Entrada",
                'body' => "Erro ao enviar o e-mail: " . $e->getMessage(),
                'status' => 'failed',
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    } else {
        \Log::info("🔕 Notificação de entrada desativada para o utente ID: {$utente->id}");
    }

    return redirect()->route('confirmacao', ['id' => $utente->id])
        ->with('status', 'Registo de Entrada realizado com sucesso!');
}






public function checkOut(Request $request, $id)
{
    $utente = Asset::findOrFail($id);
    $utilizadorBackoffice = auth()->user(); // Utilizador que realizou a ação

    \Log::info("📌 Iniciando checkout para utente ID: {$utente->id} realizado por {$utilizadorBackoffice->name}");

    $request->validate([
        'responsavel_id' => 'required|integer|exists:responsaveis,id', // Garantir que o responsável existe
    ]);

    // 🔍 Buscar o responsável que fez o checkout
    $responsavel = DB::table('responsaveis_utentes')
        ->join('responsaveis', 'responsaveis_utentes.responsavel_id', '=', 'responsaveis.id')
        ->where('responsaveis_utentes.utente_id', $utente->id)
        ->where('responsaveis.id', $request->input('responsavel_id'))
        ->where('responsaveis_utentes.estado_autorizacao', 'Autorizado')  // Filtro para estado_autorizacao
        ->where('responsaveis_utentes.grau_parentesco', 'like', '%'.$request->input('grau_parentesco').'%')  // Filtro para grau_parentesco
        ->select(
            'responsaveis.id',
            'responsaveis.nome_completo as name',
            'responsaveis.email',
            'responsaveis_utentes.tipo_responsavel',
            'responsaveis_utentes.grau_parentesco',
            'responsaveis.foto as image'
        )
        ->first();

    if (!$responsavel) {
        \Log::error("⚠️ Responsável não encontrado para utente ID: {$utente->id}");
        return redirect()->back()->withErrors('Responsável não encontrado.');
    }

    \Log::info("✅ Responsável que fez o checkout: {$responsavel->name} ({$responsavel->email})");

    $grauParentesco = $responsavel->grau_parentesco ?? 'Desconhecido';

    // 🔍 Buscar o Encarregado de Educação para notificação
    $responsavelEE = DB::table('responsaveis_utentes')
        ->join('responsaveis', 'responsaveis_utentes.responsavel_id', '=', 'responsaveis.id')
        ->where('responsaveis_utentes.utente_id', $utente->id)
        ->where('responsaveis_utentes.tipo_responsavel', 'Encarregado de Educacao')  // Filtro para tipo_responsavel
        ->whereNotNull('responsaveis.email')
        ->whereRaw("LENGTH(responsaveis.email) > 5")
        ->where('responsaveis_utentes.grau_parentesco', 'like', '%'.$request->input('grau_parentesco').'%')  // Filtro para grau_parentesco
        ->where('responsaveis_utentes.estado_autorizacao', 'Autorizado')  // Filtro para estado_autorizacao
        ->select('responsaveis.id', 'responsaveis.nome_completo as name', 'responsaveis.email')
        ->first();

    // ⚠️ Se não houver Encarregado de Educação, apenas registar log
    if (!$responsavelEE) {
        \Log::warning("⚠️ Nenhum Encarregado de Educação com e-mail válido encontrado para o utente ID: {$utente->id}");
    } else {
        \Log::info("📩 Notificação será enviada para o Encarregado de Educação: {$responsavelEE->email}");
    }

    // 🔄 Atualizar status do utente
    $utente->update([
        'status' => self::STATUS_CHECKED_OUT,
        'status_id' => 25, // Checked Out
    ]);

    \Log::info("🔄 Status atualizado para Saída - Utente ID: {$utente->id}");

    // 📝 Registar a ação no log
    DB::table('action_logs')->insert([
        'user_id' => $utilizadorBackoffice->id,
        'action_type' => 'checkout',
        'target_id' => $utente->id,
        'target_type' => 'asset',
        'note' => "Saída realizada por {$responsavel->name} ({$grauParentesco}).",
        'item_type' => 'asset',
        'action_source' => 'qr-public',
        'item_id' => $utente->id,
        'created_at' => now(),
        'updated_at' => now(),
        'action_date' => now(),
    ]);

    \Log::info("📝 Log de ação inserido no banco de dados - Utente ID: {$utente->id}");

    // 🔍 Verificar manutenção associada
    $manutencao = AssetMaintenance::where('asset_id', $utente->id)
        ->whereDate('created_at', now()->toDateString())
        ->first();

    if ($manutencao) {
        \Log::info("🔍 Incidente encontrado para utente ID: {$utente->id}");
    } else {
        \Log::info("❗ Nenhum incidente encontrado para utente ID: {$utente->id}");
    }

    // 📬 Registar tentativa de envio no log de e-mails
    if ($responsavelEE) {
        DB::table('email_logs')->insert([
            'email' => $responsavelEE->email,
            'subject' => "Notificação de Saída",
            'body' => "Enviada com sucesso para o Encarregado de Educação.",
            'status' => 'success', // Indica que o e-mail ainda não foi enviado
            'sent_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // 📬 Enviar notificação se as preferências permitirem
if ($responsavelEE) {
    // Verificar se o EE tem as notificações de checkout ativadas e, se for o caso, a opção de receber notificações quando ele mesmo faz a ação
    if ($utente->receive_checkout_notifications && ($utente->receive_self_notifications || $responsavelEE->id !== $responsavel->id)) {
        \Log::info("📤 Preparando o envio da notificação para o Encarregado de Educação (EE) para o utente ID: {$utente->id}");

        try {
            // Enviar o e-mail para a fila
            Mail::to($responsavelEE->email)
                ->queue(new CheckoutNotificationMail(
                    $utente, 
                    $responsavel, 
                    $responsavelEE, 
                    $manutencao, 
                    'Saída', // Ação em português
                    now(), // Data e hora da ação
                    $grauParentesco, 
                    $utilizadorBackoffice
                ));

            \Log::info("✅ Notificação enviada com sucesso para o Encarregado de Educação ({$responsavelEE->email})");

            // Atualizar o log de e-mails com o status 'sent' após o envio bem-sucedido
            DB::table('email_logs')->where('email', $responsavelEE->email)
                ->where('subject', "Notificação de Saída")
                ->update([
                    'status' => 'success', // Atualizar o status para 'sent'
                    'updated_at' => now(),
                ]);

        } catch (\Exception $e) {
            \Log::error("❌ Erro ao enviar a notificação para o utente ID: {$utente->id}: " . $e->getMessage());

            // ❌ Atualizar status do e-mail como falha no envio
            DB::table('email_logs')
                ->where('email', $responsavelEE->email)
                ->where('subject', "Notificação de Saída")
                ->update([
                    'body' => 'Erro ao enviar o e-mail: ' . $e->getMessage(),
                    'status' => 'failed', // Atualizar o status para 'failed'
                    'updated_at' => now(),
                ]);
        }
    } else {
        \Log::info("🔕 Notificação de saída desativada para o utente ID: {$utente->id}, ou o Encarregado de Educação é o responsável pela ação");
    }
}


    return redirect()->route('confirmacao', ['id' => $utente->id])
        ->with('status', 'Registo de Saída realizado com sucesso!');
}


public function checkOutDirect(Request $request, $id)
{
    $utente = Asset::findOrFail($id);
    $motivoCheckout = $request->input('motivo_checkout');
    $nomeResponsavel = $request->input('nome_responsavel', null);
    $nrCC = $request->input('nr_cc', null);

    // ✅ Validar entrada para motivo "Pessoa com autorização excecional"
    if ($motivoCheckout === 'Pessoa com autorização excecional' && (empty($nomeResponsavel) || empty($nrCC))) {
        return redirect()->back()->withErrors('Nome e Nº CC são obrigatórios para este motivo.');
    }

    // 📌 Construir motivo detalhado
    $motivoDetalhado = $motivoCheckout;
    if ($motivoCheckout === 'Pessoa com autorização excecional') {
        $motivoDetalhado .= ": {$nomeResponsavel} (CC: {$nrCC})";
    }

    // 🔄 Atualizar status do utente
    $utente->update([
        'status' => self::STATUS_CHECKED_OUT,
        'status_id' => 25, // Checked Out
    ]);

    // 📝 Registar a ação no log de ações
    DB::table('action_logs')->insert([
        'user_id'      => auth()->id(),
        'action_type'  => 'checkout', // <- agora é descritivo
        'target_id'    => $utente->id,
        'target_type'  => 'asset',
        'note'         => "Saída direta {$motivoDetalhado} {$nomeResponsavel} {$nrCC}.",
        'item_type'    => 'asset',
        'item_id'      => $utente->id,
        'action_source'=> 'qr-public',
        'created_at'   => now(),
        'updated_at'   => now(),
        'action_date'  => now(),
    ]);
    

    // 🔍 Verificar se há manutenção associada ao utente
    $manutencao = AssetMaintenance::where('asset_id', $utente->id)
        ->whereDate('created_at', now()->toDateString())
        ->first();

    // 🔎 Buscar o Encarregado de Educação com e-mail válido
    $responsavelEE = DB::table('responsaveis_utentes')
        ->join('responsaveis', 'responsaveis_utentes.responsavel_id', '=', 'responsaveis.id')
        ->where('responsaveis_utentes.utente_id', $utente->id)
        ->where('responsaveis_utentes.tipo_responsavel', 'Encarregado de Educacao')
        ->where('responsaveis_utentes.estado_autorizacao', 'Autorizado')
        ->whereNotNull('responsaveis.email')
        ->whereRaw("LENGTH(responsaveis.email) > 5") // Garante que tem um e-mail válido
        ->select(
            'responsaveis.id',
            'responsaveis.nome_completo as name',
            'responsaveis.email',
            'responsaveis.foto as image',
            'responsaveis_utentes.grau_parentesco'  // ✅ Adicionado grau_parentesco
        )
        ->first();

    // ⚠️ Se não houver um Encarregado de Educação, logar e ignorar envio de e-mail
    if (!$responsavelEE) {
        \Log::warning("⚠️ Nenhum Encarregado de Educação com e-mail válido encontrado para o utente ID: {$utente->id}");
        
        // 📝 Registar no log de e-mails mesmo quando não há um Encarregado de Educação
        EmailLog::create([
            'email' => 'Sem e-mail válido',
            'subject' => "Notificação de Saída",
            'body' => "Nenhum Encarregado de Educação encontrado para este utente.",
            'status' => 'failed',
            'sent_at' => now(),
        ]);

        return redirect()->route('confirmacao', ['id' => $utente->id])
            ->with('status', 'Registo de Saída realizado com sucesso, mas sem envio de e-mail.');
    }

    // 📅 Definir data/hora da ação
    $dataHoraAcao = now();

    // 📬 Enviar notificação se as preferências permitirem
    if ($utente->receive_checkout_notifications && $responsavelEE) {
        \Log::info("📤 Preparando o envio da notificação para o utente ID: {$utente->id}");

        try {
            // 📬 Enviar notificação por e-mail para o Encarregado de Educação
            Mail::to($responsavelEE->email)->queue(
                new CheckoutDirectNotificationMail(
                    $utente,
                    auth()->user(),
                    $manutencao,
                    'Saída', // Passar ação traduzida
                    $responsavelEE,
                    $nomeResponsavel,
                    $nrCC,
                    $dataHoraAcao,
                    $motivoDetalhado
                )
            );

            // ✅ Registar o envio no log de e-mails
            EmailLog::create([
                'email' => $responsavelEE->email,
                'subject' => "Notificação de Saída",
                'body' => "Notificação de Saída enviada com sucesso para o Encarregado de Educação.",
                'status' => 'success',
                'sent_at' => $dataHoraAcao,
            ]);

            \Log::info("✅ Notificação de Saída Direta enviada para {$responsavelEE->email} (Utente ID: {$utente->id})");

        } catch (\Exception $e) {
            // 🛑 Se houver erro, registar no log
            EmailLog::create([
                'email' => $responsavelEE->email,
                'subject' => "Notificação de Saída",
                'body' => "Erro ao enviar: " . $e->getMessage(),
                'status' => 'failed',
                'sent_at' => now(),
            ]);

            \Log::error("❌ Erro ao enviar e-mail de Saída Direta para {$responsavelEE->email} (Utente ID: {$utente->id}): " . $e->getMessage());
        }
    } else {
        \Log::info("🔕 Notificação de saída desativada para o utente ID: {$utente->id}");
    }

    // ✅ Redirecionar com mensagem de sucesso
    return redirect()->route('confirmacao', ['id' => $utente->id])
        ->with('status', 'Registo de Saída realizado com sucesso!');
}



private function sendNotificationInBackground(
    $utente,
    $action,
    $usuario,
    $responsavel = null,
    $infoAdicional = null,
    $dataHora,
    $manutencao = null
) {
    $emailNotificacao = $utente->_snipeit_email_de_notificacao_checkinout_52;

    if (!empty($emailNotificacao)) {
        $utenteClone = clone $utente;
        $responsavelClone = $responsavel ? clone $responsavel : null;

        // Traduzir a ação para "Entrada" ou "Saída"
        $acaoTraduzida = $this->traduzirAcao($action);

        register_shutdown_function(function () use (
            $utenteClone,
            $emailNotificacao,
            $acaoTraduzida, // Usar ação traduzida
            $usuario,
            $responsavelClone,
            $infoAdicional,
            $dataHora,
            $manutencao
        ) {
            try {
                Mail::to($emailNotificacao)->send(
                    new CheckinCheckoutNotification(
                        $utenteClone,
                        $usuario,
                        $manutencao,
                        $acaoTraduzida, // Passar ação traduzida para o Mailable
                        $responsavelClone,
                        $responsavelClone->name ?? null,
                        $responsavelClone->_snipeit_cc_52 ?? 'Não informado',
                        $dataHora,
                        $infoAdicional
                    )
                );

                EmailLog::create([
                    'email' => $emailNotificacao,
                    'subject' => "Notificação de {$acaoTraduzida}", // Usar ação traduzida no assunto
                    'body' => 'O e-mail foi enviado com sucesso.',
                    'status' => 'success',
                    'sent_at' => $dataHora,
                ]);
            } catch (\Exception $e) {
                \Log::error("Erro ao enviar e-mail de {$acaoTraduzida}: " . $e->getMessage());
            }
        });
    }
}

	

	
	
}
