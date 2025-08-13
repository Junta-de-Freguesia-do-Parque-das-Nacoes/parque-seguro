<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\EnviarCartaoPorEmailJob;

class CartaoController extends Controller
{
    public function enviarSelecionados(Request $request)
    {
        $ids = explode(',', $request->input('ids_selecionados'));
        $template = $request->input('template');

        foreach ($ids as $id) {
            dispatch(new EnviarCartaoPorEmailJob($id));
        }

        return back()->with('success', 'Cartões estão a ser enviados em background para os utentes selecionados.');
    }
}