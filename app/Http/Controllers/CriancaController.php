<?php

namespace App\Http\Controllers;

use App\Models\Asset; // Ou o modelo que você está utilizando para Crianças
use Illuminate\Http\Request;

class CriancaController extends Controller
{
    public function store(Request $request, $utenteId)
    {
        // Valida os dados da requisição
        $request->validate([
            'nome' => 'required|string|max:255', // Exemplo de validação
            // Adicione outras validações conforme necessário
        ]);

        // Obtém o utente
        $utente = Asset::find($utenteId);
        if (!$utente) {
            abort(404);
        }

        // Cria a nova criança
        $crianca = new Asset(); // Ajuste conforme o modelo que está usando para crianças
        $crianca->name = $request->nome;
        // Preencha outros campos necessários aqui
        $crianca->save();

        // Redireciona de volta com uma mensagem de sucesso
        return redirect()->back()->with('success', 'Criança adicionada com sucesso!');
    }
}
