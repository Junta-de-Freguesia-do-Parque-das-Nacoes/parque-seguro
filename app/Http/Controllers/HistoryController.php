<?php

namespace App\Http\Controllers;

use App\Models\Asset; // Modelo "Asset" que representa "Utentes"
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GeneralHistoryExport; // Exportação geral
use App\Exports\HistoryExport;        // Exportação individual
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exports\MapaPresencasExport;
use Carbon\Carbon;
use Illuminate\Support\Str; 

class HistoryController extends Controller
{
    /**
     * Mostra o histórico de checkouts/checkins de um único utente.
     */
    public function showCheckoutHistory(Request $request, $id)
    {
        $utente = Asset::find($id);
        if (!$utente) {
            return redirect()->route('utentes.index')->with('error', 'Utente não encontrado');
        }

        $perPage = 10;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $actionType = $request->input('action_type');

        $query = DB::table('action_logs as al')
            ->leftJoin('users as u', 'al.user_id', '=', 'u.id')
            ->select(
                'al.id as log_id',
                'al.action_type',
                'u.first_name as responsible_first_name',
                'u.last_name as responsible_last_name',
                'al.created_at as action_date',
                'al.note as action_note'
            )
            ->where('al.target_id', $utente->id)
            ->whereIn('al.action_type', ['checkout', 'checkin']);

        if ($startDate) {
            $query->where('al.created_at', '>=', $startDate . ' 00:00:00');
        }
        if ($endDate) {
            $query->where('al.created_at', '<=', $endDate . ' 23:59:59');
        }
        if ($actionType) {
            $query->where('al.action_type', $actionType);
        }

        $checkoutsAndCheckins = $query->orderBy('al.created_at', 'desc')->paginate($perPage);
		
		$checkoutsAndCheckins->getCollection()->transform(function ($log) {
    $log->action_type = $this->traduzirAcao($log->action_type);
    return $log;
});

        return view('history.historico', compact('utente', 'checkoutsAndCheckins'));
    }

private function traduzirAcao($acao)
{
    return match (strtolower($acao)) {
        'checkin' => 'Entrada',
        'checkout' => 'Saída',
        default => ucfirst($acao),
    };
}

    /**
     * Mostra o histórico geral de check-ins e check-outs.
     */
    public function showAllHistory(Request $request)
    {
        $query = DB::table('action_logs as al')
            ->leftJoin('assets as a', 'al.target_id', '=', 'a.id')
            ->leftJoin('users as u', 'al.user_id', '=', 'u.id')
            ->select(
                'al.id as log_id',
                'a.name as utente_name',
                'a.asset_tag as utente_code',
                'al.action_type',
                'u.first_name as responsible_first_name',
                'u.last_name as responsible_last_name',
                'al.note as action_note',
                'al.created_at as action_date'
            )
            ->whereIn('al.action_type', ['checkin', 'checkout']);

        if ($request->filled('asset_name')) {
            $query->where('a.name', 'like', '%' . $request->input('asset_name') . '%');
        }

        if ($request->filled('start_date')) {
            $query->where('al.created_at', '>=', $request->input('start_date') . ' 00:00:00');
        }

        if ($request->filled('end_date')) {
            $query->where('al.created_at', '<=', $request->input('end_date') . ' 23:59:59');
        }

        if ($request->filled('action_type')) {
            $query->where('al.action_type', $request->input('action_type'));
        }

        $checkoutsAndCheckins = $query->orderBy('al.created_at', 'desc')->paginate(10);
		
		$checkoutsAndCheckins->getCollection()->transform(function ($log) {
    $log->action_type = $this->traduzirAcao($log->action_type);
    return $log;
});

        return view('history.all_history', compact('checkoutsAndCheckins'));
    }

    /**
     * Exporta o histórico geral para Excel.
     */
    public function exportAllToExcel(Request $request)
    {
        $filters = $request->all();

        return Excel::download(new GeneralHistoryExport($filters), 'historico_geral_checkins_checkouts.xlsx');
    }

