<?php

namespace App\Http\Controllers;

use App\Models\Responsavel;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Documento;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Models\NotaResponsavel;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;


class ResponsavelController extends Controller
{
    // Listar responsáveis de um utente
    public function index($utenteId)
    {
        $utente = Asset::findOrFail($utenteId);
        
        // Buscar responsáveis e incluir os campos da tabela pivot responsaveis_utentes
        $responsaveis = Responsavel::join('responsaveis_utentes', 'responsaveis.id', '=', 'responsaveis_utentes.responsavel_id')
            ->where('responsaveis_utentes.utente_id', $utenteId)
            ->with(['adicionadoPor', 'modificadoPor']) // 🔥 Relacionamentos carregados
            ->select(
                'responsaveis.*',
                'responsaveis_utentes.tipo_responsavel AS responsavel_tipo',
                'responsaveis_utentes.estado_autorizacao AS autorizacao_estado', // ✅ Renomeado para evitar conflito
                'responsaveis_utentes.grau_parentesco',
                'responsaveis_utentes.data_inicio_autorizacao',
                'responsaveis_utentes.data_fim_autorizacao'
            )
            ->distinct()  // Verifique se a duplicação ocorre; pode não ser necessário dependendo do relacionamento
            ->get();
        
        // Passando os dados para a view
        return view('responsaveis.index', compact('utente', 'responsaveis'));
    }
    
 




    






    // Exibir formulário de criação
    public function create()
    {
        return view('responsaveis.create');
    }
    

    // Criar novo responsável

