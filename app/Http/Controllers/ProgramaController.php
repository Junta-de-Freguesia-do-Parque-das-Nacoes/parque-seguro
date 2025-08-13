<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\CustomField;
use Illuminate\Http\Request;
use App\Exports\ProgramaExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;



class ProgramaController extends Controller
{
    public function index(Request $request)
    {
        $programaSelecionado = $request->input('programa');
        $program_fields = $this->getProgramFields();
        $assets = collect(); // default vazia

        if ($programaSelecionado && array_key_exists($programaSelecionado, $program_fields)) {
            $assets = Asset::whereNotNull($programaSelecionado)->get();
        }

        return view('programas.gestao', compact('assets', 'program_fields', 'programaSelecionado'));
    }

    public function limpar(Request $request)
{
    $programa = $request->input('programa');
    $assetIds = $request->input('asset_ids', []);

    if (!in_array($programa, array_keys($this->getProgramFields()))) {
        return back()->with('error', 'Programa inválido.');
    }

    foreach ($assetIds as $assetId) {
        $asset = Asset::find($assetId);
        if ($asset) {
            $asset->{$programa} = null; // Limpa a inscrição
            $asset->save();
        }
    }

    return redirect()->route('programas.gestao', ['programa' => $programa])
                     ->with('success', 'Inscrições limpas com sucesso.');
}


    public function gerirOpcoes($field_id)
    {
        $campo = CustomField::findOrFail($field_id);
        $valores = explode("\n", str_replace("\r", '', $campo->field_values));

        $temInscritos = Asset::whereNotNull($campo->db_column_name)->exists();

        return view('programas.editar_opcoes', compact('campo', 'valores', 'temInscritos'));
    }

    public function atualizarOpcoes(Request $request, $field_id)
{
    $field = CustomField::findOrFail($field_id);

    // Corrigido: usa o campo correto do modelo
    $dbColumn = $field->db_column_name;

    if (Asset::whereNotNull($dbColumn)->exists()) {
        return redirect()->route('programas.opcoes', ['field_id' => $field_id])
                         ->with('error', 'Não é possível editar este campo porque existem utentes inscritos.');
    }

    $novasOpcoes = trim($request->input('valores'));
    $field->field_values = $novasOpcoes;
    $field->save();

    return redirect()->route('programas.opcoes', ['field_id' => $field_id])
                     ->with('success', 'Opções atualizadas com sucesso.');
}


    private function getProgramFields()
{
    // Programas atuais fixos
    $fixed = [
        '_snipeit_ha_ferias_no_parque_67' => 'Há Férias No Parque',
        '_snipeit_parque_em_movimento_verao_68' => 'Parque em Movimento Verão',
        '_snipeit_parque_em_movimento_pascoa_69' => 'Parque em Movimento Páscoa',
        '_snipeit_aaaf_caf_ferias_pascoa_70' => 'AAAF/CAF Férias Páscoa',
        '_snipeit_aaaf_caf_ferias_verao_71' => 'AAAF/CAF Férias Verão',
        '_snipeit_parque_em_movimento_natal_72' => 'Parque em Movimento Natal',
        '_snipeit_aaaf_caf_ferias_carnaval_73' => 'AAAF/CAF Férias Carnaval',
        '_snipeit_ail_setembro_76' => 'AIL Setembro',
        '_snipeit_parque_em_movimento_setembro_77' => 'Parque em Movimento Setembro',
    ];

    // Campos que começam por "_snipeit_programa_"
    $automaticos = [];

    foreach (Schema::getColumnListing('assets') as $column) {
        if (Str::startsWith($column, '_snipeit_programa_')) {
            // Formatar nome (ex: "_snipeit_programa_teste_de_programa_78" → "Teste de Programa")
            $semPrefixo = preg_replace('/^_snipeit_programa_/', '', $column);
            $semSufixo = preg_replace('/_\d+$/', '', $semPrefixo); // remove _78
            $label = Str::of($semSufixo)->replace('_', ' ')->title();
            $automaticos[$column] = $label;
        }
    }

    return $fixed + $automaticos; // Junta os fixos com os automáticos
}



    public function exportar(Request $request)
{
    $programa = $request->query('programa');
    $program_fields = $this->getProgramFields(); // <- usar o mesmo método que no resto do controller

    if (!$programa || !isset($program_fields[$programa])) {
        return redirect()->back()->with('error', 'Programa inválido.');
    }

    $assets = Asset::whereNotNull($programa)->get();
    $label = $program_fields[$programa];

    return Excel::download(new \App\Exports\ProgramaExport($assets, $programa, $label), 'Inscricoes_'.$label.'.xlsx');
}



}
