<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\Responsavel;
use App\Models\Asset;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Carbon\Carbon;
use App\Models\Company;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Traits\Attachable;
use App\Models\AssetUpload;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use App\Models\NotaEe;
use App\Models\AssetMaintenance;
use Illuminate\Support\Facades\DB;
use App\Models\Actionlog;
use App\Models\Documento;
use Illuminate\Support\Facades\View;
use App\Models\ResponsaveisHistorico;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Schema;
use App\Mail\QRCodesUtentesMail;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\NotificacaoBackoffice;
use App\Mail\CodigoAcessoMail;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class EeAuthController extends Controller
{
    // Mostra o formulário de login por email
    public function showLoginForm()
    {
        return view('ee.login');
    }

    // Envia código por email
public function enviarCodigo(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'h-captcha-response' => 'required|string',
    ]);

    $response = Http::asForm()->post('https://hcaptcha.com/siteverify', [
        'secret' => env('RECAPTCHA_SECRET_KEY'),
        'response' => $request->input('h-captcha-response'),
        'remoteip' => $request->ip(),
    ]);

    $result = $response->json();
    \Log::debug('hCaptcha resposta: ', $result);

    if (!($result['success'] ?? false)) {
        \Log::warning('Falha no hCaptcha: ' . json_encode($result));
        return back()->withErrors(['h-captcha-response' => 'Verificação "Não sou um robô" falhou. Tente novamente.']);
    }

    $responsavel = Responsavel::where('email', $request->email)
        ->whereHas('utentes', function ($query) {
            $query->where('responsaveis_utentes.tipo_responsavel', 'Encarregado de Educacao');
        })
        ->first();

    // Segurança: evitar enumeração de emails
    if (!$responsavel) {
        return redirect()->route('ee.mostrar-form-codigo', ['email' => $request->email])
            ->with('success', 'Se o email estiver registado, um código será enviado.');
    }

    // Geração e armazenamento do código
    $codigo = Str::upper(Str::random(6));
    Cache::put('ee_login_' . $responsavel->email, $codigo, now()->addMinutes(10));

    // --- ALTERAÇÃO PRINCIPAL AQUI ---
    // Coloca o email na fila em vez de tentar enviar agora.
    try {
        Mail::to($responsavel->email)->queue(new CodigoAcessoMail($codigo));
    } catch (\Exception $e) {
        // Este catch é apenas para o caso de a fila (ex: Redis, DB) estar offline.
        \Log::critical("Falha ao colocar email na fila para {$responsavel->email}: " . $e->getMessage());
        return back()->withErrors(['email' => 'Erro crítico no sistema de envio. Contacte o suporte.']);
    }
    // --- FIM DA ALTERAÇÃO ---

    // A mensagem para o utilizador é imediata e positiva.
    return redirect()->route('ee.mostrar-form-codigo', ['email' => $request->email])
        ->with('success', 'O seu código de acesso foi enviado para o seu email.');
}

    // Mostra o formulário para inserir o código
    public function mostrarFormCodigo(Request $request)
    {
        return view('ee.form-codigo', ['email' => $request->email]);
    }

    // Verifica o código inserido
    public function verificarCodigo(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'codigo' => 'required|string',
    ]);

    $email = $request->email;
    $codigoInserido = $request->codigo;

    $responsavel = Responsavel::where('email', $email)
        ->whereHas('utentes', function ($query) {
            $query->where('responsaveis_utentes.tipo_responsavel', 'Encarregado de Educacao');
        })
        ->first();

    if (!$responsavel) {
        return back()->withErrors(['email' => 'Este email não está associado a um Encarregado de Educação.']);
    }

    $codigoCache = Cache::get('ee_login_' . $email);

    if (!$codigoCache || $codigoCache !== $codigoInserido) {
        return back()->withErrors(['codigo' => 'Código inválido ou expirado.']);
    }

    // *** ISTO É A LINHA QUE ESTÁ EM FALTA E QUE RESOLVE O PROBLEMA! ***
    // Inicia a sessão do utilizador no guard 'ee' do Laravel
    Auth::guard('ee')->login($responsavel);
    // As tuas variáveis de sessão personalizadas ainda são úteis para o contador de sessão e outras verificações manuais
    session([
        'ee_responsavel_id' => $responsavel->id,
        'ee_session_expires_at' => now()->addMinutes(30)->timestamp, // Ou `config('session.lifetime')` como antes
    ]);

    // Registo no histórico de ações
    Actionlog::create([
        'item_id'       => $responsavel->id,
        'item_type'     => Responsavel::class,
        'action_type'   => 'access',
        'note'          => "Dashboard acedido pelo Encarregado de Educação: {$responsavel->nome_completo}",
        'action_source' => 'portal-ee',
        'user_id'       => $responsavel->id,
        'remote_ip'     => request()->ip(),
        'user_agent'    => request()->header('User-Agent'),
        'created_at'    => now(),
    ]);

    // Limpa o código usado
    Cache::forget('ee_login_' . $email);

    return redirect()->route('ee.gestao')->with([
    'success' => 'Autenticação concluída com sucesso!',
    'force_reload' => true,
]);
}

public function mostrarQr($id)
{
    $utente = Asset::findOrFail($id);

    // Aqui podes customizar o que mostrar — nome, foto, estado da senha, etc.
    return view('ee.qr', compact('utente'));
}


