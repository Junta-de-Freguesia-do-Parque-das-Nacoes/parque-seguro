<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use League\Csv\Reader;
use App\Models\Asset;
use App\Models\Responsavel;
use App\Models\ResponsavelUtente;
use Exception;

class ImportacaoController extends Controller
{
    public function index(Request $request)
{
    if ($request->hasFile('arquivo')) {
        $request->validate([
            'arquivo' => 'required|mimes:csv,txt|max:10240',
            'fonte' => 'required|string|in:edubox,site',
        ]);

        $file = $request->file('arquivo');
        $filePath = $file->storeAs('', $file->getClientOriginalName());
        $fullPath = storage_path("imports/" . $filePath);

        try {
            if (!file_exists($fullPath)) {
                Log::error("❌ Arquivo CSV não encontrado: " . $fullPath);
                return back()->with('error', 'Erro: Arquivo CSV não encontrado.');
            }

            $csv = Reader::createFromPath($fullPath, 'r');
            $csv->setHeaderOffset(0);

            $preview = [];
            foreach ($csv as $index => $row) {
                if ($index >= 10) break; // Limita a pré-visualização a 10 linhas
                $preview[] = $row;
            }

            return view('importacao.index', compact('preview', 'filePath'));
        } catch (Exception $e) {
            Log::error("❌ Erro ao processar o CSV: " . $e->getMessage());
            return back()->with('error', 'Erro ao processar o CSV: ' . $e->getMessage());
        }
    }

    return view('importacao.index');
}