    public function storeNovoResponsavel(Request $request)
{
    DB::beginTransaction(); // 🔒 Iniciar transação para evitar inserções erradas

    try {
        // **1️⃣ Validação**
        $validated = $request->validate([
            'nr_identificacao' => 'required|string|max:50',
            'nome_completo' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'documento' => 'nullable|file|mimes:pdf,doc,docx,txt,png,jpg,jpeg|max:51200',
        ]);

        // **2️⃣ Verificar se o responsável já existe**
        $responsavelExistente = Responsavel::where('nr_identificacao', $request->nr_identificacao)->first();

        if ($responsavelExistente) {
            return redirect()->back()->withErrors([
                'nr_identificacao' => 'Este responsável já está registado no sistema.',
            ])->withInput();
        }

        // **3️⃣ Criar novo responsável**
        $responsavel = new Responsavel();
        $responsavel->nome_completo = $request->nome_completo;
        $responsavel->nr_identificacao = $request->nr_identificacao;
        $responsavel->contacto = $request->contacto;
        $responsavel->email = $request->email;
        $responsavel->adicionado_por = auth()->user()->id;
        
        $historicoMotivo = "Novo responsável criado: {$request->nome_completo} (NIF: {$request->nr_identificacao})";

        // **4️⃣ Guardar Foto (se existir)**
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            if ($foto->isValid()) {
                $fotoPath = 'uploads/responsaveis/fotos/' . uniqid() . '.webp';
                $image = Image::make($foto->getRealPath())
                    ->fit(396, 594)
                    ->encode('webp', 80);

                $image->save(public_path($fotoPath));
                $responsavel->foto = $fotoPath;

                // Adiciona ao histórico
                $historicoMotivo .= ", Foto adicionada ({$fotoPath})";
            }
        }

        $responsavel->save();

        // **5️⃣ Guardar Documento (se existir)**
        if ($request->hasFile('documento')) {
            $documento = $request->file('documento');
            if ($documento->isValid()) {
                $documentoPath = 'uploads/responsaveis/documentos/' . uniqid() . '.' . $documento->getClientOriginalExtension();
                $documento->move(public_path('uploads/responsaveis/documentos'), $documentoPath);

                Documento::create([
                    'responsavel_id' => $responsavel->id,
                    'path' => $documentoPath
                ]);

                // Adiciona ao histórico
                $historicoMotivo .= ", Documento adicionado ({$documentoPath})";
            }
        }

        // **6️⃣ Registar no Histórico**
        $this->registarHistorico($responsavel->id, null, $historicoMotivo);

        DB::commit(); // ✅ Confirmar as alterações

        return redirect()->route('responsaveis.show', ['responsavelId' => $responsavel->id])
                         ->with('success', 'Responsável criado com sucesso!');

    } catch (\Exception $e) {
        DB::rollback(); // ❌ Reverter alterações se houver erro
        return redirect()->back()->withErrors(['erro' => 'Ocorreu um erro ao salvar o responsável.']);
    }
}

    








    public function store(Request $request)
{

    
    // **Validação**
    $validated = $request->validate([
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:51200',
        'documento' => 'nullable|file|mimes:pdf,doc,docx,txt,png,jpg,jpeg|max:51200',
        'nr_identificacao' => 'required',
        'tipo_responsavel' => 'required|in:Encarregado de Educacao,Autorizado,Autorizado Excecional'
    ]);

    DB::beginTransaction(); // **🔒 Iniciar transação**

    try {
        // **1️⃣ Verificar se a criança já tem um Encarregado de Educação**
        if ($request->tipo_responsavel === 'Encarregado de Educacao') {
            $jaTemEE = DB::table('responsaveis_utentes')
                ->join('responsaveis', 'responsaveis_utentes.responsavel_id', '=', 'responsaveis.id')
                ->where('responsaveis_utentes.utente_id', $request->utente_id)
                ->where('responsaveis.tipo_responsavel', 'Encarregado de Educacao')
                ->exists();

            if ($jaTemEE) {
                return redirect()->back()->withErrors([
                    'tipo_responsavel' => 'Esta criança já possui um Encarregado de Educação.'
                ]);
            }
        }

        // **2️⃣ Verificar se o responsável já existe**
        $responsavelExistente = Responsavel::where('nr_identificacao', $request->nr_identificacao)->first();

        if ($responsavelExistente) {
            // **3️⃣ Verificar se já está associado ao mesmo utente**
            $jaAssociado = DB::table('responsaveis_utentes')
                ->where('responsavel_id', $responsavelExistente->id)
                ->where('utente_id', $request->utente_id)
                ->exists();

            if ($jaAssociado) {
                return redirect()->back()->withErrors([
                    'nr_identificacao' => 'Este responsável já está associado a este utente.',
                ]);
            }

            // **4️⃣ Criar nova relação**
            DB::table('responsaveis_utentes')->insert([
                'responsavel_id' => $responsavelExistente->id,
                'utente_id' => $request->utente_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit(); // **✅ Confirma a transação**

            return redirect()->route('responsaveis.show', ['utenteId' => $request->utente_id])
                             ->with('success', 'Responsável existente associado com sucesso.');
        }

        // **5️⃣ Criar novo responsável**
        $responsavel = new Responsavel();
        $responsavel->nome_completo = $request->nome_completo;
        $responsavel->nr_identificacao = $request->nr_identificacao;
        $responsavel->contacto = $request->contacto;
        $responsavel->email = $request->email;
        $responsavel->tipo_responsavel = $request->tipo_responsavel;
        $responsavel->grau_parentesco = $request->grau_parentesco;
        $responsavel->data_inicio_autorizacao = $request->data_inicio_autorizacao;
        $responsavel->data_fim_autorizacao = $request->data_fim_autorizacao;
        $responsavel->adicionado_por = auth()->user()->id;

        // **6️⃣ Processamento da Foto**
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');

            if ($foto->isValid()) {
                $fotoPath = 'uploads/responsaveis/fotos/' . uniqid() . '.webp';
                $image = Image::make($foto->getRealPath())
                    ->fit(396, 594)
                    ->encode('webp', 80);

                $image->save(public_path($fotoPath));
                $responsavel->foto = $fotoPath;
            }
        }

        $responsavel->save();

        // **7️⃣ Criar a relação entre o novo responsável e o utente**
        DB::table('responsaveis_utentes')->insert([
            'responsavel_id' => $responsavel->id,
            'utente_id' => $request->utente_id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // **8️⃣ Salvar Documento**
        if ($request->hasFile('documento')) {
            $documento = $request->file('documento');

            if ($documento->isValid()) {
                $documentoPath = 'uploads/responsaveis/documentos/' . uniqid() . '.' . $documento->getClientOriginalExtension();
                $documento->move(public_path('uploads/responsaveis/documentos'), $documentoPath);

                Documento::create([
                    'responsavel_id' => $responsavel->id,
                    'path' => $documentoPath
                ]);
            }
        }

        DB::commit(); // **✅ Confirma a transação**

        return redirect()->route('responsaveis.index', ['utenteId' => $request->utente_id])
                         ->with('success', 'Responsável criado com sucesso!');

    } catch (\Exception $e) {
        DB::rollback(); // ❌ **Se houver erro, reverte tudo**
        return redirect()->back()->withErrors(['erro' => 'Ocorreu um erro ao salvar.']);
    }
}



    


    // Exibir formulário de edição
    public function edit($responsavelId)
    {
        // Buscar o responsável pelo ID
        $responsavel = Responsavel::findOrFail($responsavelId);
    
        return view('responsaveis.edit', compact('responsavel'));
    }
    
    

// Atualizar responsável sem depender do utente
public function update(Request $request, $responsavelId)
{
    // Buscar o responsável pelo ID
    $responsavel = Responsavel::findOrFail($responsavelId);
    $dadosAntigos = $responsavel->toArray();

    // **Verificar se o novo NIF já existe para outro responsável**
    $nifExistente = Responsavel::where('nr_identificacao', $request->nr_identificacao)
        ->where('id', '!=', $responsavelId) // Ignorar o próprio responsável em edição
        ->first();

    if ($nifExistente) {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Já existe um responsável com este NIF.',
            ], 422);
        }
        return redirect()->back()->withErrors([
            'nr_identificacao' => 'Já existe um responsável com este NIF.',
        ])->withInput();
    }

    // **Validar os dados**
    $validated = $request->validate([
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
        'documento' => 'nullable|file|mimes:pdf,doc,docx,txt,png,jpg,jpeg|max:20480',
    ]);

    // **Atualizar informações gerais**
    $responsavel->update([
        'nome_completo' => $request->nome_completo,
        'nr_identificacao' => $request->nr_identificacao,
        'contacto' => $request->contacto,
        'email' => $request->email,
        'modificado_por' => auth()->id(),
        'modificado_em' => now()
    ]);

    $alteracoes = [];

    // **Comparar valores antigos com novos para salvar no histórico**
    foreach ($responsavel->getChanges() as $campo => $novoValor) {
        if (isset($dadosAntigos[$campo]) && $dadosAntigos[$campo] !== $novoValor) {
            // **Ignorar campos de atualização automática**
            if (in_array($campo, ['updated_at', 'modificado_em'])) {
                continue;
            }

            $alteracoes[] = ucfirst(str_replace('_', ' ', $campo)) . ": '{$dadosAntigos[$campo]}' → '$novoValor'";
        }
    }

    // **Atualizar a foto**
    if ($request->hasFile('foto')) {
        $foto = $request->file('foto');

        if ($foto->isValid()) {
            // **Remover a foto antiga**
            if ($responsavel->foto && file_exists(public_path($responsavel->foto))) {
                unlink(public_path($responsavel->foto));
                $alteracoes[] = "Foto removida ('{$responsavel->foto}')";
            }

            $fotoNome = uniqid() . '.jpg';
            $fotoPath = 'uploads/responsaveis/fotos/' . $fotoNome;

            $image = Image::make($foto)
                ->orientate()
                ->fit(396, 594)
                ->encode('jpg', 80);

            $image->save(public_path($fotoPath));

            $responsavel->foto = $fotoPath;
            $alteracoes[] = "Foto adicionada ($fotoPath)";
        }
    }

    // **Atualizar documento**
    if ($request->hasFile('documento')) {
        $documento = $request->file('documento');

        if ($documento->isValid()) {
            $documentoPath = 'uploads/responsaveis/documentos/' . uniqid() . '.' . $documento->getClientOriginalExtension();
            $documento->move(public_path('uploads/responsaveis/documentos'), $documentoPath);

            Documento::create([
                'responsavel_id' => $responsavel->id,
                'path' => $documentoPath
            ]);

            $alteracoes[] = "Documento adicionado ($documentoPath)";
        }
    }

    // **Salvar todas as alterações**
    $responsavel->save();

    // 🔹 Regista a alteração no histórico se houver mudanças
    if (!empty($alteracoes)) {
        $this->registarHistorico($responsavel->id, null, "Responsável atualizado: " . implode(", ", $alteracoes));
    }

    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Responsável atualizado com sucesso!'
        ]);
    }

    return redirect()->route('responsaveis.show', ['responsavelId' => $responsavelId])
        ->with('success', 'Responsável atualizado com sucesso.');
}


    // Remover Documento revisto 11-02-2025
    public function removeDocumento($responsavelId, $documentoId)
{
    try {
        // ✅ Verifica se o documento pertence ao responsável
        $documento = Documento::where('responsavel_id', $responsavelId)
            ->where('id', $documentoId)
            ->first();

        // ❌ Documento não encontrado para este responsável
        if (!$documento) {
            return response()->json([
                'success' => false,
                'message' => 'Documento não encontrado ou não pertence a este responsável.'
            ], 404);
        }

        // 🗑️ Capturar o nome do documento antes de remover
        $nomeFicheiro = basename($documento->path);

        // Remover o arquivo do servidor, se existir
        if ($documento->path && file_exists(public_path($documento->path))) {
            unlink(public_path($documento->path));
        }

        // ❌ Remover do banco de dados
        $documento->delete();

        // 🔹 Registar no histórico apenas o nome do ficheiro
        $this->registarHistorico($responsavelId, null, "Documento removido: $nomeFicheiro");

        return response()->json([
            'success' => true,
            'message' => 'Documento removido com sucesso!'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao remover o documento.',
            'error' => $e->getMessage()
        ], 500);
    }
}

    


    // Exibir histórico
    public function historico($utenteId, $responsavelId)
{
    $responsavel = Responsavel::findOrFail($responsavelId);
    
    $historico = DB::table('responsaveis_historico')
        ->leftJoin('users', 'responsaveis_historico.alterado_por', '=', 'users.id')
        ->select('responsaveis_historico.*', 'users.first_name', 'users.last_name', 'users.name as user_name')
        ->where('responsavel_id', $responsavelId)
        ->orderBy('alterado_em', 'desc')
        ->get();

    return view('responsaveis.historico', compact('responsavel', 'historico', 'utenteId'));
}


    // Exibir responsável com histórico completo
    public function show($responsavelId)
{
    // Busca o responsável e carrega os utentes associados
    $responsavel = Responsavel::with('utentes')->findOrFail($responsavelId);

    // Obtém o primeiro utente associado, se houver
    $utenteId = optional($responsavel->utentes->first())->id;

    // Buscar todas as crianças associadas ao responsável
    $criancasAssociadas = DB::table('responsaveis_utentes')
        ->join('assets', 'responsaveis_utentes.utente_id', '=', 'assets.id')
        ->where('responsaveis_utentes.responsavel_id', $responsavelId)
        ->select(
            'responsaveis_utentes.id',
            'assets.id as utente_id',
            'assets.name',
            'responsaveis_utentes.grau_parentesco',
            'responsaveis_utentes.tipo_responsavel',
            'responsaveis_utentes.data_inicio_autorizacao',
            'responsaveis_utentes.data_fim_autorizacao',
            'responsaveis_utentes.estado_autorizacao',
            'responsaveis_utentes.observacoes',
            'responsaveis_utentes.dias_nao_autorizados'

        )
        ->get();

    // Buscar histórico de alterações
    $historicoCompleto = DB::table('responsaveis_historico')
    ->leftJoin('users', 'responsaveis_historico.alterado_por', '=', 'users.id')
    ->leftJoin('assets', 'responsaveis_historico.utente_id', '=', 'assets.id')
    ->leftJoin('responsaveis', 'responsaveis_historico.responsavel_id', '=', 'responsaveis.id')
    ->leftJoin('responsaveis as ee', 'responsaveis_historico.alterado_por_ee', '=', 'ee.id')
    ->select(
        'responsaveis_historico.*',
        'users.first_name',
        'users.last_name',
        'assets.name as nome_utente',
        'responsaveis.nome_completo as nome_responsavel',
        'ee.nome_completo as ee_nome',
        'ee.nr_identificacao as ee_nif'
    )
    ->where('responsaveis_historico.responsavel_id', $responsavelId)
    ->orderBy('alterado_em', 'desc')
    ->get();


    return view('responsaveis.show', compact('responsavel', 'utenteId', 'criancasAssociadas', 'historicoCompleto'));
}