public function dashboard()
{
    $responsavelId = session('ee_responsavel_id');

    // Verifica se a sessão do Encarregado de Educação está ativa
    if (!$responsavelId) {
        return redirect()->route('ee.login')->withErrors([
            'email' => 'Sessão expirada. Faça login novamente.'
        ]);
    }

    // Recupera o Encarregado de Educação logado
    $responsavelEE = Responsavel::with('utentes')->find($responsavelId);

    // Verifica se o responsável existe e é do tipo "Encarregado de Educação"
    if (!$responsavelEE || !$responsavelEE->isEncarregadoDeEducacao()) {
        return redirect()->route('ee.login')->withErrors([
            'email' => 'Sessão inválida ou sem permissões.'
        ]);
    }

    // Recupera os educandos associados ao Encarregado de Educação, incluindo os autorizados
    $educandos = $responsavelEE->utentes()->wherePivot('tipo_responsavel', 'Encarregado de Educacao')->get();

    // Verifica se o responsável tem educandos associados
    if ($educandos->isEmpty()) {
        return redirect()->route('ee.login')->withErrors([
            'email' => 'Este Encarregado de Educação não tem educandos associados.'
        ]);
    }

    // Obter os IDs dos educandos associados ao Encarregado de Educação
    $utenteIds = $educandos->pluck('id');

    // Recupera os responsáveis (Encarregados de Educação e autorizados) associados aos educandos
    $responsaveisEEPrimeiro = Responsavel::where('ativo', 1)
        ->whereHas('utentes', function ($query) use ($utenteIds) {
            $query->whereIn('assets.id', $utenteIds);
        })
        ->with([
            'documentos',
            'utentes' => function ($q) use ($utenteIds) {
                $q->whereIn('assets.id', $utenteIds)
                  ->withPivot('grau_parentesco', 'tipo_responsavel', 'estado_autorizacao', 'data_inicio_autorizacao', 'data_fim_autorizacao', 'observacoes', 'dias_nao_autorizados');
            }
        ])
        ->get();

    // Carrega informações adicionais dos educandos, como notas e manutenções
    foreach ($educandos as $utente) {
        $utente->load(['notasEe' => function ($query) {
            $query->latest();
        }]);
        
        $utente->maintenances = AssetMaintenance::with('admin')
            ->where('asset_id', $utente->id)
            ->latest()
            ->take(5)
            ->get();    

        $utente->ultimos_logs = DB::table('action_logs')
            ->where('item_type', 'asset')
            ->where('item_id', $utente->id)
            ->orderBy('action_date', 'desc')
            ->take(5)
            ->get();
    }

    // Geração dos QR codes para cada educando
    $qrcodes = [];
    foreach ($educandos as $utente) {
        $linkQr = "https://parque-seguro.jf-parquedasnacoes.pt:8126/confirmar-check/" . $utente->id;
        $qrcodes[$utente->id] = base64_encode(QrCode::format('png')->size(150)->generate($linkQr));
    }

    // Dados dos programas (para exibir na interface)
    $programas = [
        '_snipeit_ha_ferias_no_parque_67' => ['nome' => 'Há Férias no Parque', 'icone' => '🏖️'],
        '_snipeit_parque_em_movimento_verao_68' => ['nome' => 'Parque em Movimento Verão', 'icone' => '🌞'],
        '_snipeit_parque_em_movimento_pascoa_69' => ['nome' => 'Parque em Movimento Páscoa', 'icone' => '🐣'],
        '_snipeit_aaaf_caf_ferias_pascoa_70' => ['nome' => 'Férias Páscoa (AAAF/CAF)', 'icone' => '🐰'],
        '_snipeit_aaaf_caf_ferias_verao_71' => ['nome' => 'Férias Verão (AAAF/CAF)', 'icone' => '⛱️'],
        '_snipeit_parque_em_movimento_natal_72' => ['nome' => 'Parque em Movimento Natal', 'icone' => '🎄'],
        '_snipeit_aaaf_caf_ferias_carnaval_73' => ['nome' => 'Férias Carnaval (AAAF/CAF)', 'icone' => '🎭'],
    ];

    // Retorna a view com as informações do Encarregado de Educação, educandos, responsáveis e QR Codes
    return view('ee.dashboard', [
        'responsavel' => $responsavelEE,
        'educandos' => $educandos,
        'responsaveis' => $responsaveisEEPrimeiro, // Apenas os "Encarregados de Educação"
        'programas' => $programas,
        'qrcodes' => $qrcodes, // 📦 variável com os QR codes
    ]);
}



public function logout(Request $request) // <-- Certifica-te que 'Request $request' está presente
{
    // *** ISTO É A LINHA QUE ESTÁ EM FALTA E QUE RESOLVE O PROBLEMA! ***
    // Termina a sessão do utilizador no guard 'ee' do Laravel
    Auth::guard('ee')->logout();

    // Limpa as tuas variáveis de sessão personalizadas
    session()->forget(['ee_responsavel_id', 'ee_session_expires_at']);

    // Invalida a sessão atual e regenera o token CSRF
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('ee.login')->with('success', 'Sessão terminada com sucesso.');
}

    public function reenviarCodigo(Request $request)
{
    $email = $request->query('email');

    if (!$email) {
        return redirect()->route('ee.login')->withErrors(['email' => 'Email não especificado.']);
    }

    $responsavel = Responsavel::where('email', $email)
        ->whereHas('utentes', function ($query) {
            $query->where('responsaveis_utentes.tipo_responsavel', 'Encarregado de Educacao');
        })
        ->first();

    if (!$responsavel) {
        // Por segurança, não revele se o email existe ou não.
        return redirect()->route('ee.mostrar-form-codigo', ['email' => $email])
               ->with('success', 'Se o email estiver registado, um novo código será enviado.');
    }

    $codigo = Str::upper(Str::random(6));
    Cache::put('ee_login_' . $email, $codigo, now()->addMinutes(10));

    // --- ALTERAÇÃO PRINCIPAL AQUI ---
    // Usa o Mailable e coloca na fila.
    try {
        Mail::to($responsavel->email)->queue(new CodigoAcessoMail($codigo));
    } catch (\Exception $e) {
        \Log::critical("Falha ao colocar email de REENVIO na fila para {$responsavel->email}: " . $e->getMessage());
        return back()->withErrors(['email' => 'Erro crítico no sistema de envio. Contacte o suporte.']);
    }
    // --- FIM DA ALTERAÇÃO ---

    return redirect()->route('ee.mostrar-form-codigo', ['email' => $email])
        ->with('success', 'Um novo código foi enviado para o seu email.');
}
    public function editarUtente($id)
    {
        // Obter o utente
        $utente = $this->getUtenteAutorizado($id);
    
        // Obter todas as empresas (escolas) disponíveis
        $companies = Company::all();
    
        // Obter o Encarregado de Educação autenticado pela sessão
        $responsavelId = session('ee_responsavel_id');
        $responsavel = \App\Models\Responsavel::find($responsavelId);
    
        // Passar para a view
        return view('ee.editar-utente', compact('utente', 'companies', 'responsavel'));
    }
    
    
    
    
    
