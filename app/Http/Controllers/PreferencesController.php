<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class PreferencesController extends Controller
{
    /**
     * Atualizar preferências via modal (administrador).
     */
    public function update(Request $request, $id)
    {
        try {
            // Encontrar o asset
            $asset = Asset::findOrFail($id);
    
            // Atualizar as preferências de notificação
            $asset->receive_checkin_notifications = $request->input('receive_checkin_notifications', false);
            $asset->receive_checkout_notifications = $request->input('receive_checkout_notifications', false);
            $asset->receive_self_notifications = $request->input('receive_self_notifications', false);
            $asset->save();
    
            // Recuperar o responsável associado a este asset (utente)
            $responsavel = DB::table('responsaveis_utentes')
                ->join('responsaveis', 'responsaveis.id', '=', 'responsaveis_utentes.responsavel_id')
                ->where('responsaveis_utentes.utente_id', $asset->id)
                ->first();
    
            // Verificar se o responsável foi encontrado
            if ($responsavel) {
                $email = $responsavel->email; // O email do responsável
            } else {
                $email = null; // Caso não encontre o responsável, definimos como null
            }
    
            // Retornar a resposta com as preferências e o email do responsável
            return response()->json([
                'success' => true,
                'message' => 'Preferências salvas com sucesso!',
                'email' => $email, // Retornando o email do responsável
            ]);
        } catch (\Exception $e) {
            // Caso haja algum erro, retornar uma mensagem de erro
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar preferências.',
            ], 500);
        }
    }
    

    /**
     * Exibir preferências para edição via token.
     */
    public function editWithToken($id, $token)
{
    try {
        $asset = Asset::where('id', $id)
            ->where('preference_token', $token)
            ->firstOrFail();

        return view('preferences.token', compact('asset', 'token'));
    } catch (\Exception $e) {
        \Log::error('Erro ao carregar as preferências com token: ', ['error' => $e->getMessage()]);
        return redirect()->route('home')->with('error', 'Erro ao acessar preferências.');
    }
}


    /**
     * Enviar código de verificação para o e-mail associado ao Asset.
     */
    public function sendVerificationCode(Request $request, $id)
{
    try {
        // Buscar o asset (utente)
        $asset = Asset::findOrFail($id);

        // Buscar o responsável do tipo "Encarregado de Educação" relacionado ao asset
        $responsavel = $asset->responsaveis()
            ->wherePivot('tipo_responsavel', 'Encarregado de Educacao')
            ->first();

        // Verificar se foi encontrado o responsável e se possui um e-mail
        if ($responsavel && $responsavel->email) {
            $emailResponsavel = $responsavel->email;
        } else {
            // Caso não encontre o responsável, lançar erro ou definir e-mail padrão
            throw new \Exception('Responsável (Encarregado de Educação) não encontrado ou sem e-mail.');
        }

        // Gerar código de verificação
        $verificationCode = Str::random(6);
        $asset->verification_code = $verificationCode;
        $asset->verification_code_expires_at = now()->addMinutes(10);
        $asset->save();

        // Enviar o código de verificação para o e-mail do responsável (sempre)
        Mail::send('emails.verificationCode', [
            'code' => $verificationCode,
            'asset' => $asset
        ], function ($message) use ($emailResponsavel) {
            $message->to($emailResponsavel)
                ->subject('Código de verificação');
        });

        return redirect()->route('preferences.token.edit', ['id' => $id, 'token' => $asset->preference_token])
            ->with('success', 'Código enviado com sucesso para o email: ' . $emailResponsavel);
    } catch (\Exception $e) {
        \Log::error('Erro ao enviar o código de verificação: ', ['error' => $e->getMessage()]);
        return redirect()->route('preferences.token.edit', ['id' => $id, 'token' => $asset->preference_token])
            ->with('error', 'Erro ao enviar código de verificação. Por favor, tente novamente.');
    }
}


    /**
     * Verificar e atualizar preferências via token e código.
     */
   public function verifyAndUpdateWithToken(Request $request, $id, $token)
{
    $request->validate([
        'verification_code' => 'required|string',
        'receive_checkin_notifications' => 'nullable|boolean',
        'receive_checkout_notifications' => 'nullable|boolean',
        'receive_self_notifications' => 'nullable|boolean',
    ], [
        'verification_code.required' => 'Por favor, insira o código de verificação.',
        'verification_code.string' => 'O código de verificação deve ser um texto válido.',
    ]);

    try {
        // Valida o código e sua expiração
        $asset = Asset::where('id', $id)
            ->where('preference_token', $token)
            ->first();

        if (!$asset) {
            return redirect()->route('preferences.token.edit', ['id' => $id, 'token' => $token])
                ->with('error', 'O token fornecido é inválido.');
        }

        if ($asset->verification_code !== $request->input('verification_code')) {
            return redirect()->route('preferences.token.edit', ['id' => $id, 'token' => $token])
                ->with('error', 'O código de verificação está incorreto.');
        }

        if ($asset->verification_code_expires_at <= now()) {
            return redirect()->route('preferences.token.edit', ['id' => $id, 'token' => $token])
                ->with('error', 'O código de verificação expirou. Por favor, solicite um novo código.');
        }

        // Atualiza as preferências
        $asset->receive_checkin_notifications = $request->input('receive_checkin_notifications', false);
        $asset->receive_checkout_notifications = $request->input('receive_checkout_notifications', false);
        $asset->receive_self_notifications = $request->input('receive_self_notifications', false);

        // Limpa o código de verificação
        $asset->verification_code = null;
        $asset->verification_code_expires_at = null;
        $asset->save();

      // Buscar o responsável do tipo "Encarregado de Educação" associado ao asset
$responsavelEE = $asset->responsaveis()
->wherePivot('tipo_responsavel', 'Encarregado de Educacao')
->first();

// Verificar se o responsável foi encontrado e obter o email
$emailResponsavelEE = $responsavelEE ? $responsavelEE->email : 'Email não encontrado';

// Atualizar a mensagem de sucesso
$successMessage = "Preferências atualizadas com sucesso! Criança {$asset->name}, Email Associado (Encarregado de Educação): {$emailResponsavelEE}";

        
        return redirect()->route('preferences.token.edit', ['id' => $id, 'token' => $token])
            ->with('success', $successMessage);
    } catch (\Exception $e) {
        \Log::error('Erro ao verificar e salvar preferências: ', ['error' => $e->getMessage()]);
        return redirect()->route('preferences.token.edit', ['id' => $id, 'token' => $token])
            ->with('error', 'Erro ao salvar preferências. Por favor, tente novamente.');
    }
}






}