public function buscarResponsavel(Request $request)
{
    if (!$request->has('nr_identificacao')) {
        return response()->json(['success' => false, 'message' => 'Parâmetro inválido.'], 400);
    }

    $nrIdentificacao = $request->nr_identificacao;

    // Usa LIKE para pesquisar qualquer parte do número de identificação
    $responsaveis = Responsavel::where('nr_identificacao', 'LIKE', "%$nrIdentificacao%")
        ->limit(5) // Limita os resultados para evitar sobrecarga
        ->get(['id', 'nome_completo', 'nr_identificacao', 'contacto', 'email', 'foto']);

    return response()->json([
        'success' => true,
        'data' => $responsaveis
    ]);
}

public function removerCompletamente($responsavelId)
{
    \Log::info("🚀 Tentando remover completamente o responsável ID: $responsavelId");

    $responsavel = Responsavel::find($responsavelId);

    if (!$responsavel) {
        \Log::error("⚠️ Responsável ID $responsavelId não encontrado.");
        return redirect()->back()->withErrors(['erro' => 'Responsável não encontrado.']);
    }

    try {
        DB::beginTransaction();

        // 🔹 **Remover histórico primeiro** (para evitar violação da chave estrangeira)
        DB::table('responsaveis_historico')->where('responsavel_id', $responsavelId)->delete();
        \Log::info("✅ Registos do histórico removidos.");

        // 🗑 Remover todas as associações do responsável
        DB::table('responsaveis_utentes')->where('responsavel_id', $responsavelId)->delete();
        \Log::info("✅ Associações removidas.");

        // 🗑 Remover todos os documentos associados
        $documentos = Documento::where('responsavel_id', $responsavelId)->get();
        foreach ($documentos as $documento) {
            if (file_exists(public_path($documento->path))) {
                unlink(public_path($documento->path)); // Apagar ficheiro do servidor
            }
            $documento->delete();
        }
        \Log::info("✅ Documentos removidos.");

        // 🗑 Remover a foto do responsável, se existir
        if ($responsavel->foto && file_exists(public_path($responsavel->foto))) {
            unlink(public_path($responsavel->foto)); // Apagar ficheiro do servidor
            \Log::info("✅ Foto removida.");
        }

        // 🗑 Remover responsável
        if ($responsavel->delete()) {
            \Log::info("✅ Responsável {$responsavel->nome_completo} removido!");
        } else {
            throw new \Exception("Erro ao remover responsável.");
        }

        DB::commit();
        return redirect()->route('responsaveis.listar')->with('success', 'Responsável removido.');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("❌ Erro ao remover responsável: " . $e->getMessage());
        return redirect()->back()->withErrors(['erro' => 'Erro ao remover responsável.']);
    }
}


    
public function destroy($utenteId, $responsavelId)
{
    $associacoes = DB::table('responsaveis_utentes')
        ->where('responsavel_id', $responsavelId)
        ->count();

    if ($associacoes > 1) {
        // Apenas remover a associação com esta criança
        DB::table('responsaveis_utentes')
            ->where('responsavel_id', $responsavelId)
            ->where('utente_id', $utenteId)
            ->delete();

        return redirect()->route('responsaveis.index', ['utenteId' => $utenteId])
                         ->with('success', 'Associação removida com sucesso.');
    } else {
        // Remover completamente o responsável
        $responsavel = Responsavel::findOrFail($responsavelId);
        $responsavel->delete();

        return redirect()->route('responsaveis.index', ['utenteId' => $utenteId])
                         ->with('success', 'Responsável removido completamente.');
    }
}

