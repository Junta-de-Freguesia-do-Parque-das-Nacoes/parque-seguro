@extends('layouts.default')

@section('title', 'Presenças e Ausências')

<style>
    h1 {
        text-align: center;
        color: #4CAF50;
        margin-bottom: 20px;
    }

    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); 
        gap: 20px; 
        justify-content: start;
        margin: 20px 0;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        text-align: center;
        width: 100%;
        max-width: 200px;
        margin: 0;
        color: white;
    }

    .card-green { background-color: #4CAF50; }
    .card-orange { background-color: #FFA500; }
    
    .card img {
        width: 100%;
        height: 150px; 
        object-fit: cover;
        border-bottom: 1px solid #ddd;
    }

    .card .details {
        padding: 10px;
    }

    .card .details h3 {
        margin: 5px 0;
        font-size: 1em;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    .card .details p {
        margin: 5px 0;
        font-size: 0.9em;
    }

    .filters {
        text-align: center;
        margin-bottom: 20px;
    }

    .filters form {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .filters select, .filters input, .filters button {
        padding: 8px;
        font-size: 0.9em;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .filters button {
        background-color: #004080;
        color: white;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .filters button:hover {
        background-color: #003366;
    }

    .clear-btn {
        background-color: #ccc;
        color: black;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
    }

    .clear-btn:hover {
        background-color: #bbb;
    }

    .legend {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin: 20px 0;
        font-size: 0.9em;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 5px;
    }

    .legend-green { background-color: #4CAF50; }
    .legend-orange { background-color: #FFA500; }
</style>

@section('content')

@php
use App\Http\Controllers\ScannerController;
use Illuminate\Support\Str;

$programasCheckbox = [
    '_snipeit_ha_ferias_no_parque_67' => 'Há Férias no Parque',
    '_snipeit_parque_em_movimento_verao_68' => 'Parque em Movimento Verão',
    '_snipeit_parque_em_movimento_pascoa_69' => 'Parque em Movimento Páscoa',
    '_snipeit_aaaf_caf_ferias_pascoa_70' => 'AAAF/CAF Férias Páscoa',
    '_snipeit_aaaf_caf_ferias_verao_71' => 'AAAF/CAF Férias Verão',
    '_snipeit_parque_em_movimento_natal_72' => 'Parque em Movimento Natal',
    '_snipeit_aaaf_caf_ferias_carnaval_73' => 'AAAF/CAF Férias Carnaval',
];

// Adicionar dinamicamente os campos novos
foreach (\Schema::getColumnListing('assets') as $coluna) {
    if (
        Str::startsWith($coluna, '_snipeit_programa_') &&
        !array_key_exists($coluna, $programasCheckbox)
    ) {
        $nomeRaw = str_replace('_snipeit_programa_', '', $coluna);
        $partes = explode('_', $nomeRaw);
        if (is_numeric(end($partes))) {
            array_pop($partes);
        }
        $nomeFormatado = ucwords(implode(' ', $partes));
        $programasCheckbox[$coluna] = $nomeFormatado;
    }
}

$hoje = \Carbon\Carbon::now('Europe/Lisbon')->startOfDay();
@endphp

<h1>Lista de {{ $status }}</h1>
<p style="text-align: center; font-size: 1em; color: #555; margin-top: -10px;">
    Data: {{ \Carbon\Carbon::now()->format('d/m/Y') }}
</p>

<div class="legend">
    <div class="legend-item">
        <div class="legend-color legend-green"></div> <span>Presente</span>
    </div>
    <div class="legend-item">
        <div class="legend-color legend-orange"></div> <span>Ausente</span>
    </div>
</div>

<div class="filters">
    <form method="GET" action="{{ route('presencas.ausencias') }}">
        <select name="school_id">
            <option value="">Todas os locais</option>
            @foreach($schools as $school)
                <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                    {{ $school->is_program ? '📅 ' : '🏫 ' }}{{ $school->name }}
                </option>
            @endforeach
        </select>

        <select name="status_id">
            <option value="">Todos os Estados</option>
            <option value="23" {{ request('status_id') == 23 ? 'selected' : '' }}>Presentes</option>
            <option value="25" {{ request('status_id') == 25 ? 'selected' : '' }}>Ausentes</option>
        </select>

        <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar aluno...">
        <button type="submit">Pesquisar</button>

        @if(request('search') || request('school_id') || request('status_id'))
            <a href="{{ route('presencas.ausencias') }}" class="clear-btn">Limpar</a>
        @endif
    </form>
</div>

@if($alunos->isEmpty())
    <p style="text-align: center; color: red;">Não há registros de {{ $status }} no momento.</p>
@else
    <div class="cards">
        @foreach($alunos as $aluno)
            @php
                $programasHoje = [];
                foreach ($programasCheckbox as $campo => $nome) {
                    if (!empty($aluno->$campo)) {
                        $datas = ScannerController::expandirDatasPublic($aluno->$campo)
                            ->map(fn($d) => \Carbon\Carbon::parse($d));
                        if ($datas->contains(fn($d) => $d->isSameDay($hoje))) {
                            $programasHoje[] = $nome;
                        }
                    }
                }
            @endphp

            <div class="card {{ $aluno->status_id == 23 ? 'card-green' : ($aluno->status_id == 25 ? 'card-orange' : '') }}">
                <a href="https://parque-seguro.jf-parquedasnacoes.pt:8126/hardware/{{ $aluno->id }}" target="_blank" style="text-decoration: none; color: inherit;">
                    <img src="{{ $aluno->image ? route('assets.foto', ['filename' => basename($aluno->image)]) : asset('img/anoninochild.jpg') }}" alt="{{ $aluno->name }}">
                    <div class="details">
                        <h3>{{ $aluno->name }}</h3>

                        @foreach($programasHoje as $programa)
                            <p>📅 {{ $programa }}</p>
                        @endforeach

                        @if($aluno->company)
                            <p>🏫 {{ $aluno->company->name }}</p>
                        @else
                            <p>❓ Escola não definida</p>
                        @endif
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endif

@endsection