public function updateUtente(Request $request, $id)
{
    \Log::info('Update iniciado pelo EE!', $request->all());

    $request->validate([
        '_snipeit_nif_30' => [
            'required',
            'digits:9',
            'regex:/^[1235689]\d{8}$/',
            Rule::unique('assets', '_snipeit_nif_30')->ignore($id),
        ],
    ]);

    $utente = $this->getUtenteAutorizado($id);
    $responsavelId = session('ee_responsavel_id');
    $responsavel = \App\Models\Responsavel::find($responsavelId);

    // Calcular idade se necessário
    if ($request->filled('_snipeit_data_nascimento_34')) {
        $idade = Carbon::parse($request->_snipeit_data_nascimento_34)->age;
        $request->merge(['_snipeit_idade_35' => $idade]);
    }

    $camposAtualizaveis = [
        'name', 'serial', '_snipeit_data_nascimento_34', '_snipeit_nif_30',
        '_snipeit_ha_ferias_no_parque_67', '_snipeit_utente_sns_29',
        '_snipeit_seg_social_31', '_snipeit_morada_no_andar_fracao_lugar_bairro_32',
        '_snipeit_codigo_postal_33', '_snipeit_data_validade_documento_de_identificacao_37',
        '_snipeit_nomes_irmao_irma_39', '_snipeit_tem_algum_problema_de_saude_41',
        '_snipeit_restricoes_alimentares_42', '_snipeit_contacto_de_emergencia_47',
        'status', '_snipeit_contacto_telefonico_54', '_snipeit_email_55',
        '_snipeit_autoriza_a_recolha_e_difusao_de_imagens_56', '_snipeit_grau_de_ensino_57',
        '_snipeit_ano_esc_58', '_snipeit_turma_59', '_snipeit_tem_irmaos_na_caf_60',
        '_snipeit_distrito_61', '_snipeit_concelho_74', '_snipeit_freguesia_62',
        '_snipeit_localidade_75', '_snipeit_autorizacao_a_recolha_e_difusao_de_imagen_63',
        '_snipeit_idade_35', 'company_id', 'notes',
    ];

    $dados = $request->only($camposAtualizaveis);
    $alteracoes = [];

    foreach ($dados as $campo => $novoValor) {
        $valorAntigo = $utente->$campo;
        if ($valorAntigo != $novoValor) {
            // Limpa o nome do campo para apresentação
            // Mapeamento manual para campos conhecidos (evita mostrar "snipeit xyz")
$labelsPersonalizados = [
    'name' => 'Nome',
    'serial' => 'Nrº de identificação',
    '_snipeit_utente_sns_29' => 'Utente SNS',
    '_snipeit_seg_social_31' => 'Segurança Social',
    '_snipeit_data_nascimento_34' => 'Data de Nascimento',
    '_snipeit_idade_35' => 'Idade',
    '_snipeit_data_validade_documento_de_identificacao_37' => 'Validade do Documento',
    '_snipeit_contacto_de_emergencia_47' => 'Contacto de Emergência',
    '_snipeit_turma_59' => 'Turma',
    '_snipeit_distrito_61' => 'Distrito',
    '_snipeit_concelho_74' => 'Concelho',
    '_snipeit_freguesia_62' => 'Freguesia',
    '_snipeit_localidade_75' => 'Localidade',
    '_snipeit_morada_no_andar_fracao_lugar_bairro_32' => 'Morada',
    '_snipeit_codigo_postal_33' => 'Código Postal',
    '_snipeit_contacto_telefonico_54' => 'Contacto Telefónico',
    '_snipeit_email_55' => 'Email',
    '_snipeit_autoriza_a_recolha_e_difusao_de_imagens_56' => 'Autorização para Imagens',
    '_snipeit_autorizacao_a_recolha_e_difusao_de_imagen_63' => 'Autorização para Imagens',
    'company_id' => 'Escola',
    'notes' => 'Notas',
];

// Obter label bonito
$label = $labelsPersonalizados[$campo] ?? ucwords(trim(str_replace('_', ' ', preg_replace('/^_?snipeit[_ ]?/', '', preg_replace('/\d+$/', '', $campo)))));


            // Emojis opcionais por tipo
            $emoji = match(true) {
                str_contains($label, 'Data') => '📅',
                str_contains($label, 'Contacto') || str_contains($label, 'Telefone') => '📞',
                str_contains($label, 'Email') => '📧',
                str_contains($label, 'Localidade') || str_contains($label, 'Distrito') || str_contains($label, 'Concelho') || str_contains($label, 'Freguesia') => '🌍',
                str_contains($label, 'Idade') => '🔢',
                default => '🔸'
            };

            $antigoFmt = ($valorAntigo !== null && $valorAntigo !== '') ? $valorAntigo : '—';
            $novoFmt   = ($novoValor !== null && $novoValor !== '') ? $novoValor : '—';
            $alteracoes[] = "{$emoji} <strong>{$label}</strong>: \"{$antigoFmt}\" → \"{$novoFmt}\"";
        }
    }

    $utente->update($dados);

    // Log no histórico
    \App\Models\Actionlog::create([
        'item_id'       => $utente->id,
        'item_type'     => \App\Models\Asset::class,
        'action_type'   => 'update',
        'note'          => 'Dados do educando atualizados pelo EE: ' . $responsavel->nome_completo,
        'action_source' => 'portal-ee',
        'remote_ip'     => request()->ip(),
        'user_agent'    => request()->header('User-Agent'),
        'created_at'    => now(),
    ]);

    // Notificação se houver alterações
    if ($alteracoes) {
        \App\Models\NotificacaoBackoffice::create([
            'tipo' => 'dados_utente_ee',
            'asset_id' => $utente->id,
            'mensagem' => "📝 <strong>EE {$responsavel->nome_completo}</strong> atualizou dados do educando <strong>{$utente->name}</strong>:<br>" . implode('<br>', $alteracoes),
        ])->utilizadores()->attach(
            \App\Models\User::whereHas('groups', function ($q) {
                $q->whereIn('name', ['NSI', 'COORDENADORES AAAF CAF']);
            })->pluck('id')->toArray(),
            ['lida' => false]
        );
    }

    return back()->with('success', '✅ Dados do educando atualizados com sucesso!');
}



    
    