public function uploadDocumento(Request $request, $responsavelId)
{
    $responsavel = Responsavel::findOrFail($responsavelId);

    $request->validate([
        'documento' => 'required|file|mimes:pdf,doc,docx,txt,png,jpg,jpeg|max:51200',
    ]);

    if ($request->hasFile('documento')) {
        $documento = $request->file('documento');

        if ($documento->isValid()) {
            // Criar um nome único para o ficheiro
            $nomeFicheiro = uniqid() . '.' . $documento->getClientOriginalExtension();
            $documentoPath = 'uploads/responsaveis/documentos/' . $nomeFicheiro;

            // Guardar o documento na pasta correta
            $documento->move(public_path('uploads/responsaveis/documentos'), $documentoPath);

            // Guardar o registo na base de dados
            Documento::create([
                'responsavel_id' => $responsavel->id,
                'path' => $documentoPath
            ]);

            // 🔹 Registar no histórico apenas o nome do ficheiro
            $this->registarHistorico($responsavelId, null, "Documento adicionado: $nomeFicheiro");

            return response()->json(['success' => true, 'message' => 'Documento carregado com sucesso!']);
        }
    }

    return response()->json(['success' => false, 'message' => 'Falha no upload do documento.'], 500);
}


public function adicionarNota(Request $request, $responsavelId)
{
    $request->validate([
        'nota' => 'required|string|max:1000',
    ]);

    NotaResponsavel::create([
        'responsavel_id' => $responsavelId,
        'nota' => $request->nota,
        'adicionado_por' => auth()->id(),
    ]);

    return response()->json(['success' => true, 'message' => 'Nota adicionada com sucesso!']);
}

