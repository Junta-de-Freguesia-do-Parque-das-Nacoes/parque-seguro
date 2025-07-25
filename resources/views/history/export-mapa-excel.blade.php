{{-- resources/views/history/export-mapa-excel.blade.php --}}
@php
    use Carbon\Carbon;
    // Assumindo que $ano e $mes são passados para esta view pela classe MapaPresencasExport
    $dataTitulo = Carbon::create($ano, $mes)->translatedFormat('F Y');
    // Assumindo que $diasNoMes é passado para esta view pela classe MapaPresencasExport
    // Se não, pode calcular aqui, mas é melhor passar: $diasNoMes = Carbon::create($ano, $mes)->daysInMonth;
@endphp

<h3>Mapa de Presenças</h3>

@if (request('turma')) {{-- Se quiser incluir a turma no título do Excel --}}
    <p><strong>Turma:</strong> {{ request('turma') }}</p>
@endif
{{-- Se quiser incluir o nome do utente filtrado no título do Excel --}}
@if (request('utente'))
    <p><strong>Utente:</strong> {{ request('utente') }}</p>
@endif

<p><strong>Mês:</strong> {{ $dataTitulo }}</p>

<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Período</th>
            {{-- Certifique-se que $diasNoMes está disponível aqui --}}
            @for ($dia = 1; $dia <= $diasNoMes; $dia++)
                <th>{{ $dia }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        {{--
            A variável $dados que a view espera deve ser a lista de utentes
            com a estrutura hierárquica. Vamos chamá-la $listaUtentes para clareza,
            e assumir que a classe MapaPresencasExport passa os dados com este nome.
        --}}
        @if (isset($listaUtentes) && count($listaUtentes) > 0)
            @foreach ($listaUtentes as $utenteData) {{-- Loop pelos utentes --}}
                @if (isset($utenteData['periodos']) && count($utenteData['periodos']) > 0)
                    @foreach ($utenteData['periodos'] as $indexPeriodo => $periodoInfo) {{-- Loop pelos períodos do utente --}}
                        <tr>
                            @if ($indexPeriodo === 0) {{-- Mostrar nome apenas na primeira linha do período do utente --}}
                                <td rowspan="{{ count($utenteData['periodos']) }}">{{ $utenteData['nome'] ?? '' }}</td>
                            @endif
                            {{-- Acesso correto à chave 'periodo' --}}
                            <td>{{ $periodoInfo['periodo'] ?? '' }}</td>
                            
                            @for ($dia = 1; $dia <= $diasNoMes; $dia++)
                                <td>{{ $periodoInfo['dias'][$dia] ?? '' }}</td>
                            @endfor
                        </tr>
                    @endforeach
                @else
                    {{-- Caso um utente não tenha períodos (raro, mas possível) --}}
                    <tr>
                        <td>{{ $utenteData['nome'] ?? 'Utente sem dados' }}</td>
                        <td colspan="{{ 1 + $diasNoMes }}">Sem períodos registados.</td>
                    </tr>
                @endif
                
                {{-- Linha separadora visual entre utentes (opcional para Excel, mas mantém a sua lógica anterior) --}}
                {{-- No Excel, isto criará uma linha extra. Se não quiser, remova este bloco @if --}}
                @if (!$loop->last && count($listaUtentes) > 1)
                    <tr><td colspan="{{ 2 + $diasNoMes }}" style="border-top: 1px solid #888;"></td></tr>
                @endif
            @endforeach
        @else
            <tr>
                <td colspan="{{ 2 + $diasNoMes }}">Nenhum dado para exportar.</td>
            </tr>
        @endif
    </tbody>
</table>