    /**
     * Método para autocomplete de utentes.
     */
    public function autocompleteUtentes(Request $request)
    {
        $query = $request->input('q');
        $results = DB::table('assets')
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('asset_tag', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'asset_tag')
            ->limit(10)
            ->get();

        return response()->json($results);
    }

    /**
     * Exporta o histórico de um utente para PDF.
     */
    public function exportToPDF(Request $request, $id)
    {
        $utente = Asset::find($id);
        if (!$utente) {
            return redirect()->route('utentes.index')->with('error', 'Utente não encontrado');
        }

        $query = DB::table('action_logs as al')
            ->leftJoin('users as u', 'al.user_id', '=', 'u.id')
            ->select(
                'al.id as log_id',
                'al.action_type',
                'u.first_name as responsible_first_name',
                'u.last_name as responsible_last_name',
                'al.created_at as action_date',
                'al.note as action_note'
            )
            ->where('al.target_id', $utente->id)
            ->orderBy('al.created_at', 'desc')
            ->get();

        $pdf = PDF::loadView('history.pdf', [
            'utente' => $utente,
            'checkoutsAndCheckins' => $query,
            'generatedDate' => now()->format('d/m/Y H:i:s'),
        ]);

        return $pdf->download('historico.pdf');
    }

    /**
     * Exporta o histórico de um utente para Excel.
     */
    public function exportToExcel(Request $request, $id)
    {
        $utente = Asset::find($id);
        if (!$utente) {
            return redirect()->route('utentes.index')->with('error', 'Utente não encontrado');
        }

        return Excel::download(new HistoryExport($id), 'historico.xlsx');
    }