public function carregarNotas($responsavelId)
{
    $notas = NotaResponsavel::where('responsavel_id', $responsavelId)
        ->orderBy('created_at', 'desc')
        ->with('usuario')
        ->get();

    return response()->json($notas);
}

public function removeFoto($responsavelId)
{
    try {
        // Encontra o responsável
        $responsavel = Responsavel::findOrFail($responsavelId);

        // Verifica se há foto antes de deletar
        if ($responsavel->foto && file_exists(public_path($responsavel->foto))) {
            unlink(public_path($responsavel->foto)); // Remove o arquivo físico
        }

        // Atualiza o banco de dados
        $responsavel->foto = null;
        $responsavel->save();

        return response()->json(['success' => true, 'message' => 'Foto removida com sucesso!']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Erro ao remover a foto.', 'error' => $e->getMessage()], 500);
    }
}


public function listar(Request $request)
{
    $query = Responsavel::query();

    // Filtro de pesquisa
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where('nome_completo', 'LIKE', "%$search%")
              ->orWhere('nr_identificacao', 'LIKE', "%$search%")
              ->orWhere('email', 'LIKE', "%$search%"); // Pesquisar também por email
    }

    // Filtro para Encarregados de Educação sem email
    if ($request->has('find_ee_without_email') && $request->input('find_ee_without_email') == '1') {
        $query->whereHas('utentes', function($query) {
            $query->where('tipo_responsavel', 'Encarregado de Educacao')
                  ->whereNull('email');
        });
    }

    // Buscar responsáveis com a relação 'utentes'
    $responsaveis = $query->with('utentes') // Carrega a relação com 'utentes'
                          ->paginate(10); // Paginação de 10 itens por página

    // Adiciona o campo 'is_encarregado_de_educacao' para cada responsável
    foreach ($responsaveis as $responsavel) {
        $responsavel->is_encarregado_de_educacao = $responsavel->isEncarregadoDeEducacao();
    }

    // Passa o valor de pesquisa para a view
    return view('responsaveis.listar', compact('responsaveis'))->with('search', $request->input('search'));
}



