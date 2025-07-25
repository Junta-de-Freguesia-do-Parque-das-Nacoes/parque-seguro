<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificacaoBackoffice; // Seu modelo de notificação
use App\Models\User; // Certifique-se que o modelo User está importado
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Para DB::table se necessário

class NotificacaoBackofficeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        // Inicia a query a partir da relação do utilizador.
        // Isto já filtra por user_id e junta automaticamente a tabela notificacao_user.
        $query = $user->notificacoes();

        // Filtro de leitura (usando wherePivot para aceder ao campo 'lida' da tabela pivot)
        if ($request->filled('lida')) {
            // Garante que o valor é tratado como booleano (0 ou 1)
            $lidaStatus = filter_var($request->lida, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($lidaStatus !== null) {
                $query->wherePivot('lida', $lidaStatus);
            }
        }

        // Filtro por data (os campos de data estão na tabela notificacoes_backoffice)
        if ($request->filled('data_inicio')) {
            $query->whereDate('notificacoes_backoffice.created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('notificacoes_backoffice.created_at', '<=', $request->data_fim);
        }

        // Filtro por tipo (campo 'tipo' está na tabela notificacoes_backoffice)
        if ($request->filled('tipo')) {
            $query->where('notificacoes_backoffice.tipo', $request->tipo);
        }

        // Filtro por texto (campo 'mensagem' está na tabela notificacoes_backoffice)
        if ($request->filled('pesquisa')) {
            $query->where('notificacoes_backoffice.mensagem', 'like', '%' . $request->pesquisa . '%');
        }

        // Obtem tipos únicos para o filtro de dropdown
        // É melhor obter os tipos de notificações que o user realmente tem, ou todos os tipos existentes.
        // Para todos os tipos existentes no sistema:
        $tiposDisponiveis = NotificacaoBackoffice::distinct()->pluck('tipo');
        // Para tipos que o utilizador autenticado tem:
        // $tiposDisponiveis = $user->notificacoes()->distinct('notificacoes_backoffice.tipo')->pluck('notificacoes_backoffice.tipo');


        // Ordena e pagina
        // O select('notificacoes_backoffice.*', 'notificacao_user.lida as pivot_lida')
        // pode ser útil se quiser aceder a 'lida' diretamente no objeto $notificacao->pivot_lida
        // em vez de $notificacao->pivot->lida, mas com withPivot('lida') já é acessível.
        $notificacoes = $query
            ->orderBy('notificacoes_backoffice.created_at', 'desc') // Ordena pela data de criação da notificação
            ->paginate(20) // Ajuste o número de itens por página conforme necessário
            ->appends($request->query()); // Mantém os parâmetros de filtro na paginação

        // Se for uma requisição AJAX (para filtros ou paginação)
        if ($request->ajax()) {
            return response()->json([
                // Renderiza a view parcial apenas com a lista de notificações
                'html' => view('notificacoes.partials.lista_notificacoes', compact('notificacoes'))->render(),
                // A paginação já está incluída no partial, mas se precisar dela separadamente:
                // 'pagination_html' => (string) $notificacoes->links(),
            ]);
        }

        // Para a carga inicial da página, retorna a view completa
        return view('notificacoes.index', compact('notificacoes', 'tiposDisponiveis'));
    }

    // Adicionado Request $request para poder verificar se é AJAX
    public function marcarComoLida(Request $request, $id)
{
    $user = Auth::user();

    // Confirma que a notificação existe para este utilizador
    $notificacao = $user->notificacoes()
        ->where('notificacoes_backoffice.id', $id)
        ->withPivot('lida') // Garante que 'lida' está carregado
        ->first();

    if (!$notificacao) {
        return response()->json([
            'success' => false,
            'message' => 'Notificação não encontrada.',
        ], 404);
    }

    // Atualiza apenas se ainda estiver por ler
    if (!$notificacao->pivot->lida) {
        DB::table('notificacao_user')
            ->where('user_id', $user->id)
            ->where('notificacao_backoffice_id', $id)
            ->update([
                'lida' => true,
                'updated_at' => now(),
            ]);
    }

    // Contagem de não lidas atualizada
    $newCount = $user->notificacoes()->wherePivot('lida', false)->count();

    return response()->json([
        'success' => true,
        'message' => 'Notificação marcada como lida.',
        'nao_lidas' => $newCount,
    ]);
}


    public function contagem()
    {
        $user = Auth::user(); // Adicionado para consistência, embora auth() funcione
        $count = $user
            ->notificacoes()
            ->wherePivot('lida', false)
            ->count();

        return response()->json(['nao_lidas' => $count]);
    }

    public function marcarTodasComoLidas(Request $request)
    {
        $user = Auth::user();

        // Atualiza todas as notificações não lidas do utilizador para lidas na tabela pivot
        DB::table('notificacao_user')
    ->where('user_id', $user->id)
    ->where('lida', false)
    ->update(['lida' => true, 'updated_at' => now()]);

        // Se precisar garantir que os timestamps 'updated_at' na pivot são atualizados para cada linha:
        // DB::table('notificacao_user')
        //    ->where('user_id', $user->id)
        //    ->where('lida', false)
        //    ->update(['lida' => true, 'updated_at' => now()]);


        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Todas as notificações foram marcadas como lidas.',
                'nao_lidas' => 0 // Após marcar todas, não haverá não lidas
            ]);
        }

        return redirect()->route('notificacoes.index')->with('success', 'Todas as notificações foram marcadas como lidas.');
    }

    public function marcarComoLidaRedirect($id)
{
    $user = Auth::user();

    $notificacao = $user->notificacoes()
        ->where('notificacoes_backoffice.id', $id)
        ->first();

    if (!$notificacao) {
        return redirect()->route('notificacoes.index')->with('error', 'Notificação não encontrada.');
    }

    if (!$notificacao->pivot->lida) {
        DB::table('notificacao_user')
            ->where('user_id', $user->id)
            ->where('notificacao_backoffice_id', $id)
            ->update([
                'lida' => true,
                'updated_at' => now(),
            ]);
    }

    return redirect()->to(route('notificacoes.index', ['lida' => '']) . '#notificacao-' . $id);




}


}