    public function mapaPresencasMensal(Request $request)
    {
        $ano = $request->input('ano', now()->year);
        $mes = $request->input('mes', now()->month);
        $turma = $request->input('turma');
        $utenteSearch = $request->input('utente');
        $pagina = $request->input('page', 1);
        $porPagina = 5;

        $inicioMes = Carbon::create($ano, $mes, 1)->startOfDay();
        $fimMes = $inicioMes->copy()->endOfMonth();

        $utentesQuery = Asset::whereIn('model_id', [261, 264])
            ->when($turma, fn($q) => $q->where('_snipeit_turma_59', $turma))
            ->when($utenteSearch, fn($q) => $q->where('name', 'like', "%{$utenteSearch}%"));

        $utentesPaginados = $utentesQuery->orderBy('name')->paginate($porPagina, ['*'], 'page', $pagina); // Adicionado orderBy
        $idsUtentesDaPagina = collect($utentesPaginados->items())->pluck('id');

        $logsPorUtente = collect(); // Inicializar
        if($idsUtentesDaPagina->isNotEmpty()){
            $logsPorUtente = DB::table('action_logs')
                ->whereBetween('created_at', [$inicioMes, $fimMes])
                ->whereIn('action_type', ['checkin', 'checkout'])
                ->whereIn('target_id', $idsUtentesDaPagina)
                ->orderBy('created_at', 'asc')
                ->get()
                ->groupBy('target_id');
        }
        
        $periodosDef = [
            261 => [
                'Manhã' => ['inicio_entrada' => '08:00', 'fim_entrada' => '09:00'],
                'Tarde' => ['inicio_entrada' => '15:00', 'fim_entrada' => '17:30'],
                'Extra' => ['horario_saida_minimo' => '17:30', 'referencia_fim' => '19:00']
            ],
            264 => [
                'Manhã' => ['inicio_entrada' => '08:00', 'fim_entrada' => '09:00'],
                'Tarde' => ['inicio_entrada' => '17:00', 'fim_entrada' => '19:00'],
            ],
        ];

        $dadosFormatadosParaView = [];

        foreach ($utentesPaginados as $utente) {
            $modelo = $utente->model_id;
            $periodosDoModelo = $periodosDef[$modelo] ?? [];
            $estadoPresencaDiaria = [];

            if (isset($logsPorUtente[$utente->id])) {
                foreach ($logsPorUtente[$utente->id] as $log) {
                    $dataLog = Carbon::parse($log->created_at);
                    $diaDoLog = $dataLog->day;
                    $horaDoLogStr = $dataLog->format('H:i');

                    if (!isset($estadoPresencaDiaria[$diaDoLog])) {
                        $estadoPresencaDiaria[$diaDoLog] = [
                            'P_Manha' => false, 'P_Tarde' => false, 'CheckoutAposHorarioExtra' => false,
                        ];
                    }
                    if (isset($periodosDoModelo['Manhã']) && !$estadoPresencaDiaria[$diaDoLog]['P_Manha']) {
                        $defManha = $periodosDoModelo['Manhã'];
                        if ($log->action_type == 'checkin' && $horaDoLogStr >= $defManha['inicio_entrada'] && $horaDoLogStr <= $defManha['fim_entrada']) {
                            $estadoPresencaDiaria[$diaDoLog]['P_Manha'] = true;
                        }
                    }
                    if (isset($periodosDoModelo['Tarde']) && !$estadoPresencaDiaria[$diaDoLog]['P_Tarde']) {
                        $defTarde = $periodosDoModelo['Tarde'];
                        if ($log->action_type == 'checkin' && $horaDoLogStr >= $defTarde['inicio_entrada'] && $horaDoLogStr <= $defTarde['fim_entrada']) {
                            $estadoPresencaDiaria[$diaDoLog]['P_Tarde'] = true;
                        }
                    }
                    if ($modelo == 261 && isset($periodosDoModelo['Extra'])) {
                        $defExtra = $periodosDoModelo['Extra'];
                        if ($log->action_type == 'checkout' && $horaDoLogStr > $defExtra['horario_saida_minimo']) {
                            $estadoPresencaDiaria[$diaDoLog]['CheckoutAposHorarioExtra'] = true;
                        }
                    }
                }
            }

            $utenteDataParaView = [
                'id' => $utente->id, 'nome' => $utente->name, 'periodos' => []
            ];
            $nomesPeriodosExibicao = ['Manhã', 'Tarde'];
            if ($modelo == 261 && isset($periodosDoModelo['Extra'])) {
                $nomesPeriodosExibicao[] = 'Extra';
            }

            foreach ($nomesPeriodosExibicao as $nomePeriodo) {
                if (!isset($periodosDoModelo[$nomePeriodo]) && $nomePeriodo === 'Extra' && $modelo != 261) continue;
                if (!isset($periodosDoModelo[$nomePeriodo]) && $nomePeriodo !== 'Extra') continue;

                $linhaPeriodo = ['periodo' => $nomePeriodo, 'dias' => []];
                for ($dia = 1; $dia <= $fimMes->day; $dia++) {
                    $marcado = '';
                    $estadoDoDia = $estadoPresencaDiaria[$dia] ?? ['P_Manha' => false, 'P_Tarde' => false, 'CheckoutAposHorarioExtra' => false];
                    if ($nomePeriodo == 'Manhã' && $estadoDoDia['P_Manha']) $marcado = 'P';
                    elseif ($nomePeriodo == 'Tarde' && $estadoDoDia['P_Tarde']) $marcado = 'P';
                    elseif ($nomePeriodo == 'Extra' && $modelo == 261) {
                        if ($estadoDoDia['P_Manha'] && $estadoDoDia['P_Tarde'] && $estadoDoDia['CheckoutAposHorarioExtra']) $marcado = 'P';
                    }
                    $linhaPeriodo['dias'][$dia] = $marcado;
                }
                $utenteDataParaView['periodos'][] = $linhaPeriodo;
            }
            $dadosFormatadosParaView[] = $utenteDataParaView;
        }

        $turmasDisponiveis = Asset::whereNotNull('_snipeit_turma_59')
            ->whereIn('model_id', [261, 264])
            ->select('_snipeit_turma_59')
            ->distinct()
            ->orderBy('_snipeit_turma_59', 'asc')
            ->pluck('_snipeit_turma_59');

        return view('history.mapa-presencas', [
            'utentesDados' => $dadosFormatadosParaView,
            'ano' => $ano,
            'mes' => $mes,
            'paginator' => $utentesPaginados,
            'turmasDisponiveis' => $turmasDisponiveis,
            'diasNoMes' => $fimMes->day,
        ]);
    }