public function verFicheiro($id, $filename)
{
    // Verificações opcionais, como se o utente pertence ao responsável autenticado
    $path = storage_path('private_uploads/assets/' . $filename);

    if (!file_exists($path)) {
        abort(404, 'Ficheiro não encontrado.');
    }

    return response()->file($path);
}


public function guardarPreferencias(Request $request, $id)
{
    $utente = Asset::findOrFail($id);
    $responsavelId = session('ee_responsavel_id');
    $responsavel = Responsavel::with('utentes')->findOrFail($responsavelId);

    if (!$responsavel->utentes->contains($utente)) {
        abort(403, 'Este educando não está associado a si.');
    }

    $utente->receive_checkin_notifications  = $request->has('receive_checkin_notifications');
    $utente->receive_checkout_notifications = $request->has('receive_checkout_notifications');
    $utente->receive_self_notifications     = $request->has('receive_self_notifications');
    $utente->save();

    Actionlog::create([
        'item_id'       => $utente->id,
        'item_type'     => Asset::class,
        'action_type'   => 'update',
        'note'          => "Preferências atualizadas pelo EE: {$responsavel->nome_completo}",
        'action_source' => 'portal-ee',
        'remote_ip'     => request()->ip(),
        'user_agent'    => request()->header('User-Agent'),
        'created_at'    => now(),
    ]);

    \App\Models\NotificacaoBackoffice::create([
    'tipo' => 'preferencias_atualizadas',
    'asset_id' => $utente->id,
    'mensagem' => "⚙️ <strong>EE {$responsavel->nome_completo}</strong> atualizou as preferências de notificação para o utente {$utente->name}.",
])->utilizadores()->attach(
    \App\Models\User::whereHas('groups', function ($q) {
        $q->whereIn('name', ['NSI', 'COORDENADORES AAAF CAF']);
    })->pluck('id')->toArray(),
    ['lida' => false]
);


    return back()->with('success', 'Preferências atualizadas com sucesso.');
}