public function atualizarAssociacao(Request $request)
{
    try {
        // **1️⃣ Validação dos dados**
        $request->validate([
            'id' => 'required|exists:responsaveis_utentes,id',
            'grau_parentesco' => 'required|string|max:50',
            'data_inicio_autorizacao' => 'nullable|date',
            'data_fim_autorizacao' => 'nullable|date|after_or_equal:data_inicio_autorizacao',
            'tipo_responsavel' => ['required', Rule::in(['Encarregado de Educacao', 'Autorizado', 'Autorizado Excecional'])],
            'observacoes' => 'nullable|string',
            'dias_nao_autorizados' => 'nullable|array',
            'dias_nao_autorizados.*' => 'string|in:Segunda,Terça,Quarta,Quinta,Sexta',
        ]);

        // **2️⃣ Buscar associação existente**
        $associacao = DB::table('responsaveis_utentes')->where('id', $request->id)->first();

        if (!$associacao) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => '❌ Associação não encontrada.'
            ], 404);
        }

        // **3️⃣ Verificar se já existe outro "Encarregado de Educação" para o mesmo utente**
        if ($request->tipo_responsavel === 'Encarregado de Educacao') {
            $existeOutro = DB::table('responsaveis_utentes')
                ->join('responsaveis', 'responsaveis_utentes.responsavel_id', '=', 'responsaveis.id')
                ->where('responsaveis_utentes.utente_id', $associacao->utente_id)
                ->where('responsaveis_utentes.id', '!=', $associacao->id)
                ->where('responsaveis_utentes.tipo_responsavel', 'Encarregado de Educacao')
                ->select('responsaveis.nome_completo', 'responsaveis.nr_identificacao')
                ->first();

            if ($existeOutro) {
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => "⚠️ Este utente já tem um Encarregado de Educação associado: **{$existeOutro->nome_completo}** (NIF: {$existeOutro->nr_identificacao}). Apenas um é permitido."
                ], 422);
            }
        }

        // **4️⃣ Buscar nome do utente para registrar no histórico**
        $utente = DB::table('assets')->where('id', $associacao->utente_id)->first();
        $nomeUtente = $utente ? $utente->name : 'Desconhecido';

        // **5️⃣ Capturar alterações**
        $alteracoes = [];
        $campos = [
            'grau_parentesco',
            'data_inicio_autorizacao',
            'data_fim_autorizacao',
            'tipo_responsavel',
            'observacoes',
            'dias_nao_autorizados'
        ];

        // Preparar o novo valor de dias_nao_autorizados como string CSV (ou null)
        $novoDiasNaoAutorizados = $request->has('dias_nao_autorizados')
            ? implode(',', $request->dias_nao_autorizados)
            : null;

        foreach ($campos as $campo) {
            $valorAntigo = $associacao->$campo ?? 'N/A';
            $valorNovo = ($campo === 'dias_nao_autorizados')
                ? $novoDiasNaoAutorizados ?? 'N/A'
                : ($request->$campo ?? 'N/A');

            if ($valorAntigo !== $valorNovo) {
                $alteracoes[] = ucfirst(str_replace('_', ' ', $campo)) . ": '{$valorAntigo}' → '{$valorNovo}'";
            }
        }

        // **6️⃣ Se houver alterações, salvar**
        if (!empty($alteracoes)) {
            DB::table('responsaveis_utentes')->where('id', $request->id)->update([
                'grau_parentesco' => $request->grau_parentesco,
                'data_inicio_autorizacao' => $request->data_inicio_autorizacao,
                'data_fim_autorizacao' => $request->data_fim_autorizacao,
                'tipo_responsavel' => $request->tipo_responsavel,
                'observacoes' => $request->observacoes ?? '',
                'dias_nao_autorizados' => $novoDiasNaoAutorizados,
                'updated_at' => now(),
            ]);

            // **7️⃣ Recalcular o estado de autorização**
            $responsavel = Responsavel::find($associacao->responsavel_id);
            $novoEstado = $responsavel->atualizarEstadoAutorizacao($associacao->utente_id);

            DB::table('responsaveis_utentes')
                ->where('id', $request->id)
                ->update(['estado_autorizacao' => $novoEstado]);

            // **8️⃣ Registrar no histórico**
            DB::table('responsaveis_historico')->insert([
                'responsavel_id' => $associacao->responsavel_id,
                'utente_id' => $associacao->utente_id,
                'alterado_por' => Auth::id(),
                'alterado_em' => now(),
                'motivo' => "📌 Associação de **{$nomeUtente}** atualizada: " . implode(', ', $alteracoes),
            ]);

            return response()->json([
                'sucesso' => true,
                'mensagem' => "Associação de {$nomeUtente} atualizada com sucesso!"
            ]);
        }

        return response()->json([
            'sucesso' => false,
            'mensagem' => 'Nenhuma alteração realizada.'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'sucesso' => false,
            'mensagem' => 'Erro de validação. Verifique os dados inseridos.',
            'erros' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'sucesso' => false,
            'mensagem' => '❌ Ocorreu um erro ao atualizar a associação. Tente novamente.',
            'erro' => $e->getMessage()
        ], 500);
    }
}