    public function exportarMapa(Request $request)
    {
        // Tentar aumentar o limite de tempo de execução para 300 segundos (5 minutos).
        // O @ suprime erros se a função set_time_limit for desabilitada nas configurações do PHP (php.ini).
        @set_time_limit(300);

        // Se estiver a usar Laravel Debugbar, desabilitá-lo para esta requisição pode ajudar
        // a reduzir um pouco o overhead, especialmente se muitas queries estiverem a ser logadas.
        if (class_exists(\Debugbar::class) && \App::environment('local')) { // Desabilitar apenas se existir e em ambiente local/dev
            try {
                \Debugbar::disable();
            } catch (\Exception $e) {
                // Ignorar se houver algum problema ao desabilitar o Debugbar
            }
        }

        $ano = $request->input('ano', now()->year);
        $mes = $request->input('mes', now()->month);
        $turma = $request->input('turma');
        $utenteSearch = $request->input('utente');

        $inicioMes = Carbon::create($ano, $mes, 1)->startOfDay();
        $fimMes = $inicioMes->copy()->endOfMonth();

        $utentesQuery = Asset::query()
            ->whereIn('model_id', [261, 264])
            ->when($turma, fn($q) => $q->where('_snipeit_turma_59', $turma))
            ->when($utenteSearch, fn($q) => $q->where('name', 'like', "%{$utenteSearch}%"));

        $utentes = $utentesQuery->orderBy('name')->get();

        $idsUtentes = $utentes->pluck('id');
        $logsPorUtente = collect();
        if ($idsUtentes->isNotEmpty()) {
            $logsPorUtente = DB::table('action_logs')
                ->whereBetween('created_at', [$inicioMes, $fimMes])
                ->whereIn('action_type', ['checkin', 'checkout'])
                ->whereIn('target_id', $idsUtentes)
                ->orderBy('created_at', 'asc')
                ->get()
                ->groupBy('target_id');
        }
        
        $periodosDef = [ /* Sua definição de períodos... */
            261 => [
                'Manhã' => ['inicio_entrada' => '08:00', 'fim_entrada' => '09:00'],
                'Tarde' => ['inicio_entrada' => '15:00', 'fim_entrada' => '17:30'],
                'Extra' => ['horario_saida_minimo' => '17:30', 'referencia_fim' => '19:00']
            ],
            264 => [
                'Manhã' => ['inicio_entrada' => '08:00', 'fim_entrada' => '09:00'],
                'Tarde' => ['inicio_entrada' => '17:00', 'fim_entrada' => '19:00'],
            ],
        ];

        $dadosParaExportar = [];
        $nomeUtenteParaFicheiro = null;

        if ($utenteSearch && $utentes->count() === 1) {
            $nomeUtenteParaFicheiro = $utentes->first()->name;
        } elseif ($utenteSearch) {
            $nomeUtenteParaFicheiro = $utenteSearch;
        }

        foreach ($utentes as $utente) {
            $modelo = $utente->model_id;
            $periodosDoModelo = $periodosDef[$modelo] ?? [];
            $estadoPresencaDiaria = [];

            if (isset($logsPorUtente[$utente->id])) {
                foreach ($logsPorUtente[$utente->id] as $log) {
                    $dataLog = Carbon::parse($log->created_at);
                    $diaDoLog = $dataLog->day;
                    $horaDoLogStr = $dataLog->toTimeString(); // Ligeiramente mais performático que format()

                    if (!isset($estadoPresencaDiaria[$diaDoLog])) {
                        $estadoPresencaDiaria[$diaDoLog] = [
                            'P_Manha' => false, 'P_Tarde' => false, 'CheckoutAposHorarioExtra' => false,
                        ];
                    }
                    if (isset($periodosDoModelo['Manhã']) && !$estadoPresencaDiaria[$diaDoLog]['P_Manha']) {
                        $defManha = $periodosDoModelo['Manhã'];
                        if ($log->action_type == 'checkin' && $horaDoLogStr >= $defManha['inicio_entrada'] && $horaDoLogStr <= $defManha['fim_entrada']) {
                            $estadoPresencaDiaria[$diaDoLog]['P_Manha'] = true;
                        }
                    }
                    if (isset($periodosDoModelo['Tarde']) && !$estadoPresencaDiaria[$diaDoLog]['P_Tarde']) {
                        $defTarde = $periodosDoModelo['Tarde'];
                        if ($log->action_type == 'checkin' && $horaDoLogStr >= $defTarde['inicio_entrada'] && $horaDoLogStr <= $defTarde['fim_entrada']) {
                            $estadoPresencaDiaria[$diaDoLog]['P_Tarde'] = true;
                        }
                    }
                    if ($modelo == 261 && isset($periodosDoModelo['Extra'])) {
                        $defExtra = $periodosDoModelo['Extra'];
                        if ($log->action_type == 'checkout' && $horaDoLogStr > $defExtra['horario_saida_minimo']) {
                            $estadoPresencaDiaria[$diaDoLog]['CheckoutAposHorarioExtra'] = true;
                        }
                    }
                }
            }

            $utenteDataParaExport = ['nome' => $utente->name, 'periodos' => []];
            $nomesPeriodosExibicao = ['Manhã', 'Tarde'];
            if ($modelo == 261 && isset($periodosDoModelo['Extra'])) $nomesPeriodosExibicao[] = 'Extra';

            foreach ($nomesPeriodosExibicao as $nomePeriodo) {
                if (!isset($periodosDoModelo[$nomePeriodo]) && $nomePeriodo === 'Extra' && $modelo != 261) continue;
                if (!isset($periodosDoModelo[$nomePeriodo]) && $nomePeriodo !== 'Extra') continue;

                $linhaPeriodo = ['periodo' => $nomePeriodo, 'dias' => []];
                for ($dia = 1; $dia <= $fimMes->day; $dia++) {
                    $marcado = '';
                    $estadoDoDia = $estadoPresencaDiaria[$dia] ?? ['P_Manha' => false, 'P_Tarde' => false, 'CheckoutAposHorarioExtra' => false];
                    if ($nomePeriodo == 'Manhã' && $estadoDoDia['P_Manha']) $marcado = 'P';
                    elseif ($nomePeriodo == 'Tarde' && $estadoDoDia['P_Tarde']) $marcado = 'P';
                    elseif ($nomePeriodo == 'Extra' && $modelo == 261) {
                        if ($estadoDoDia['P_Manha'] && $estadoDoDia['P_Tarde'] && $estadoDoDia['CheckoutAposHorarioExtra']) $marcado = 'P';
                    }
                    $linhaPeriodo['dias'][$dia] = $marcado;
                }
                $utenteDataParaExport['periodos'][] = $linhaPeriodo;
            }
            $dadosParaExportar[] = $utenteDataParaExport;
        }

        $nomeMesAno = Carbon::createFromDate($ano, $mes, 1)->translatedFormat('F_Y');
        $ficheiroNomeBase = 'Mapa_Presencas_' . $nomeMesAno;
        if ($turma) $ficheiroNomeBase .= '_' . Str::slug($turma, '_');
        if ($nomeUtenteParaFicheiro) $ficheiroNomeBase .= '_' . Str::slug($nomeUtenteParaFicheiro, '_');
        $ficheiroNomeFinal = $ficheiroNomeBase . '.xlsx';

        return Excel::download(new MapaPresencasExport($dadosParaExportar, $ano, $mes, $fimMes->day), $ficheiroNomeFinal);
    }



}