    /**
     * Importação de dados do Edubox
     */
    protected function importarDeEdubox($filePath)
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $row) {
            Log::info("📌 Processando utente Serial: {$row['serial']}");

            $utente = Asset::where('serial', $row['serial'])->first();

            if (!$utente) {
                Log::warning("⚠️ Utente não encontrado: Serial {$row['serial']}");
                continue;
            }

            $this->importarResponsaveis($row, $utente);
        }
    }

    /**
     * Importação de dados do Site (com fotos)
     */
    protected function importarDeSite($filePath)
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $row) {
            Log::info("📌 Processando utente Serial: {$row['serial']}");

            $utente = Asset::where('serial', $row['serial'])->first();

            if (!$utente) {
                Log::warning("⚠️ Utente não encontrado: Serial {$row['serial']}");
                continue;
            }

            $this->importarResponsaveisComFoto($row, $utente);
        }
    }

    /**
     * Importação de responsáveis sem foto (Edubox)
     */
    protected function importarResponsaveis($row, Asset $utente)
    {
        $responsaveisProcessados = [];
    
        for ($i = 1; $i <= 6; $i++) {
            $nomeColuna = "nome_completo_$i";
            $docColuna = "nr_identificacao_$i";
            $contactoColuna = "contacto_$i";
            $emailColuna = "email_$i";
            $grauColuna = "grau_parentesco_$i";
            $tipoColuna = "tipo_responsavel_$i";
            $estadoColuna = "estado_autorizacao_$i";
    
            if (!isset($row[$nomeColuna]) || empty(trim($row[$nomeColuna]))) {
                continue;
            }
    
            $nrIdentificacao = !empty($row[$docColuna]) ? $row[$docColuna] : 'nao_informado_' . uniqid();
            $tipoResponsavel = !empty($row[$tipoColuna]) ? $row[$tipoColuna] : 'Autorizado'; // Valor padrão "Autorizado"
            $email = !empty($row[$emailColuna]) ? $row[$emailColuna] : null;
            $contacto = !empty($row[$contactoColuna]) ? $row[$contactoColuna] : null;
    
            // 🔍 Verificar se o responsável já existe pelo `nr_identificacao` ou `nome_completo`
            $responsavel = Responsavel::where('nr_identificacao', $nrIdentificacao)
                ->orWhere('nome_completo', $row[$nomeColuna])
                ->first();
    
            if ($responsavel) {
                // 🔄 Atualizar os dados apenas se estiverem vazios
                $responsavel->update([
                    'contacto' => $contacto ?? $responsavel->contacto,
                    'email' => $email ?? $responsavel->email,
                ]);
    
                // 🔄 Se o responsável já existir e for EE, mantém como EE
                if (isset($responsaveisProcessados[$responsavel->id])) {
                    if ($tipoResponsavel === 'Encarregado de Educacao') {
                        $responsaveisProcessados[$responsavel->id] = 'Encarregado de Educacao';
                    }
                } else {
                    $responsaveisProcessados[$responsavel->id] = $tipoResponsavel;
                }
            } else {
                // 🆕 Criar novo responsável
                $responsavel = Responsavel::create([
                    'nr_identificacao' => $nrIdentificacao,
                    'nome_completo' => $row[$nomeColuna],
                    'contacto' => $contacto,
                    'email' => $email,
                ]);
    
                $responsaveisProcessados[$responsavel->id] = $tipoResponsavel;
            }
    
            // 🔎 Criar ou atualizar a relação responsável - utente
            ResponsavelUtente::updateOrCreate(
                ['responsavel_id' => $responsavel->id, 'utente_id' => $utente->id],
                [
                    'grau_parentesco' => $row[$grauColuna] ?? 'Desconhecido',
                    'tipo_responsavel' => $responsaveisProcessados[$responsavel->id], // 🔹 Garante que EE tem prioridade
                    'estado_autorizacao' => $row[$estadoColuna] ?? 'Autorizado',
                ]
            );
    
            Log::info("✅ Responsável {$responsavel->nome_completo} associado ao utente {$utente->name} (Tipo: {$responsaveisProcessados[$responsavel->id]})");
        }
    }
    

    public function importar(Request $request)
{
    $request->validate([
        'arquivo_path' => 'required|string',
        'fonte' => 'required|string|in:edubox,site',
    ]);

    $filePath = $request->input('arquivo_path');
    $fullPath = str_starts_with($filePath, storage_path()) 
        ? $filePath 
        : storage_path("app/" . ltrim($filePath, '/'));

    if (!file_exists($fullPath)) {
        Log::error("❌ Arquivo CSV não encontrado: " . $fullPath);
        return back()->withErrors("Erro na importação: Arquivo não encontrado.");
    }

    try {
        if ($request->fonte === 'edubox') {
            $this->importarDeEdubox($fullPath);
        } elseif ($request->fonte === 'site') {
            $this->importarDeSite($fullPath);
        }

        return redirect()->route('importacao.index')->with('success', '✅ Importação concluída com sucesso!');
    } catch (Exception $e) {
        Log::error("❌ Erro na importação: " . $e->getMessage());
        return back()->withErrors('Erro na importação: ' . $e->getMessage());
    }
}

    /**
     * Importação de responsáveis com foto (Site)
     */
    protected function importarResponsaveisComFoto($row, Asset $utente)
    {
        for ($i = 1; $i <= 6; $i++) {
            $nomeColuna = "nome_completo_$i";
            $docColuna = "nr_identificacao_$i";
            $contactoColuna = "contacto_$i";
            $emailColuna = "email_$i";
            $fotoColuna = "foto_$i";
            $grauColuna = "grau_parentesco_$i";
            $tipoColuna = "tipo_responsavel_$i";
            $estadoColuna = "estado_autorizacao_$i";

            if (!isset($row[$nomeColuna]) || empty(trim($row[$nomeColuna]))) {
                continue;
            }

            // Verifica se o responsável já existe pelo nome
            $responsavel = Responsavel::where('nome_completo', $row[$nomeColuna])->first();

            if ($responsavel) {
                $responsavel->update([
                    'contacto' => $responsavel->contacto ?: ($row[$contactoColuna] ?? '000000000'),
                    'email' => $responsavel->email ?: ($row[$emailColuna] ?? 'nao_informado@example.com'),
                ]);
            } else {
                $responsavel = Responsavel::create([
                    'nr_identificacao' => $row[$docColuna] ?? 'nao_informado_' . uniqid(),
                    'nome_completo' => $row[$nomeColuna],
                    'contacto' => $row[$contactoColuna] ?? '000000000',
                    'email' => $row[$emailColuna] ?? 'nao_informado@example.com',
                ]);
            }

            if (!empty($row[$fotoColuna])) {
                $this->baixarFotoResponsavel($row[$fotoColuna], $responsavel);
            }

            ResponsavelUtente::updateOrCreate(
                ['responsavel_id' => $responsavel->id, 'utente_id' => $utente->id],
                ['grau_parentesco' => $row[$grauColuna] ?? 'Desconhecido']
            );

            Log::info("✅ Responsável {$responsavel->nome_completo} associado ao utente {$utente->name}");
        }
    }

    protected function baixarFotoResponsavel($urlFoto, $responsavel)
    {
        $extensao = pathinfo($urlFoto, PATHINFO_EXTENSION);
        $nomeArquivo = "foto_{$responsavel->id}." . $extensao;
        $caminhoDestino = "public/responsaveis/fotos/" . $nomeArquivo;

        if (!Storage::exists($caminhoDestino)) {
            $conteudo = Http::get($urlFoto)->body();
            Storage::put($caminhoDestino, $conteudo);
            $responsavel->update(['foto' => $caminhoDestino]);
        }
    }
}