public function atualizarPreferenciaAjax(Request $request, $id)
{
    try {
        Log::debug("----------------------------------------------------");
        Log::debug("[PREFERENCIA AJAX] Iniciando para Utente ID: {$id}");
        Log::debug("[PREFERENCIA AJAX] Request Payload: ", $request->all());

        $utente = Asset::findOrFail($id);
        $responsavelId = session('ee_responsavel_id');

        if (!$responsavelId) {
            Log::warning("[PREFERENCIA AJAX] Sessão inválida ou não autenticado para Utente ID: {$id}.");
            return response()->json(['erro' => 'Sessão inválida.'], 401);
        }

        $responsavel = Responsavel::findOrFail($responsavelId);

        if (!$responsavel->utentes()->where((new Asset)->getTable().'.id', $utente->id)->exists()) {
            Log::warning("[PREFERENCIA AJAX] Responsável ID {$responsavelId} não autorizado para Utente ID: {$id}");
            return response()->json(['erro' => 'Não autorizado.'], 403);
        }

        $tipo = $request->input('tipo');
        $valorInputRequest = $request->input('valor');

        $campo = match ($tipo) {
            'checkin' => 'receive_checkin_notifications',
            'checkout' => 'receive_checkout_notifications',
            'self' => 'receive_self_notifications',
            'autoriza_imagens' => '_snipeit_autoriza_a_recolha_e_difusao_de_imagens_56',
            'sair_sozinho' => '_snipeit_pode_sair_sozinho_66',
            default => null
        };

        if (!$campo) {
            Log::error("[PREFERENCIA AJAX] Tipo de preferência inválido: {$tipo}");
            return response()->json(['erro' => 'Tipo inválido.'], 400);
        }

        // Caso especial: "sair_sozinho" guarda "Sim" ou NULL (checkbox)
        $usarSimOuNull = in_array($tipo, ['sair_sozinho', 'autoriza_imagens']);
        $valorParaGuardar = $usarSimOuNull
            ? ($valorInputRequest ? 'Sim' : null)
            : (filter_var($valorInputRequest, FILTER_VALIDATE_BOOLEAN) ? 1 : 0);

        Log::debug("[PREFERENCIA AJAX] Tipo: {$tipo}, Campo: {$campo}, Valor recebido: " . print_r($valorInputRequest, true) . ", Interpretado como: " . print_r($valorParaGuardar, true));

        $valorAntigo = $utente->$campo;
        $utente->$campo = $valorParaGuardar;

        if ($utente->isDirty($campo)) {
            Log::debug("[PREFERENCIA AJAX] O campo '{$campo}' está dirty. Tentando guardar...");
            if (!$utente->save()) {
                Log::error("[PREFERENCIA AJAX] ERRO: \$utente->save() retornou false.");
                return response()->json(['erro' => 'Erro ao guardar a preferência.'], 500);
            }
            Log::info("[PREFERENCIA AJAX] SUCESSO: Valor guardado.");

            $this->registarHistoricoPreferencia($utente, $responsavel, $campo, $valorAntigo, $valorParaGuardar);
        } else {
            Log::warning("[PREFERENCIA AJAX] O campo '{$campo}' não ficou dirty. Nenhuma alteração.");
        }

        // Mensagem de feedback
        $nomesHumanos = [
            'checkin' => 'Notificação de entrada',
            'checkout' => 'Notificação de saída',
            'self' => 'Notificação pessoal',
            'autoriza_imagens' => 'Autorização para imagens',
            'sair_sozinho' => 'Permissão para sair sozinho'
        ];

        $nomePreferencia = $nomesHumanos[$tipo] ?? ucfirst($tipo);
        $estadoMensagem = $valorParaGuardar
            ? ($usarSimOuNull ? 'concedida' : 'ativada')
            : ($usarSimOuNull ? 'revogada' : 'desativada');

        $mensagem = "{$nomePreferencia} foi {$estadoMensagem} para {$utente->name}.";
        Log::debug("[PREFERENCIA AJAX] Mensagem final: {$mensagem}");

        // Log no histórico
        $valorParaLog = $valorParaGuardar
            ? ($usarSimOuNull ? 'Sim (Concedida)' : 'Sim (Ativada)')
            : ($usarSimOuNull ? 'Não (Revogada)' : 'Não (Desativada)');

        Actionlog::create([
            'item_id'       => $utente->id,
            'item_type'     => Asset::class,
            'action_type'   => 'update',
            'note' => "{$nomePreferencia} foi {$estadoMensagem} para {$utente->name}. (Valor definido para: {$valorParaLog}) por EE {$responsavel->nome_completo}.",
            'action_source' => 'portal-ee',
            'remote_ip'     => $request->ip(),
            'user_agent'    => $request->header('User-Agent'),
            'created_at'    => now(),
        ]);
       
        \App\Models\NotificacaoBackoffice::create([
    'tipo' => 'preferencia_individual',
    'asset_id' => $utente->id,
    'mensagem' => "🔧 <strong>EE {$responsavel->nome_completo}</strong> alterou \"{$nomePreferencia}\" para o utente {$utente->name}: {$valorParaLog}.",
])->utilizadores()->attach(
    \App\Models\User::whereHas('groups', function ($q) {
        $q->whereIn('name', ['NSI', 'COORDENADORES AAAF CAF']);
    })->pluck('id')->toArray(),
    ['lida' => false]
);


        
        return response()->json(['mensagem' => $mensagem]);

    } catch (ModelNotFoundException $e) {
        Log::error("[PREFERENCIA AJAX] ModelNotFoundException: " . $e->getMessage());
        return response()->json(['erro' => 'Registo não encontrado.'], 404);
    } catch (QueryException $e) {
        Log::error("[PREFERENCIA AJAX] QueryException: " . $e->getMessage());
        return response()->json(['erro' => 'Erro na base de dados.'], 500);
    } catch (Throwable $e) {
        Log::error("[PREFERENCIA AJAX] Erro inesperado: " . $e->getMessage());
        return response()->json(['erro' => 'Erro inesperado no servidor.'], 500);
    }
}





    public function autorizarResponsavelAjax(Request $request)
{
    $request->validate([
        'utente_id' => 'required|exists:assets,id',
        'responsavel_id' => 'required|exists:responsaveis,id',
    ]);

    $existe = DB::table('responsaveis_utentes')
        ->where('utente_id', $request->utente_id)
        ->where('responsavel_id', $request->responsavel_id)
        ->first();

    if ($existe) {
        return response()->json(['success' => false, 'mensagem' => 'Já está associado.']);
    }

    $pivotId = DB::table('responsaveis_utentes')->insertGetId([
        'utente_id' => $request->utente_id,
        'responsavel_id' => $request->responsavel_id,
        'tipo_responsavel' => 'Autorizado',
        'grau_parentesco' => 'Outro',
        'estado_autorizacao' => 'Autorizado',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $responsavel = Responsavel::find($request->responsavel_id);

    return response()->json([
        'success' => true,
        'pivot_id' => $pivotId,
        'responsavel' => [
            'nome_completo' => $responsavel->nome_completo,
            'grau_parentesco' => 'Outro',
            'tipo_responsavel' => 'Autorizado',
            'foto_url' => $responsavel->foto
                ? route('ee.responsavel.foto', ['filename' => basename($responsavel->foto)])
                : asset('img/anonimoadulto.png'),
        ],
    ]);
}


    /**
     * Valida se o utente pertence ao EE autenticado.
     * @param int $utenteId
     * @return \App\Models\Asset
     */
    private function getUtenteAutorizado($utenteId)
    {
        $responsavelId = session('ee_responsavel_id');

        if (!$responsavelId) {
            abort(403, 'Sessão expirada ou não autenticado.');
        }

        $utente = Asset::with('responsaveis')->findOrFail($utenteId);

        $autorizado = $utente->responsaveis->contains(function ($r) use ($responsavelId) {
            return $r->id === $responsavelId && $r->pivot->tipo_responsavel === 'Encarregado de Educacao';
        });

        if (!$autorizado) {
            abort(403, 'Não autorizado a aceder a este utente.');
        }

        return $utente;
    }


public function uploadAnexo(Request $request, $id)
    {
        $utente = $this->getUtenteAutorizado($id);
        $responsavelId = session('ee_responsavel_id');
        $responsavel = \App\Models\Responsavel::find($responsavelId);

        if (!$responsavel) {
            Log::error('Responsável não encontrado na sessão ou na base de dados.', ['responsavel_id_sessao' => $responsavelId]);
            return response()->json(['success' => false, 'error' => 'Erro de autenticação do responsável.'], 403);
        }

        if ($request->hasFile('ficheiro') && $request->file('ficheiro')->isValid()) {
            $ficheiro = $request->file('ficheiro');
            $nota = $request->input('nota');
            $originalName = $ficheiro->getClientOriginalName();
            $tempPath = $ficheiro->getRealPath(); // Caminho para o ficheiro temporário

            // --- VERIFICAÇÃO COM CLAMAV ---
            try {
                // Tente usar 'clamdscan' se o clamd daemon estiver ativo para melhor performance.
                // Se não, 'clamscan' funciona bem mas é mais lento para cada scan.
                // Certifique-se que 'clamscan' ou 'clamdscan' estão no PATH do seu servidor web
                // ou forneça o caminho completo (ex: /usr/bin/clamscan)
                $process = new Process(['clamscan', '--infected', '--no-summary', $tempPath]);
                // Alternativa com clamdscan (mais rápido se o daemon estiver a correr):
                // $process = new Process(['clamdscan', '--no-summary', '--infected', $tempPath]);

                $process->run(); // Executa o comando

                $exitCode = $process->getExitCode();

                if ($exitCode === 1) { // Código 1 geralmente significa que um vírus foi encontrado
                    Log::warning('Virus detected in uploaded file', [
                        'filename' => $originalName,
                        'temp_path' => $tempPath,
                        'clamav_output' => $process->getOutput(), // O output pode conter o nome do vírus
                        'responsavel_id' => $responsavel->id,
                        'utente_id' => $utente->id,
                    ]);
                    // O ficheiro temporário será eliminado automaticamente pelo PHP no final do script.
                    return response()->json([
                        'success' => false,
                        'error' => 'Ficheiro infetado detetado e bloqueado. O ficheiro não foi guardado.',
                        'virus_detected' => true // Flag para o frontend
                    ], 422); // 422 Unprocessable Entity é um bom código HTTP para isto
                } elseif ($exitCode > 1) { // Outros códigos de saída indicam um erro do ClamAV
                    Log::error('ClamAV scan error', [
                        'filename' => $originalName,
                        'temp_path' => $tempPath,
                        'exit_code' => $exitCode,
                        'clamav_error_output' => $process->getErrorOutput(),
                        'clamav_output' => $process->getOutput(),
                        'responsavel_id' => $responsavel->id,
                        'utente_id' => $utente->id,
                    ]);
                    return response()->json([
                        'success' => false,
                        'error' => 'Ocorreu um erro no serviço de antivírus ao tentar verificar o ficheiro. Por favor, tente novamente mais tarde.',
                        'clamav_error' => true // Flag para o frontend
                    ], 500); // 500 Internal Server Error
                }
                // Se $exitCode === 0, o ficheiro está limpo e podemos continuar.

            } catch (\Exception $e) {
                Log::error('Exception during ClamAV scan process initialization', [
                    'filename' => $originalName,
                    'exception_message' => $e->getMessage(),
                    'responsavel_id' => $responsavel->id,
                    'utente_id' => $utente->id,
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Ocorreu um erro inesperado ao processar a verificação do ficheiro.',
                    'system_error' => true // Flag para o frontend
                ], 500);
            }
            // --- FIM DA VERIFICAÇÃO COM CLAMAV ---

            // Se a verificação passou (nenhum vírus encontrado e sem erros do ClamAV), prossiga:
            $filename = 'utente-' . $utente->id . '-' . Str::random(8) . '-' . $originalName;
            // É importante usar 'local' ou o disco privado configurado para 'private_uploads'
            $path = $ficheiro->storeAs('private_uploads/assets', $filename, 'local');

            Log::debug('Ficheiro guardado em', ['path' => storage_path('app/' . $path)]);

            // Criar o log e guardar o ID
            $log = \App\Models\Actionlog::create([
                'item_id'     => $utente->id,
                'item_type'   => \App\Models\Asset::class, // Se Utente é um Asset
                'filename'    => $filename,
                'filepath'    => $path,
                'action_type' => 'uploaded',
                'note'        => $nota,
                'action_source' => 'portal-ee',
                'created_at'  => now(),
                // Adicione o user_id que fez o upload, se aplicável e disponível
                // 'user_id' => $responsavel->user_id, // Exemplo, se Responsavel tem um user_id
                // 'target_id' => $utente->id, // Se Actionlog tem target_id
            ]);

            // URL funcional para o backoffice
            $linkFicheiro = url("/hardware/{$utente->id}/showfile/{$log->id}?inline=true");

            // 🔔 Notificação backoffice
            $notificacao = \App\Models\NotificacaoBackoffice::create([
                'tipo' => 'ficheiro_anexado',
                'asset_id' => $utente->id, // Ou 'related_id' ou o campo que usa para associar ao utente
                'mensagem' => "📎 <strong>EE {$responsavel->nome_completo}</strong> anexou um ficheiro ao utente {$utente->name}: <a href=\"{$linkFicheiro}\" target=\"_blank\">{$originalName}</a>" . ($nota ? " com nota: \"{$nota}\"." : '.'),
            ]);

            $notificacao->utilizadores()->attach(
                \App\Models\User::whereHas('groups', function ($q) {
                    $q->whereIn('name', [
                        'NSI',
                        'COORDENADORES AAAF CAF',
                        'admin',
                        'backoffice',
                        'secretaria',
                    ]);
                })->pluck('id')->toArray(),
                ['lida' => false]
            );

            return response()->json([
                'success' => true,
                'filename' => $filename,
                'filenote' => $nota,
                'created_at' => now()->format('d/m/Y H:i'),
                // Certifique-se que a rota 'ee.utente.verFicheiro' existe e está correta.
                // Se esta rota serve o ficheiro diretamente, verifique as permissões e segurança.
                'url' => route('ee.utente.verFicheiro', ['id' => $utente->id, 'filename' => $filename]),
            ]);
        }

        return response()->json(['success' => false, 'error' => 'Ficheiro inválido ou não carregado.']);
    }




public function atualizarFoto(Request $request, $id)
{
    $utente = $this->getUtenteAutorizado($id);

    try {
        if ($request->filled('imagem_base64')) {
            $image = Image::make($request->imagem_base64);
        } elseif ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $request->validate([
                'foto' => 'required|image|max:4096',
            ]);
            $image = Image::make($request->file('foto')->getRealPath());
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma imagem válida recebida.'
            ], 400);
        }

        $filename = 'utente-image-' . $utente->id . '-' . Str::random(10) . '.jpg';
        $uploadPath = public_path('uploads/assets');

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $image->fit(413, 531, function ($constraint) {
            $constraint->upsize();
        });

        $image->save($uploadPath . '/' . $filename, 75);

        $utente->image = $filename;
        $utente->save();
        $responsavel = Responsavel::find(session('ee_responsavel_id'));

        Actionlog::create([
            'item_id' => $utente->id,
            'item_type' => Asset::class,
            'action_type' => 'uploaded',
            'note' => "Foto do educando atualizada pelo EE: {$responsavel->nome_completo}",
            'action_source' => 'portal-ee',
            'user_id' => $responsavel->id,
            'remote_ip' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'created_at' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => '📸 Foto do educando atualizada com sucesso!',
            'url' => route('ee.utente.foto', ['filename' => $filename]),
        ]);

    } catch (\Exception $e) {
        \Log::error("Erro ao processar imagem do educando ID {$utente->id}: " . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Erro ao processar a imagem.'], 500);
    }
}



