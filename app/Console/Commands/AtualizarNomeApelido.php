<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AtualizarNomeApelido extends Command
{
    protected $signature = 'apelido:atualizar';
    protected $description = 'Atualiza campo nome_apelido com abreviações dos nomes do meio, ignorando preposições';

    public function handle()
    {
        $preposicoes = ['de', 'da', 'do', 'das', 'dos', 'e', 'd'];

        DB::table('assets')
            ->whereNull('deleted_at')
            ->select('id', 'name', 'nome_apelido')
            ->chunkById(100, function ($registos) use ($preposicoes) {
                foreach ($registos as $registo) {
                    $partes = preg_split('/\s+/', trim($registo->name));
                    $total = count($partes);

                    if ($total <= 2) {
                        $apelido = $registo->name;
                    } else {
                        $primeiro = $partes[0];
                        $ultimo = $partes[$total - 1];
                        $meios = array_slice($partes, 1, $total - 2);
                        $iniciais = [];

                        foreach ($meios as $m) {
                            $word = mb_strtolower($m);
                            if (!in_array($word, $preposicoes)) {
                                $iniciais[] = mb_substr($m, 0, 1) . '.';
                            }
                        }

                        $apelido = trim("{$primeiro} " . implode(' ', $iniciais) . " {$ultimo}");
                    }

                    if ($registo->nome_apelido !== $apelido) {
                        DB::table('assets')
                            ->where('id', $registo->id)
                            ->update(['nome_apelido' => $apelido]);
                    }
                }
            });

        $this->info('Campo nome_apelido atualizado com abreviações (preposições ignoradas).');
    }
}
