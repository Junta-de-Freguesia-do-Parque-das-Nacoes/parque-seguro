<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Responsavel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AtualizarEstadosResponsaveis extends Command
{
    protected $signature = 'responsaveis:atualizar-estados';
    protected $description = 'Atualiza automaticamente o estado de autorização dos responsáveis conforme a data atual.';

    public function handle()
    {
        \Log::info('Comando responsaveis:atualizar-estados executado!');
        
        $hoje = Carbon::now();

        // Buscando todos os responsáveis e suas relações com os utentes
        $responsaveisUtentes = DB::table('responsaveis_utentes')->get();

        foreach ($responsaveisUtentes as $responsavelUtente) {
            // Encontrar o responsável
            $responsavel = Responsavel::find($responsavelUtente->responsavel_id);

            // Verifica se o responsável existe e se as datas de autorização existem
            if ($responsavel && $responsavelUtente->data_inicio_autorizacao && $responsavelUtente->data_fim_autorizacao) {
                // Atualizar o estado de autorização com base nas datas
                $novoEstado = $responsavel->atualizarEstadoAutorizacao($responsavelUtente->utente_id);

                // Verificar se o estado atual é diferente do novo estado
                if ($responsavelUtente->estado_autorizacao !== $novoEstado) {
                    // Atualizar o estado apenas se houver mudança
                    DB::table('responsaveis_utentes')
                        ->where('id', $responsavelUtente->id)
                        ->update(['estado_autorizacao' => $novoEstado]);
                }
            }
        }

        $this->info('Estados dos responsáveis atualizados com sucesso!');
    }
}