public function verFoto($filename)
{
    $responsavelId = session('ee_responsavel_id');

    if (!$responsavelId) {
        abort(403, 'Sessão inválida');
    }

    // Verifica se o utente está autorizado
    $utente = Asset::where('image', $filename)
        ->whereHas('responsaveis', function ($q) use ($responsavelId) {
            $q->where('responsaveis.id', $responsavelId)
              ->where('responsaveis_utentes.tipo_responsavel', 'Encarregado de Educacao');
        })
        ->first();

    if (!$utente) {
        abort(403, 'Utente não autorizado ou não encontrado.');
    }

    // Corrigido: usar public_path e não storage_path
    $path = public_path('uploads/assets/' . $filename);

    if (!file_exists($path)) {
        abort(404, 'Ficheiro não encontrado.');
    }

    return response()->file($path);
}


public function guardarNota(Request $request, $id)
{
    $utente = Asset::findOrFail($id);

    $responsavelId = session('ee_responsavel_id');
    if (!$responsavelId) {
        return response()->json(['success' => false, 'error' => 'Sessão inválida'], 403);
    }

    $request->validate([
        'conteudo' => 'required|string|max:1000',
    ]);

    $nota = NotaEe::create([
        'asset_id' => $id,
        'responsavel_id' => $responsavelId,
        'conteudo' => $request->conteudo,
    ]);

    $responsavel = \App\Models\Responsavel::find($responsavelId);

    // Registar no histórico de ações
    $this->logAcaoEE($responsavel, 'update', "Nota adicionada pelo EE: {$responsavel->nome_completo}", $utente);

    // Criar notificação e associar aos utilizadores
    $notificacao = \App\Models\NotificacaoBackoffice::create([
        'tipo' => 'nota_adicionada',
        'asset_id' => $utente->id,
        'mensagem' => "📝 <strong>EE {$responsavel->nome_completo}</strong> adicionou uma nova nota para o utente {$utente->name}: \"{$request->conteudo}\"",
    ]);

    $notificacao->utilizadores()->attach(
        \App\Models\User::whereHas('groups', function ($q) {
            $q->whereIn('name', [
                'admin',
                'backoffice',
                'secretaria',
                'NSI',
                'COORDENADORES AAAF CAF'
            ]);
        })->pluck('id')->toArray(),
        ['lida' => false]
    );

    return response()->json([
        'success' => true,
        'conteudo' => $nota->conteudo,
        'created_at' => $nota->created_at->format('d/m/Y H:i'),
        'nota_id' => $nota->id,
        'responsavel_id' => $responsavelId,
    ]);
}




