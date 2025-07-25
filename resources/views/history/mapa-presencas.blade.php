{{-- /var/www/html/snipeit/resources/views/history/mapa-presencas.blade.php --}}

@extends('layouts.default')

@section('content')
<h3 class="mb-3">Mapa de Presenças - {{ \Carbon\Carbon::create($ano, $mes)->translatedFormat('F Y') }}</h3>

{{-- Filtros (mantém-se igual ao seu código original) --}}
<form method="GET" class="mb-4">
    <div class="row">
        <div class="col-md-3">
            <label>Mês</label>
            <select name="mes" class="form-control">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label>Ano</label>
            <input type="number" name="ano" class="form-control" value="{{ $ano }}">
        </div>
        <div class="col-md-3">
            <label>Turma</label>
            <select name="turma" class="form-control">
                <option value="">Todas</option>
                @foreach ($turmasDisponiveis as $turmaOp)
                    <option value="{{ $turmaOp }}" {{ request('turma') == $turmaOp ? 'selected' : '' }}>
                        {{ $turmaOp }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label>Utente</label>
            <input type="text" name="utente" class="form-control" value="{{ request('utente') }}" placeholder="Nome do Utente">
        </div>
        <div class="col-md-1 align-self-end">
            <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
        </div>
    </div>
</form>

{{-- Tabela Ajustada --}}
<table class="table table-bordered table-sm table-hover" style="table-layout: fixed;">
    <thead class="thead-light sticky-top bg-light">
        <tr>
            {{-- Use a variável $diasNoMes passada pelo controlador --}}
            <th rowspan="2" style="width: 200px;">Nome</th>
            <th rowspan="2" style="width: 120px;">Período</th>
            @for ($dia = 1; $dia <= $diasNoMes; $dia++)
                <th class="text-center" style="width: 35px;">{{ $dia }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        {{-- Itera sobre $utentesDados em vez de $dados --}}
        @forelse ($utentesDados as $utente)
            @foreach ($utente['periodos'] as $indexPeriodo => $periodoData)
                <tr>
                    @if ($indexPeriodo === 0) {{-- Mostrar nome do utente apenas na primeira linha de período dele --}}
                        <td rowspan="{{ count($utente['periodos']) }}" class="align-middle font-weight-bold">
                            {{ $utente['nome'] }}
                        </td>
                    @endif
                    <td>{{ $periodoData['periodo'] }}</td>
                    {{-- Use a variável $diasNoMes passada pelo controlador --}}
                    @for ($dia = 1; $dia <= $diasNoMes; $dia++)
                        <td class="text-center {{ ($periodoData['dias'][$dia] ?? '') == 'P' ? 'bg-success text-white font-weight-bold' : '' }}">
                            {{ $periodoData['dias'][$dia] ?? '' }}
                        </td>
                    @endfor
                </tr>
            @endforeach
            {{-- Linha separadora entre utentes, se não for o último utente --}}
            @if (!$loop->last && count($utentesDados) > 1)
                <tr class="table-secondary">
                     {{-- Use a variável $diasNoMes passada pelo controlador --}}
                    <td colspan="{{ 2 + $diasNoMes }}" style="border-top: 2px solid #ddd;"></td>
                </tr>
            @endif
        @empty
            <tr>
                 {{-- Use a variável $diasNoMes passada pelo controlador --}}
                <td colspan="{{ 2 + $diasNoMes }}" class="text-center">Nenhum utente encontrado para os filtros selecionados.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Exportar (mantém-se igual) --}}
<a href="{{ route('mapa.presencas.exportar', request()->query()) }}" class="btn btn-success mb-3">
    📥 Exportar para Excel
</a>



{{-- Paginação (mantém-se igual) --}}
<div class="d-flex justify-content-center mt-4">
    {{ $paginator->appends(request()->query())->links() }}
</div>
@endsection