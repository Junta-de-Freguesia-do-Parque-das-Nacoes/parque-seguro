<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Responsavel;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ResponsaveisHistorico;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;


class EeResponsavelController extends Controller
{
    public function atualizarFoto(Request $request, $id)
{
    $responsavelId = session('ee_responsavel_id');
    $responsavel = Responsavel::with('utentes')->findOrFail($responsavelId);

    $target = Responsavel::findOrFail($id);
    $isRelacionado = $target->utentes()
        ->whereIn('assets.id', $responsavel->utentes->pluck('id'))
        ->exists();

    if (!$isRelacionado) {
        return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
    }

    if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
        $imagem = Image::make($request->file('foto')->getRealPath());

        $filename = 'responsavel-image-' . $id . '-' . Str::random(10) . '.jpg';
        $uploadPath = public_path('uploads/responsaveis/fotos'); // <- atualizado para a pasta protegida

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $imagem->fit(300, 400)->save($uploadPath . '/' . $filename, 75);

        $target->foto = 'uploads/responsaveis/fotos/' . $filename;
        $target->save();

        return response()->json([
            'success' => true,
            'url' => route('ee.responsavel.foto', ['filename' => $filename]),
            'message' => '📸 Foto do responsável atualizada com sucesso!'
        ]);
    }

    return response()->json(['success' => false, 'message' => 'Ficheiro inválido.'], 400);
}


    public function verFoto($filename)
{
    $filename = basename($filename);
    $path = public_path('uploads/responsaveis/fotos/' . $filename);

    if (!file_exists($path)) {
        abort(404, 'Ficheiro não encontrado: ' . $filename);
    }

    return response()->file($path);
}