public function eliminarNota(Request $request, $id, $notaId)
{
    $responsavelId = session('ee_responsavel_id');
    if (!$responsavelId) {
        return response()->json(['success' => false, 'error' => 'Sessão inválida'], 403);
    }

    $nota = \App\Models\NotaEe::where('id', $notaId)
        ->where('asset_id', $id)
        ->where('responsavel_id', $responsavelId)
        ->first();

    if (!$nota) {
        return response()->json(['success' => false, 'error' => 'Nota não encontrada ou não autorizada.'], 404);
    }

    $conteudoApagado = $nota->conteudo;
    $nota->delete();

    $responsavel = \App\Models\Responsavel::find($responsavelId);
    $utente = \App\Models\Asset::find($id);

    // Registo no histórico
    $this->logAcaoEE($responsavel, 'update', "Nota eliminada pelo EE: {$responsavel->nome_completo}", $utente);

    // 🛎️ Criar notificação para os grupos
    \App\Models\NotificacaoBackoffice::create([
        'tipo' => 'nota_removida',
        'asset_id' => $utente->id,
        'mensagem' => "❌ <strong>EE {$responsavel->nome_completo}</strong> eliminou uma nota do utente {$utente->name}: \"{$conteudoApagado}\"",
    ])->utilizadores()->attach(
        \App\Models\User::whereHas('groups', function ($q) {
            $q->whereIn('name', [
                'NSI',
                'COORDENADORES AAAF CAF'
            ]);
        })->pluck('id')->toArray(),
        ['lida' => false]
    );

    return response()->json(['success' => true]);
}


public function historicoUtente(Request $request, $id)
{
    $utente = $this->getUtenteAutorizado($id); // Garante que pertence ao EE

    $logs = \DB::table('action_logs')
        ->where('item_type', 'asset')
        ->where('item_id', $utente->id)
        ->when($request->start_date, fn($q) => $q->whereDate('action_date', '>=', $request->start_date))
        ->when($request->end_date, fn($q) => $q->whereDate('action_date', '<=', $request->end_date))
        ->when($request->action_type, fn($q) => $q->where('action_type', $request->action_type))
        ->orderBy('action_date', 'desc')
        ->paginate(10);

    $responsavel = \App\Models\Responsavel::find(session('ee_responsavel_id'));

    return view('ee.historico', compact('logs', 'utente', 'responsavel'));
}


