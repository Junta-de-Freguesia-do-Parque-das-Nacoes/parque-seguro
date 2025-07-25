<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Asset;
use Carbon\Carbon;

class ResetEstadoUtentes extends Command
{
    protected $signature = 'utentes:reset {--modo=}';
    protected $description = 'Atualiza o estado dos utentes: às 21h recolhe todos, às 10h recolhe todos exceto quem está em programa hoje';

    public function handle()
    {
        $modo = $this->option('modo');
        $hoje = now()->toDateString();

        if (!in_array($modo, ['21h', '10h'])) {
            $this->error("Modo inválido. Usa --modo=21h ou --modo=10h");
            return 1;
        }

        if ($modo === '21h') {
            $this->recolherTodos();
        } else {
            $this->recolherExcetoQuemTemProgramaHoje($hoje);
        }
    }

    private function recolherTodos()
    {
        $utentes = DB::table('assets')
            ->where('status_id', 23)
            ->pluck('id');

        if ($utentes->isEmpty()) {
            $this->warn('Nenhum utente com estado "Em Atividade" encontrado para recolher.');
            return;
        }

        DB::table('assets')->whereIn('id', $utentes)->update(['status_id' => 25]);

        foreach ($utentes as $id) {
            DB::table('action_logs')->insert([
                'user_id' => null,
                'action_type' => 'checkout',
                'item_id' => $id,
                'item_type' => 'asset',
                'target_id' => $id,
                'target_type' => 'asset',
                'note' => 'Saída automática: estado alterado para "Recolhido" às 21h.',
                'action_source' => 'cron-recolha-21h',
                'action_date' => now(),
                'log_meta' => $this->gerarLogMeta('21h'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->info($utentes->count() . ' utente(s) passaram para estado Recolhido às 21h.');
    }

private function recolherExcetoQuemTemProgramaHoje($hoje)

{
    $dataHoje = Carbon::parse($hoje);

    // Verifica se estamos no período de férias (inclusive)
    if ($dataHoje->between(Carbon::create(2025, 7, 7), Carbon::create(2025, 9, 12))) {
        $this->info("⛔ Recolha automática das 10h desativada durante o período de férias (07-07-2025 a 12-09-2025)");


        return;
    }

    $hoje = Carbon::today()->toDateString();

    $idsComEntradaHoje = DB::table('action_logs')
        ->whereDate('created_at', $hoje)
        ->where('action_type', 'checkin')
        ->pluck('target_id');

    $assets = Asset::where('status_id', 23) // Em Atividade
        ->whereIn('id', $idsComEntradaHoje)
        ->where('company_id', '!=', 39) // Internos
        ->whereIn('model_id', [261, 264]) // AAAF / CAF
        ->get();

    foreach ($assets as $asset) {
        // Verifica se está em algum programa ativo hoje
        $emPrograma = false;

        $programasCampos = [
            '_snipeit_ha_ferias_no_parque_67',
            '_snipeit_parque_em_movimento_verao_68',
            '_snipeit_parque_em_movimento_pascoa_70',
            '_snipeit_parque_em_movimento_natal_73',
            '_snipeit_aaaf_caf_ferias_pascoa_71',
            '_snipeit_aaaf_caf_ferias_verao_72',
            '_snipeit_aaaf_caf_ferias_carnaval_74',
        ];

        foreach ($programasCampos as $campo) {
            $valor = $asset->{$campo};

            if (!$valor) continue;

            if (str_contains($valor, ' a ')) {
                // Intervalo de datas (ex: 2025-07-01 a 2025-07-15)
                [$inicio, $fim] = explode(' a ', $valor);
                if ($hoje >= $inicio && $hoje <= $fim) {
                    $emPrograma = true;
                    break;
                }
            } elseif ($valor === $hoje) {
                // Data única
                $emPrograma = true;
                break;
            }
        }

        if (!$emPrograma) {
            $this->info("✅ Recolhido: {$asset->name}");
            $asset->status_id = 25; // Recolhido
            $asset->save();
        } else {
            $this->info("⏭️ Ignorado (programa ativo): {$asset->name}");
        }
    }
}



    private function gerarLogMeta(string $modo): string
    {
        return json_encode([
            'modo' => $modo,
            'servidor' => gethostname(),
        ], JSON_FORCE_OBJECT);
    }

    private function dataIgual($dataDDMMYYYY, $hojeISO)
    {
        $data = \DateTime::createFromFormat('d/m/Y', $dataDDMMYYYY);
        return $data && $data->format('Y-m-d') === $hojeISO;
    }

    private function dataDentroIntervalo($inicioDDMMYYYY, $fimDDMMYYYY, $hojeISO)
    {
        $inicio = \DateTime::createFromFormat('d/m/Y', $inicioDDMMYYYY);
        $fim = \DateTime::createFromFormat('d/m/Y', $fimDDMMYYYY);
        $hoje = \DateTime::createFromFormat('Y-m-d', $hojeISO);

        return $inicio && $fim && $hoje >= $inicio && $hoje <= $fim;
    }
}
