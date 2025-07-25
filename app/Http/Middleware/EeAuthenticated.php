<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- **IMPORTANTE: Adicionar esta linha se não tiveres**
use Illuminate\Support\Facades\Redirect;

class EeAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // **ALTERAÇÃO CRUCIAL AQUI:**
        // Usar o guard 'ee' do Laravel para verificar se o utilizador está autenticado.
        if (!Auth::guard('ee')->check()) {
            // Se o utilizador NÃO estiver autenticado pelo guard 'ee',
            // limpamos quaisquer variáveis de sessão personalizadas remanescentes
            // para garantir um estado limpo e redirecionamos para o login.
            session()->forget(['ee_responsavel_id', 'ee_session_expires_at']);
            return redirect()->route('ee.login')->withErrors(['email' => 'Sessão expirada. Faça login novamente.']);
        }

        // Se o utilizador ESTIVER autenticado pelo guard 'ee',
        // podemos então adicionar uma verificação de expiração de sessão manual,
        // se quiseres uma mensagem de erro mais específica por inatividade,
        // ou se a tua lógica de negócio exigir um controlo de tempo mais granular
        // para além do que o Laravel já faz por padrão com as sessões.
        if (session()->has('ee_session_expires_at') && now()->timestamp > session('ee_session_expires_at')) {
            // Se a sessão manual expirou, também fazemos logout do guard do Laravel
            // para garantir consistência e redirecionamos com uma mensagem específica.
            Auth::guard('ee')->logout();
            session()->forget(['ee_responsavel_id', 'ee_session_expires_at']);
            return redirect()->route('ee.login')->withErrors(['email' => 'Sessão expirada por inatividade.']);
        }

        // Se tudo estiver OK, o utilizador está autenticado e a sessão é válida,
        // permitimos que a requisição continue.
        return $next($request);
    }
}