private function logAcaoEE($responsavel, $tipo, $nota, $item = null)
{
    Actionlog::create([
        'item_id' => $item?->id ?? $responsavel->id,
        'item_type' => $item ? get_class($item) : Responsavel::class,
        'action_type' => $tipo,
        'note' => $nota,
        'action_source' => 'portal-ee',
        'user_id' => $responsavel->id,
        'remote_ip' => request()->ip(),
        'user_agent' => request()->header('User-Agent'),
        'created_at' => now(),
    ]);
}

protected function registarHistoricoPreferencia($utente, $responsavel, $campo, $valorAntigo, $valorNovo)
{
    if (!Schema::hasTable('responsaveis_historico')) {
        return;
    }

    if ($valorAntigo == $valorNovo) {
        return; // Nada a registar
    }

    try {
        \DB::table('responsaveis_historico')->insert([
            'utente_id'        => $utente->id,
            'responsavel_id'   => $responsavel->id,
            'campo'            => $campo,
            'valor_antigo'     => $valorAntigo,
            'valor_novo'       => $valorNovo,
            'alterado_por_ee'  => true,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
        Log::info("[HISTORICO] Registo efetuado: {$campo}");
    } catch (\Exception $e) {
        Log::error("[HISTORICO] ERRO: " . $e->getMessage());
    }
}


public function enviarQRCodes(Request $request)
{
    $eeId = session('ee_responsavel_id');
    $eePrincipal = Responsavel::with('utentes')->findOrFail($eeId);

    // A lógica de verificação de e-mail do EE principal pode ser mantida se desejar
    // que ele sempre receba, mas não é estritamente necessário para enviar aos outros.
    // if (!$eePrincipal->email) {
    //     if ($request->ajax() || $request->wantsJson()) {
    //         return response()->json(['success' => false, 'message' => 'O Encarregado de Educação solicitante não tem e-mail definido.'], 422);
    //     }
    //     return back()->with('erro', 'O Encarregado de Educação solicitante não tem e-mail definido.');
    // }

    $utentesDoEE = $eePrincipal->utentes;

    if ($utentesDoEE->isEmpty()) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Não foram encontrados utentes associados a este Encarregado de Educação.'], 422);
        }
        return back()->with('erro', 'Não foram encontrados utentes associados a este Encarregado de Educação.');
    }

    $anexos = [];
$utentesComFicheiro = [];

foreach ($utentesDoEE as $utente) {
    $uuid = $utente->id;
    $url = route('confirmar.check', ['id' => $uuid]);
    $qrCodeImageObject = QrCode::format('png')->size(300)->generate($url);

    $qrCodeImageData = ($qrCodeImageObject instanceof HtmlString)
        ? (string) $qrCodeImageObject
        : $qrCodeImageObject;

    if (!is_string($qrCodeImageData) || empty($qrCodeImageData)) {
        Log::error('[EeAuthController - enviarQRCodes] ERRO CRÍTICO: qrCodeImageData inválido para utente ID ' . $uuid);
        return response()->json(['success' => false, 'message' => 'Erro ao gerar QR Code para o utente ' . $utente->name . '.'], 500);
    }

    $nomeLimpio = Str::slug($utente->name);
    $nomeFicheiro = 'QR_Code_' . $uuid . '_' . $nomeLimpio . '.png';

    // Anexar como ficheiro
    $anexos[] = [
        'nome' => $nomeFicheiro,
        'conteudo' => $qrCodeImageData,
        'mime' => 'image/png',
    ];

    // Caminho da foto (se houver)
    $fotoPath = $utente->image
        ? asset('uploads/assets/' . $utente->image)
        : asset('img/anonimocrianca.png');

    // Versão base64 do QR para inline
    $qrBase64 = 'data:image/png;base64,' . base64_encode($qrCodeImageData);

    $utentesComFicheiro[] = [
        'model'     => $utente,
        'ficheiro'  => $nomeFicheiro,
        'qr_base64' => $qrBase64,
        'foto'      => $fotoPath,
    ];
}

    
    if (empty($anexos)) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Não foi possível gerar QR codes para os utentes.'], 422);
        }
        return back()->with('erro', 'Não foi possível gerar QR codes para os utentes.');
    }

    $utenteIds = $utentesDoEE->pluck('id');
    $todosResponsaveisRelevantes = Responsavel::where('ativo', 1)
    ->whereNotNull('email')
    ->where('email', '!=', '')
    ->whereHas('utentes', function ($query) use ($utenteIds) {
        $query->whereIn('assets.id', $utenteIds)
              ->where('responsaveis_utentes.estado_autorizacao', 'Autorizado');
    })
    ->get()
    ->unique('id');


    $emailsDestinatarios = $todosResponsaveisRelevantes->pluck('email')->filter()->unique()->values()->all();

    if (empty($emailsDestinatarios)) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Nenhum destinatário com e-mail válido encontrado.'], 422);
        }
        return back()->with('erro', 'Nenhum destinatário com e-mail válido encontrado para os utentes.');
    }

    Log::info('[EeAuthController - enviarQRCodes] E-mails destinatários para os QR codes: ', $emailsDestinatarios);

    try {
        Mail::to($emailsDestinatarios)
     ->send(new QRCodesUtentesMail($eePrincipal, $anexos, $utentesComFicheiro));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'QR Codes enviados com sucesso!']);
        }
        return back()->with('success', 'QR Codes enviados com sucesso para todos os responsáveis associados.');

    } catch (\Exception $e) {
        Log::error('[EeAuthController - enviarQRCodes] Erro ao enviar e-mail: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Erro ao enviar os QR Codes. Tente novamente mais tarde.'], 500);
        }
        return back()->with('erro', 'Ocorreu um erro ao tentar enviar os QR Codes. Detalhes do erro foram registados.');
    }
}


}