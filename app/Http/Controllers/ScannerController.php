<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Asset;
use App\Models\Company;
use Carbon\Carbon;

class ScannerController extends Controller
{
    public function index()
{
    // Presenças por escolas (company_id)
    $presenceData = DB::table('companies')
        ->select(
            'companies.id as school_id', 
            'companies.name as school_name',
            DB::raw("IFNULL(SUM(CASE WHEN status_labels.id = 23 THEN 1 ELSE 0 END), 0) as total_present"),
            DB::raw("IFNULL(SUM(CASE WHEN status_labels.id = 25 THEN 1 ELSE 0 END), 0) as total_absent"),
            DB::raw("false as is_program")
        )
        ->leftJoin('assets', 'companies.id', '=', 'assets.company_id')
        ->leftJoin('status_labels', 'assets.status_id', '=', 'status_labels.id')
        ->groupBy('companies.id', 'companies.name')
        ->orderBy('companies.name', 'asc')
        ->get();

    // Programas personalizados baseados em campos de inscrição
    $programasCheckbox = [
        '_snipeit_ha_ferias_no_parque_67' => 'Há Férias no Parque',
        '_snipeit_parque_em_movimento_verao_68' => 'Parque em Movimento Verão',
        '_snipeit_parque_em_movimento_pascoa_69' => 'Parque em Movimento Páscoa',
        '_snipeit_aaaf_caf_ferias_pascoa_70' => 'AAAF/CAF Férias Páscoa',
        '_snipeit_aaaf_caf_ferias_verao_71' => 'AAAF/CAF Férias Verão',
        '_snipeit_parque_em_movimento_natal_72' => 'Parque em Movimento Natal',
        '_snipeit_aaaf_caf_ferias_carnaval_73' => 'AAAF/CAF Férias Carnaval',
    ];

    $hoje = \Carbon\Carbon::today();
    $dadosProgramas = [];

    foreach ($programasCheckbox as $campo => $nome) {
        $utentes = \App\Models\Asset::whereNotNull($campo)->get();

        $presentes = 0;
        $ausentes = 0;

        foreach ($utentes as $utente) {
            $datas = $this->expandirDatas($utente->$campo);
            if ($datas->contains(fn($d) => $d->isSameDay($hoje))) {
                if ($utente->status_id == 23) $presentes++;
                elseif ($utente->status_id == 25) $ausentes++;
            }
        }

        // Apenas adiciona programas com utentes relevantes
        if ($presentes > 0 || $ausentes > 0) {
            $dadosProgramas[] = (object)[
                'school_id' => $campo,
                'school_name' => $nome,
                'total_present' => $presentes,
                'total_absent' => $ausentes,
                'is_program' => true,
            ];
        }
    }

    // Junta as escolas e os programas no mesmo conjunto
    $presenceData = $presenceData->merge($dadosProgramas)->sortBy('school_name');

    return view('qr-code-scanner', compact('presenceData'));
}

    private function expandirDatas($stringDatas)
    {
        return collect(preg_split('/[\r\n,]+/', $stringDatas ?? ''))
            ->map(fn($data) => trim($data))
            ->filter()
            ->flatMap(function ($item) {
                if (
                    preg_match('/(\d{2}\/\d{2}\/\d{4})\s*-\s*(\d{2}\/\d{2}\/\d{4})/', $item, $matches) ||
                    preg_match('/(\d{2}\/\d{2}\/\d{4})\s*a\s*(\d{2}\/\d{2}\/\d{4})/', $item, $matches)
                ) {
                    try {
                        $dataInicio = Carbon::createFromFormat('d/m/Y', trim($matches[1]));
                        $dataFim = Carbon::createFromFormat('d/m/Y', trim($matches[2]));
                        $datas = [];

                        while ($dataInicio->lte($dataFim)) {
                            $datas[] = $dataInicio->copy();
                            $dataInicio->addDay();
                        }

                        return collect($datas);
                    } catch (\Exception $e) {
                        return collect();
                    }
                } else {
                    try {
                        return collect([Carbon::createFromFormat('d/m/Y', $item)]);
                    } catch (\Exception $e) {
                        return collect();
                    }
                }
            });
    }