public function removerAssociacao(Request $request)
{
    try {
        // ✅ Validação do ID
        $request->validate([
            'id' => 'required|exists:responsaveis_utentes,id',
        ]);

        // 🔍 Buscar associação e utente antes da remoção
        $associacao = DB::table('responsaveis_utentes')
            ->where('id', $request->id)
            ->first();

        if (!$associacao) {
            return response()->json(['success' => false, 'message' => 'Associação não encontrada.'], 404);
        }

        // 🔎 Buscar nome do utente associado
        $utente = DB::table('assets')->where('id', $associacao->utente_id)->first();

        // 📜 Registrar no histórico antes de remover
        DB::table('responsaveis_historico')->insert([
            'responsavel_id' => $associacao->responsavel_id,
            'utente_id' => $associacao->utente_id,
            'nome_completo' => $utente->name ?? 'Nome desconhecido',
            'alterado_por' => Auth::id(),
            'alterado_em' => now(),
            'motivo' => "Removida associação do utente: {$utente->name}",
        ]);

        // 🗑️ Remover a associação
        DB::table('responsaveis_utentes')->where('id', $request->id)->delete();

        return response()->json([
            'success' => true,
            'message' => "Associação de '{$utente->name}' removida com sucesso!"
        ]);

    } catch (\Exception $e) {
        // ❌ Tratamento de erro
        return response()->json([
            'success' => false,
            'message' => 'Erro ao remover associação.',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function adicionarUtente(Request $request)
{
    \Log::info('📩 Requisição recebida para adicionar utente', [
        'dados' => $request->all()
    ]);

    try {
        // ✅ **Validação dos dados**
        $validated = $request->validate([
            'responsavel_id' => 'required|exists:responsaveis,id',
            'utente_id' => 'required|exists:assets,id',
            'grau_parentesco' => 'required|string|max:50',
            'tipo_responsavel' => [
                'required',
                Rule::in(['Encarregado de Educacao', 'Autorizado', 'Autorizado Excecional']),
            ],
            'data_inicio_autorizacao' => 'nullable|date',
            'data_fim_autorizacao' => 'nullable|date|after_or_equal:data_inicio_autorizacao',
            'observacoes' => 'nullable|string',
        ]);

        $responsavelId = $validated['responsavel_id'];
        $utenteId = $validated['utente_id'];

        // 🔹 **Verificar se já existe um Encarregado de Educação para este utente**
        if ($validated['tipo_responsavel'] === 'Encarregado de Educacao') {
            $encarregadoExistente = DB::table('responsaveis_utentes')
                ->join('responsaveis', 'responsaveis_utentes.responsavel_id', '=', 'responsaveis.id')
                ->where('responsaveis_utentes.utente_id', $utenteId)
                ->where('responsaveis_utentes.tipo_responsavel', 'Encarregado de Educacao')
                ->select('responsaveis.nome_completo', 'responsaveis.nr_identificacao')
                ->first();

            if ($encarregadoExistente) {
                return response()->json([
                    'success' => false,
                    'message' => "⚠️ O utente já tem um Encarregado de Educação associado: **{$encarregadoExistente->nome_completo}** (NIF: {$encarregadoExistente->nr_identificacao}). Apenas um Encarregado de Educação é permitido.",
                ], 422);
            }
        }

        // ✅ **Criar nova associação**
        DB::table('responsaveis_utentes')->insert([
            'responsavel_id' => $responsavelId,
            'utente_id' => $utenteId,
            'grau_parentesco' => $validated['grau_parentesco'],
            'tipo_responsavel' => $validated['tipo_responsavel'],
            'data_inicio_autorizacao' => $validated['data_inicio_autorizacao'] ?? now()->format('Y-m-d'),
            'data_fim_autorizacao' => $validated['data_fim_autorizacao'] ?? null,
            'observacoes' => $validated['observacoes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Log::info("✅ Associação criada com sucesso", compact('responsavelId', 'utenteId'));

        // 🔹 Buscar o nome do utente para o histórico
        $utente = DB::table('assets')->where('id', $utenteId)->first();

        // ✅ **Registrar no histórico**
        DB::table('responsaveis_historico')->insert([
            'responsavel_id' => $responsavelId,
            'utente_id' => $utenteId,
            'nome_completo' => $utente->name ?? 'Desconhecido',
            'alterado_por' => Auth::id(),
            'alterado_em' => now(),
            'motivo' => "Adicionado utente: {$utente->name}",
        ]);

        return response()->json([
            'success' => true,
            'message' => "✅ Utente {$utente->name} associado com sucesso!",
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error("❌ Erro de Validação", ['errors' => $e->errors()]);

        return response()->json([
            'success' => false,
            'message' => '⚠️ Erro de validação.',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error("❌ Erro inesperado", ['exception' => $e->getMessage()]);

        return response()->json([
            'success' => false,
            'message' => '❌ Erro interno no servidor.',
        ], 500);
    }
}



public function buscarUtentesNaoAssociados(Request $request, $responsavelId)
{
    $search = $request->input('search');

    $utentesAssociados = DB::table('responsaveis_utentes')
        ->where('responsavel_id', $responsavelId)
        ->pluck('utente_id');

    $utentes = Asset::whereNotIn('id', $utentesAssociados)
        ->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('serial', 'LIKE', "%{$search}%"); // 🔍 Agora pesquisa pelo NIF também
        })
        ->limit(5)
        ->get(['id', 'name', DB::raw("COALESCE(serial, 'Sem NIF') as serial")]); // ✅ Retorna sempre um serial válido

    return response()->json(['data' => $utentes]);
}


private function registarHistorico($responsavelId, $utenteId = null, $motivo)
{
    DB::table('responsaveis_historico')->insert([
        'responsavel_id'      => $responsavelId,
        'utente_id'           => $utenteId, // pode ser null
        'alterado_por'        => Auth::check() ? Auth::id() : null,
        'alterado_por_ee'     => session()->has('ee_responsavel_id') ? session('ee_responsavel_id') : null,
        'alterado_em'         => now(),
        'motivo'              => $motivo
    ]);
}





}