public function atualizarDados(Request $request, $id)
{
    $responsavelId = session('ee_responsavel_id');
    $responsavel = Responsavel::with('utentes')->findOrFail($responsavelId);
    $target = Responsavel::findOrFail($id);

    // Verifica se existe relação entre o responsável autenticado e o responsável a editar
    $isRelacionado = $target->utentes()
        ->whereIn('assets.id', $responsavel->utentes->pluck('id'))
        ->exists();

    if (!$isRelacionado) {
        return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
    }

    // Validação dos dados recebidos
    $validator = Validator::make($request->all(), [
        'email' => 'nullable|email|unique:responsaveis,email,' . $id,
        'nr_identificacao' => 'nullable|unique:responsaveis,nr_identificacao,' . $id,
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    // Detectar alterações
    $camposAlterados = [];
    $campos = ['nome_completo', 'email', 'contacto', 'nr_identificacao'];

    foreach ($campos as $campo) {
        $valorAntigo = $target->$campo;
        $valorNovo = $request->$campo;

        if ($valorAntigo != $valorNovo) {
            $camposAlterados[] = ucfirst(str_replace('_', ' ', $campo)) . ": \"$valorAntigo\" → \"$valorNovo\"";
            $target->$campo = $valorNovo;
        }
    }

    $target->save();

    // Notificação Backoffice (mantém mesmo sem histórico)
    if (!empty($camposAlterados)) {
        foreach ($responsavel->utentes as $utente) {
            if ($target->utentes->pluck('id')->contains($utente->id)) {
                \App\Models\NotificacaoBackoffice::create([
                    'tipo' => 'responsavel_atualizado',
                    'asset_id' => $utente->id,
                    'mensagem' => "📝 <strong>EE {$responsavel->nome_completo}</strong> atualizou dados do responsável <strong>{$target->nome_completo}</strong>:<br>" . implode('<br>', $camposAlterados),
                ])->utilizadores()->attach(
                    \App\Models\User::whereHas('groups', function ($q) {
                        $q->whereIn('name', ['NSI', 'COORDENADORES AAAF CAF']);
                    })->pluck('id')->toArray(),
                    ['lida' => false]
                );
            }
        }
    }

    return response()->json([
        'success' => true,
        'nome_completo' => $target->nome_completo,
        'email' => $target->email,
        'contacto' => $target->contacto,
        'nr_identificacao' => $target->nr_identificacao
    ]);
}




public static function calcularEstado($dataInicio, $dataFim)
{
    $hoje = \Carbon\Carbon::now()->startOfDay();
    $inicio = $dataInicio ? \Carbon\Carbon::parse($dataInicio)->startOfDay() : null;
    $fim = $dataFim ? \Carbon\Carbon::parse($dataFim)->startOfDay() : null;

    $estado = 'Não autorizado';
    $icone = '❌';
    $cor = 'red';

    if ((!$inicio || $inicio->lte($hoje)) && (!$fim || $fim->gte($hoje))) {
        $estado = 'Autorizado';
        $icone = '✅';
        $cor = 'green';
    } elseif ($inicio && $inicio->gt($hoje)) {
        $estado = 'Pendente';
        $icone = '⏳';
        $cor = 'orange';
    }

    return [$estado, $cor, $icone];
}



public function atualizarAssociacao(Request $request)
{
    try {
        \Log::info('Recebido pedido de atualização de associação', $request->all());

        // Validação
        $request->validate([
            'id' => 'required|integer|exists:responsaveis_utentes,id',
            'grau_parentesco' => 'required|string|max:50',
            'data_inicio_autorizacao' => 'nullable|date',
            'data_fim_autorizacao' => 'nullable|date|after_or_equal:data_inicio_autorizacao',
            'observacoes' => 'nullable|string',
            'dias_nao_autorizados' => 'nullable|string|max:255',
        ]);

        $responsavelId = session('ee_responsavel_id');
        $linha = DB::table('responsaveis_utentes')->where('id', $request->id)->first();
        if (!$linha) return response()->json(['success' => false, 'message' => 'Associação não encontrada.'], 404);

        $temLigacao = DB::table('responsaveis_utentes')
            ->where('responsavel_id', $responsavelId)
            ->where('utente_id', $linha->utente_id)
            ->exists();
        if (!$temLigacao) return response()->json(['success' => false, 'message' => 'Associação não autorizada.'], 403);

        // Cálculo do estado
        $hoje = now()->startOfDay();
        $inicio = $request->data_inicio_autorizacao ? Carbon::parse($request->data_inicio_autorizacao)->startOfDay() : null;
        $fim = $request->data_fim_autorizacao ? Carbon::parse($request->data_fim_autorizacao)->startOfDay() : null;
        $estado = (!$inicio || $inicio->lte($hoje)) && (!$fim || $fim->gte($hoje)) ? 'Autorizado'
            : ($inicio && $inicio->gt($hoje) ? 'Nao Iniciado' : 'Autorizacao Expirada');

        // Novos valores
        $novosValores = [
            'grau_parentesco' => $request->grau_parentesco,
            'tipo_responsavel' => $request->tipo_responsavel,
            'data_inicio_autorizacao' => $request->data_inicio_autorizacao,
            'data_fim_autorizacao' => $request->data_fim_autorizacao,
            'observacoes' => $request->observacoes,
            'estado_autorizacao' => $estado,
            'dias_nao_autorizados' => $request->dias_nao_autorizados,
        ];

        // Verificar alterações
        $campos = array_keys($novosValores);
        $alteracoes = [];
        foreach ($campos as $campo) {
    $antigo = $linha->$campo ?? '';
    $novo = $novosValores[$campo] ?? '';
    if (in_array($campo, ['data_inicio_autorizacao', 'data_fim_autorizacao'])) {
        $antigo = $antigo ? Carbon::parse($antigo)->format('Y-m-d') : '';
        $novo = $novo ? Carbon::parse($novo)->format('Y-m-d') : '';
    }
    $antigoFmt = ($antigo !== null && $antigo !== '') ? $antigo : '—';
    $novoFmt   = ($novo !== null && $novo !== '') ? $novo : '—';

    if ($antigoFmt != $novoFmt) {
        $alteracoes[] = ucfirst(str_replace('_', ' ', $campo)) . ": '$antigoFmt' → '$novoFmt'";
    }
}

// Sempre adicionar o estado atual no fim
$alteracoes[] = "Estado atual: '<strong>{$estado}</strong>'";

        // Atualiza registo
        DB::table('responsaveis_utentes')->where('id', $request->id)->update(array_merge($novosValores, [
            'updated_at' => now(),
        ]));

        // Histórico
        $responsavel = Responsavel::find($linha->responsavel_id);
$eeResponsavel = Responsavel::find($responsavelId);

        DB::table('responsaveis_historico')->insert([
            'responsavel_id' => $linha->responsavel_id,
            'utente_id' => $linha->utente_id,
            'nome_completo' => $responsavel?->nome_completo,
            'nr_identificacao' => $responsavel?->nr_identificacao,
            'contacto' => $responsavel?->contacto,
            'email' => $responsavel?->email,
            'tipo_responsavel' => $request->tipo_responsavel,
            'grau_parentesco' => $request->grau_parentesco,
            'data_inicio_autorizacao' => $request->data_inicio_autorizacao,
            'data_fim_autorizacao' => $request->data_fim_autorizacao,
            'estado_autorizacao' => $estado,
            'alterado_por_ee' => $responsavelId,
            'alterado_em' => now(),
            'motivo' => $alteracoes ? implode("\n", $alteracoes) : 'Atualização manual pelo EE',
        ]);

        // 🔔 Notificação
        if ($alteracoes) {
    $utente = Asset::find($linha->utente_id);
    $mensagem = "🔧 <strong>EE {$eeResponsavel->nome_completo}</strong> atualizou a associação do responsável <strong>{$responsavel->nome_completo}</strong> com o utente <strong>{$utente->name}</strong>:<br>" . implode('<br>', $alteracoes);

    \App\Models\NotificacaoBackoffice::create([
        'tipo' => 'alteracao_associacao',
        'asset_id' => $utente->id,
        'mensagem' => $mensagem,
    ])->utilizadores()->attach(
        \App\Models\User::whereHas('groups', function ($q) {
            $q->whereIn('name', ['NSI', 'COORDENADORES AAAF CAF']);
        })->pluck('id')->toArray(),
        ['lida' => false]
    );
}


        return response()->json(['success' => true]);

    } catch (\Throwable $e) {
        \Log::error('Erro ao atualizar associação: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}




public function removerResponsavel(Request $request, $id)
{
    $responsavel = Responsavel::findOrFail($id);
    $eeResponsavelId = session('ee_responsavel_id');
    $utenteId = $request->input('utente_id');

    // ✅ Verificação mínima
    if (!$utenteId) {
        return response()->json(['error' => 'Utente não especificado.'], 400);
    }

    // ✅ Verificar se o EE é efetivamente o Encarregado de Educação do utente
    $ehEE = DB::table('responsaveis_utentes')
        ->where('responsavel_id', $eeResponsavelId)
        ->where('utente_id', $utenteId)
        ->where('tipo_responsavel', 'Encarregado de Educação')
        ->exists();

    if (!$ehEE) {
        return response()->json(['error' => 'Não tem permissão para remover este responsável deste utente.'], 403);
    }

    // 🔔 Notificação de remoção
    $nomeResponsavel = $responsavel->nome_completo;
    $nomeEE = Responsavel::find($eeResponsavelId)?->nome_completo ?? 'EE desconhecido';
    $utente = Asset::find($utenteId);

    if ($utente) {
        \App\Models\NotificacaoBackoffice::create([
            'tipo' => 'remocao_responsavel',
            'asset_id' => $utente->id,
            'mensagem' => "❌ <strong>EE {$nomeEE}</strong> removeu o responsável <strong>{$nomeResponsavel}</strong> do utente <strong>{$utente->name}</strong>.",
        ])->utilizadores()->attach(
            \App\Models\User::whereHas('groups', function ($q) {
                $q->whereIn('name', ['admin', 'backoffice', 'secretaria', 'NSI', 'COORDENADORES AAAF CAF']);
            })->pluck('id')->toArray(),
            ['lida' => false]
        );
    }

    // ✅ Remove apenas a associação entre este responsável e este utente
    DB::table('responsaveis_utentes')
        ->where('responsavel_id', $id)
        ->where('utente_id', $utenteId)
        ->delete();

    // 🔘 Se já não tiver nenhuma associação, marca como inativo
    $aindaTemAssociacoes = DB::table('responsaveis_utentes')
        ->where('responsavel_id', $id)
        ->exists();

    if (!$aindaTemAssociacoes) {
        $responsavel->ativo = false;
        $responsavel->modificado_em = now();
        $responsavel->modificado_por = auth()->id() ?? null;
        $responsavel->save();
    }

    return response()->json(['success' => true]);
}


public function criarGlobal(Request $request): JsonResponse
{
    try {
        if ($request->has('foto') && !$request->hasFile('foto')) {
            return response()->json([
                'success' => false,
                'errors'  => ['foto' => ['A imagem excede o tamanho máximo permitido (10MB).']]
            ], 422);
        }

        $request->validate([
            'nr_identificacao' => 'required|string|max:50',
            'nome_completo'    => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'contacto'         => 'nullable|string|max:255',
            'foto'             => 'nullable|image|max:10240',
        ], [
            'nr_identificacao.required' => 'O número de identificação é obrigatório.',
            'nome_completo.required'    => 'O nome completo é obrigatório.',
            'email.email'               => 'O endereço de email não é válido.',
            'foto.image'                => 'O ficheiro selecionado não é uma imagem válida.',
            'foto.max'                  => 'A imagem não pode ter mais de 10MB.',
        ]);

        $responsavel = \App\Models\Responsavel::withTrashed()
            ->where('nr_identificacao', $request->nr_identificacao)
            ->first();

        if ($responsavel) {
            $responsavel->ativo = true;
            $responsavel->deleted_at = null;
            $responsavel->save();

            $responsavel->update(array_filter([
                'nome_completo' => $request->nome_completo,
                'email'         => $request->email,
                'contacto'      => $request->contacto,
            ]));
        } else {
            $responsavel = \App\Models\Responsavel::create([
                'nr_identificacao' => $request->nr_identificacao,
                'nome_completo'    => $request->nome_completo,
                'email'            => $request->email,
                'contacto'         => $request->contacto,
                'ativo'            => true,
                'adicionado_por'   => auth()->id() ?? session('ee_responsavel_id'),
                'adicionado_em'    => now(),
            ]);
        }

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $nomeFicheiro = 'responsavel-image-' . $responsavel->id . '-' . uniqid() . '.jpg';
            $destino = public_path('uploads/responsaveis/fotos');

            if (!file_exists($destino)) {
                mkdir($destino, 0775, true);
            }

            Image::make($foto->getRealPath())
                ->resize(600, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('jpg', 80)
                ->save($destino . '/' . $nomeFicheiro);

            $responsavel->foto = 'uploads/responsaveis/fotos/' . $nomeFicheiro;
            $responsavel->save();
        }

        $responsavelIdEE = session('ee_responsavel_id');

        // Associa aos utentes do EE
        $utentes = DB::table('responsaveis_utentes')
            ->where('responsavel_id', $responsavelIdEE)
            ->where('tipo_responsavel', 'Encarregado de Educação')
            ->pluck('utente_id');

        foreach ($utentes as $utenteId) {
            DB::table('responsaveis_utentes')->updateOrInsert(
                [
                    'responsavel_id' => $responsavel->id,
                    'utente_id'      => $utenteId,
                ],
                [
                    'estado_autorizacao'      => 'Nao Autorizado',
                    'tipo_responsavel'        => 'Outro',
                    'grau_parentesco'         => 'Outro',
                    'data_inicio_autorizacao' => null,
                    'data_fim_autorizacao'    => null,
                    'observacoes'             => null,
                ]
            );
        }

        $responsavel->load(['utentes' => function ($q) {
            $q->withPivot([
                'grau_parentesco',
                'tipo_responsavel',
                'data_inicio_autorizacao',
                'data_fim_autorizacao',
                'estado_autorizacao',
                'observacoes'
            ]);
        }]);

        // ✅ Notificação única sem mencionar utente
        $nomeResponsavel = $responsavel->nome_completo;
        $nomeEE = \App\Models\Responsavel::find($responsavelIdEE)?->nome_completo ?? 'EE desconhecido';
        $assetIdParaNotificacao = $utentes->first();
        \App\Models\NotificacaoBackoffice::create([
            'tipo' => 'novo_responsavel',
            'asset_id' => $assetIdParaNotificacao,
            'mensagem' => "➕ <strong>EE {$nomeEE}</strong> adicionou o responsável <strong>{$nomeResponsavel}</strong>.",
        ])->utilizadores()->attach(
            \App\Models\User::whereHas('groups', function ($q) {
                $q->whereIn('name', ['admin', 'backoffice', 'secretaria', 'NSI', 'COORDENADORES AAAF CAF']);
            })->pluck('id')->toArray(),
            ['lida' => false]
        );

        return response()->json([
            'success'    => true,
            'message'    => 'Responsável criado e associado com sucesso.',
            'responsavel'=> [
                'id'             => $responsavel->id,
                'nome_completo'  => $responsavel->nome_completo,
                'nr_identificacao' => $responsavel->nr_identificacao,
                'email'          => $responsavel->email,
                'contacto'       => $responsavel->contacto,
                'foto_url'       => $responsavel->foto
                    ? route('ee.responsavel.foto', ['filename' => basename($responsavel->foto)])
                    : null,
                'utentes'        => $responsavel->utentes->map(function ($utente) {
                    return [
                        'id'     => $utente->id,
                        'name'   => $utente->name,
                        'image'  => $utente->image ? route('ee.utente.foto', ['filename' => $utente->image]) : null,
                        'pivot'  => $utente->pivot->toArray(),
                    ];
                }),
            ]
        ]);

    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors'  => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Erro no criarGlobal:', [
            'mensagem' => $e->getMessage(),
            'linha'    => $e->getLine(),
            'ficheiro'=> $e->getFile(),
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Erro inesperado ao criar o responsável.'
        ], 500);
    }
}




}