    public function showPresenceDetails(Request $request)
{
    $schoolId = $request->input('school_id'); // Pode ser company_id ou nome de campo personalizado
    $statusId = $request->input('status_id');
    $search = $request->input('search');

    // Lista dos campos personalizados dos programas
    $programasCheckbox = [
        '_snipeit_ha_ferias_no_parque_67' => 'Há Férias no Parque',
        '_snipeit_parque_em_movimento_verao_68' => 'Parque em Movimento Verão',
        '_snipeit_parque_em_movimento_pascoa_69' => 'Parque em Movimento Páscoa',
        '_snipeit_aaaf_caf_ferias_pascoa_70' => 'AAAF/CAF Férias Páscoa',
        '_snipeit_aaaf_caf_ferias_verao_71' => 'AAAF/CAF Férias Verão',
        '_snipeit_parque_em_movimento_natal_72' => 'Parque em Movimento Natal',
        '_snipeit_aaaf_caf_ferias_carnaval_73' => 'AAAF/CAF Férias Carnaval',
    ];

    $hoje = \Carbon\Carbon::today();

    // Verifica se é um programa (campo personalizado)
    if (array_key_exists($schoolId, $programasCheckbox)) {
        $nomePrograma = $programasCheckbox[$schoolId];

        $query = Asset::whereNotNull($schoolId)
            ->where(function ($q) use ($schoolId, $hoje) {
                $assets = \App\Http\Controllers\ScannerController::expandirDatasPublic(); // Função auxiliar abaixo
                $q->whereRaw("1 = 1"); // Garante a sintaxe
            })
            ->get()
            ->filter(function ($asset) use ($schoolId, $hoje) {
                $datas = ScannerController::expandirDatasPublic($asset->$schoolId);
                return $datas->contains(fn($d) => $d->isSameDay($hoje));
            });

        // Aplica filtros manuais
        if ($statusId) {
            $query = $query->where('status_id', $statusId);
        }

        if (!empty($search)) {
            $query = $query->filter(fn($a) => stripos($a->name, $search) !== false);
        }

        return view('presence-details', [
            'assets' => $query,
            'schoolName' => $nomePrograma,
            'status_id' => $statusId,
            'search' => $search,
        ]);
    }

    // Caso contrário, continua como escola
    $school = Company::find($schoolId);
    if (!$school) {
        return redirect()->back()->with('error', 'Escola não encontrada.');
    }

    $query = Asset::where('company_id', $schoolId)
        ->where('status_id', $statusId);

    if (!empty($search)) {
        $query->where('name', 'LIKE', "%$search%");
    }

    $assets = $query->orderBy('name', 'asc')->get();

    return view('presence-details', [
        'assets' => $assets,
        'schoolName' => $school->name,
        'status_id' => $statusId,
        'search' => $search,
    ]);
}


public function showPresenceAbsences(Request $request)
{
    $schoolId = $request->get('school_id');
    $statusId = $request->get('status_id');
    $search = $request->get('search');

    // Carregar escolas com utentes ativos (status 23 ou 25)
    $schools = \App\Models\Company::whereHas('assets', function ($query) {
            $query->whereIn('status_id', [23, 25]);
        })
        ->orderBy('name', 'asc')
        ->get()
        ->map(function ($school) {
            return (object)[
                'id' => $school->id,
                'name' => $school->name,
                'is_program' => false,
            ];
        });

    // Lista dos programas e nomes
    $programasCheckbox = [
        '_snipeit_ha_ferias_no_parque_67' => 'Há Férias no Parque',
        '_snipeit_parque_em_movimento_verao_68' => 'Parque em Movimento Verão',
        '_snipeit_parque_em_movimento_pascoa_69' => 'Parque em Movimento Páscoa',
        '_snipeit_aaaf_caf_ferias_pascoa_70' => 'AAAF/CAF Férias Páscoa',
        '_snipeit_aaaf_caf_ferias_verao_71' => 'AAAF/CAF Férias Verão',
        '_snipeit_parque_em_movimento_natal_72' => 'Parque em Movimento Natal',
        '_snipeit_aaaf_caf_ferias_carnaval_73' => 'AAAF/CAF Férias Carnaval',
    ];

    $hoje = now();
    $programas = collect();

    foreach ($programasCheckbox as $campo => $nome) {
        $utentes = \App\Models\Asset::whereNotNull($campo)
            ->whereIn('status_id', [23, 25])
            ->get()
            ->filter(function ($asset) use ($campo, $hoje) {
                return \App\Http\Controllers\ScannerController::expandirDatasPublic($asset->$campo)
                    ->contains(fn($d) => $d->isSameDay($hoje));
            });

        if ($utentes->isNotEmpty()) {
            $programas->push((object)[
                'id' => $campo,
                'name' => $nome,
                'is_program' => true,
            ]);
        }
    }

    // Juntar escolas e programas com dados
    $schools = $schools->merge($programas);

    // Buscar alunos
    $query = \App\Models\Asset::query();

    if ($statusId) {
        $query->where('status_id', $statusId);
    }

    if (!empty($search)) {
        $query->where('name', 'LIKE', "%$search%");
    }

    if ($schoolId && is_numeric($schoolId)) {
        $query->where('company_id', $schoolId);
    } elseif ($schoolId && !is_numeric($schoolId)) {
        $query = $query->whereNotNull($schoolId)->get()->filter(function ($asset) use ($schoolId, $hoje) {
            return \App\Http\Controllers\ScannerController::expandirDatasPublic($asset->$schoolId)
                ->contains(fn($d) => $d->isSameDay($hoje));
        });
    } else {
        $query = $query->with('company')->orderBy('name')->get();
    }

    // Se query virou Collection (programa), deixar como está. Senão, get()
    $alunos = $query instanceof \Illuminate\Support\Collection ? $query : $query->with('company')->orderBy('name')->get();

    $status = $statusId == 23 ? 'Presentes' : ($statusId == 25 ? 'Ausentes' : 'Todos');

    return view('presencas_ausencias', compact('alunos', 'schools', 'status', 'search'));
}

    public static function expandirDatasPublic($stringDatas = '')
{
    $hoje = \Carbon\Carbon::today();

    return collect(preg_split('/[\r\n,]+/', $stringDatas ?? ''))
        ->map(fn($data) => trim($data))
        ->filter()
        ->flatMap(function ($item) {
            if (
                preg_match('/(\d{2}\/\d{2}\/\d{4})\s*-\s*(\d{2}\/\d{2}\/\d{4})/', $item, $matches) ||
                preg_match('/(\d{2}\/\d{2}\/\d{4})\s*a\s*(\d{2}\/\d{2}\/\d{4})/', $item, $matches)
            ) {
                try {
                    $inicio = \Carbon\Carbon::createFromFormat('d/m/Y', trim($matches[1]));
                    $fim = \Carbon\Carbon::createFromFormat('d/m/Y', trim($matches[2]));
                    $datas = [];

                    while ($inicio->lte($fim)) {
                        $datas[] = $inicio->copy();
                        $inicio->addDay();
                    }

                    return collect($datas);
                } catch (\Exception $e) {
                    return collect();
                }
            } else {
                try {
                    return collect([\Carbon\Carbon::createFromFormat('d/m/Y', $item)]);
                } catch (\Exception $e) {
                    return collect();
                }
            }
        });
}


}
