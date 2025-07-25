<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle; // Opcional, para o nome da aba
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Opcional

class MapaPresencasExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $dadosParaView; // Alterei o nome para evitar confusão com a chave na view
    protected $ano;
    protected $mes;
    protected $diasNoMes; // Nova propriedade para guardar o número de dias
    protected $turmaFiltrada; // Para passar para a view para o título
    protected $utenteFiltrado; // Para passar para a view para o título

    // Ajustar o construtor para aceitar o quarto parâmetro
    public function __construct($dadosHierarquicos, $ano, $mes, $numeroDeDiasNoMes)
    {
        $this->dadosParaView = $dadosHierarquicos; // Estes são os dados hierárquicos do controlador
        $this->ano = $ano;
        $this->mes = $mes;
        $this->diasNoMes = $numeroDeDiasNoMes; // Guardar o número de dias

        // Opcional: buscar os filtros da request para usar nos títulos da view Excel
        // É melhor se o controlador passar estes explicitamente se forem sempre necessários,
        // mas request() também pode funcionar aqui se o contexto da request estiver disponível.
        $this->turmaFiltrada = request('turma');
        $this->utenteFiltrado = request('utente');
    }

    public function view(): View
    {
        // Passar todas as variáveis necessárias para a view
        return view('history.export-mapa-excel', [
            // A sua view Blade export-mapa-excel.blade.php está a usar $listaUtentes
            // para iterar os dados principais. Então, vamos passar os dados com essa chave.
            'listaUtentes' => $this->dadosParaView,
            'ano' => $this->ano,
            'mes' => $this->mes,
            'diasNoMes' => $this->diasNoMes,   // <--- PASSAR PARA A VIEW
            'turma' => $this->turmaFiltrada,     // Passar para usar no @if (isset($turma))
            'utente' => $this->utenteFiltrado   // Passar para usar no @if (isset($utente))
        ]);
    }

    public function title(): string
    {
        // Define o nome da aba na folha de cálculo do Excel
        return \Carbon\Carbon::createFromDate($this->ano, $this->mes, 1)->translatedFormat('F Y');
    }
}