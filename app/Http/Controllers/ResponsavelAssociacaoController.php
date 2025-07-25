<?php

namespace App\Http\Controllers;

use App\Models\Responsavel;
use App\Models\Asset;
use Illuminate\Http\Request;
use App\Models\ResponsavelUtente;
use Intervention\Image\Facades\Image;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class ResponsavelAssociacaoController extends Controller
{
    /**
     * Associa um responsável a um utente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $utenteId
     * @return \Illuminate\Http\Response
     */
    public function createAssociado(Request $request, $utenteId)
{
    // Obter o utente pelo ID
    $utente = Asset::findOrFail($utenteId);

    // Verificar se já existe um Encarregado de Educação (EE) associado ao utente
    $responsavelEE = ResponsavelUtente::where('utente_id', $utente->id)
                                      ->where('tipo_responsavel', 'Encarregado de Educacao')
                                      ->first();

    // Passar o utente e a informação do Encarregado de Educação para a view
    return view('responsaveis.createAssociado', compact('utente', 'responsavelEE'));
}


protected function associarResponsavelAoUtente($responsavel, $utente)
{
    // Associe o responsável ao utente na tabela pivot
    $responsavel->utentes()->attach($utente->id, [
        'data_inicio_autorizacao' => now(),
        'estado_autorizacao' => 'Autorizado', // ou outro valor conforme necessário
        'tipo_responsavel' => 'Encarregado de Educacao', // ou outro valor conforme necessário
        'grau_parentesco' => 'Pai', // Exemplo, pode ser um campo do formulário
    ]);
}


public function showCreateAssociadoForm($utenteId)
{
    // Buscar o Utente pelo ID e obter o nome
    $utente = Asset::find($utenteId);

    // Verificar se o utente foi encontrado
    if (!$utente) {
        return redirect()->route('error.page')->with('error', 'Utente não encontrado');
    }

    // Passar o nome do utente para a view
    return view('responsaveis.createAssociado', [
        'utenteId' => $utenteId,
        'utenteNome' => $utente->name
    ]);
}





public function show($responsavelId)
{
    $responsavel = Responsavel::findOrFail($responsavelId);
    return view('responsaveis.show', compact('responsavel'));
}



public function associar(Request $request, $utenteId)
{
    // Validar dados recebidos
    $validated = $request->validate([
        'nr_identificacao' => 'required|string|max:255',
        'nome_completo' => 'required|string|max:255',
        'grau_parentesco' => 'required|string|max:255',
        'tipo_responsavel' => [
            'required',
            Rule::in(['Encarregado de Educacao', 'Autorizado', 'Autorizado Excecional']),
        ],
        'data_inicio_autorizacao' => 'nullable|date',
        'data_fim_autorizacao' => [
            'nullable',
            'date',
            'after_or_equal:data_inicio_autorizacao',
            'after_or_equal:' . now()->toDateString(),
        ],
        'observacoes' => 'nullable|string',
    ], [
        'data_fim_autorizacao.after_or_equal' => 'A data de Fim da Autorização não pode ser anterior à data de Início da Autorização.',
        'data_fim_autorizacao.after_or_equal:' . now()->toDateString() => 'A data de Fim da Autorização não pode ser igual ou inferior à data atual.',
    ]);

    // Encontrar o utente pelo ID
    $utente = Asset::findOrFail($utenteId);

    // 🔹 **Verificar se já existe um Encarregado de Educação para este utente**
    $encarregadoExistente = DB::table('responsaveis_utentes')
        ->join('responsaveis', 'responsaveis_utentes.responsavel_id', '=', 'responsaveis.id')
        ->where('responsaveis_utentes.utente_id', $utenteId)
        ->where('responsaveis_utentes.tipo_responsavel', 'Encarregado de Educacao')
        ->select('responsaveis.nome_completo', 'responsaveis.nr_identificacao')
        ->first();

    // Se já existe um Encarregado de Educação associado, verificar o tipo de responsável que está sendo associado
    if ($encarregadoExistente && $request->tipo_responsavel == 'Encarregado de Educacao') {
        return redirect()->route('responsaveis.createAssociado', ['utenteId' => $utenteId])
                         ->with('error', "⚠️ O utente já tem um Encarregado de Educação associado: **{$encarregadoExistente->nome_completo}** (NIF: {$encarregadoExistente->nr_identificacao}).")
                         ->with('isEncarregadoDeEducacao', true); // Passa a variável para a view
    }

    // **Procurar o responsável com base no NIF ou criar um novo**
    $responsavel = Responsavel::firstOrCreate(
        ['nr_identificacao' => $request->nr_identificacao],
        [
            'nome_completo' => $request->nome_completo,
            'contacto' => $request->contacto,
            'email' => $request->email,
            'adicionado_por' => Auth::id(),
            'modificado_por' => Auth::id(),
        ]
    );

    // **Atualizar a foto caso seja fornecida**
    if ($request->hasFile('foto')) {
        $foto = $request->file('foto');

        if ($foto->isValid()) {
            // **Remover a foto antiga** (caso haja)
            if ($responsavel->foto && file_exists(public_path($responsavel->foto))) {
                unlink(public_path($responsavel->foto));
            }

            // Gerar um nome único para o arquivo
            $fotoNome = uniqid() . '.jpg';
            $fotoPath = 'uploads/responsaveis/fotos/' . $fotoNome;

            // Processar e salvar a foto
            $image = Image::make($foto)
                ->orientate() // Corrige a orientação da imagem
                ->fit(396, 594) // Ajusta o tamanho da imagem
                ->encode('jpg', 80); // Define o formato e qualidade

            // Salvar no diretório correto
            $image->save(public_path($fotoPath));

            // Atualizar o caminho da foto no responsável
            $responsavel->foto = $fotoPath;
            $responsavel->save(); // Salva as alterações no responsável
        }
    }

    // **Verificar se a associação já existe para evitar duplicação**
    $existeAssociacao = ResponsavelUtente::where('responsavel_id', $responsavel->id)
                                         ->where('utente_id', $utente->id)
                                         ->exists();

    if ($existeAssociacao) {
        return redirect()->route('responsaveis.show', ['responsavelId' => $responsavel->id])
                         ->with('error', 'Este responsável já está associado a esta criança ' . $utente->name . ' sendo que nada foi alterado!')
                         ->withInput();
    }

    // **Associar o responsável ao utente na tabela pivot**
    ResponsavelUtente::create([
        'responsavel_id' => $responsavel->id,
        'utente_id' => $utente->id,
        'grau_parentesco' => $request->grau_parentesco,
        'tipo_responsavel' => $request->tipo_responsavel,
        'data_inicio_autorizacao' => $request->data_inicio_autorizacao,
        'data_fim_autorizacao' => $request->data_fim_autorizacao,
        'observacoes' => $request->observacoes,
    ]);

    // **7️⃣ Recalcular o estado de autorização após as alterações**
    $responsavel = Responsavel::find($responsavel->id);
    $novoEstado = $responsavel->atualizarEstadoAutorizacao($utente->id);

    // **8️⃣ Atualizar o estado na tabela responsaveis_utentes**
    DB::table('responsaveis_utentes')
        ->where('responsavel_id', $responsavel->id)
        ->where('utente_id', $utente->id)
        ->update(['estado_autorizacao' => $novoEstado]);

    // **9️⃣ Registrar no histórico**
    DB::table('responsaveis_historico')->insert([
        'responsavel_id' => $responsavel->id,
        'utente_id' => $utente->id,
        'alterado_por' => Auth::id(), // O id do usuário logado
        'alterado_em' => now(),
        'motivo' => "📌 Associação de **{$utente->name}** atualizada: " . implode(', ', $request->only(['grau_parentesco', 'tipo_responsavel', 'data_inicio_autorizacao', 'data_fim_autorizacao'])),
        'estado_autorizacao' => $novoEstado, // Estado após alteração
    ]);

    // Registrar no histórico chamando o método registarHistorico
    $this->registarHistorico(
        $responsavel->id,
        $utente->id,
        "📌 Associação de **{$utente->name}** atualizada: " . implode(', ', $request->only(['grau_parentesco', 'tipo_responsavel', 'data_inicio_autorizacao', 'data_fim_autorizacao'])),
        $request->grau_parentesco,
        $request->tipo_responsavel,
        $request->data_inicio_autorizacao,
        $request->data_fim_autorizacao,
        $novoEstado
    );

    // **Retornar para a view com sucesso**
    return redirect()->route('responsaveis.show', ['responsavelId' => $responsavel->id])
                     ->with('success', 'Responsável associado com sucesso à criança ' . $utente->name . '!');
}


private function registarHistorico($responsavelId, $utenteId = null, $motivo, $grauParentesco = null, $tipoResponsavel = null, $dataInicioAutorizacao = null, $dataFimAutorizacao = null, $estadoAutorizacao = null)
{
    DB::table('responsaveis_historico')->insert([
        'responsavel_id' => $responsavelId,
        'utente_id' => $utenteId, // Pode ser NULL se for uma alteração apenas do responsável
        'alterado_por' => Auth::id(),
        'alterado_em' => now(),
        'motivo' => $motivo,
        'grau_parentesco' => $grauParentesco, // Grau de parentesco, se fornecido
        'tipo_responsavel' => $tipoResponsavel, // Tipo de responsável, se fornecido
        'data_inicio_autorizacao' => $dataInicioAutorizacao, // Data de início da autorização, se fornecida
        'data_fim_autorizacao' => $dataFimAutorizacao, // Data de fim da autorização, se fornecida
        'estado_autorizacao' => $estadoAutorizacao, // Estado da autorização, se fornecido
    ]);
}




}
