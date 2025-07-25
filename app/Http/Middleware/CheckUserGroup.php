<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserGroup
{
    public function handle(Request $request, Closure $next, $groupName)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Acesso negado.');
        }

        // Verifica se o utilizador pertence ao grupo específico
        $userGroups = $user->groups()->pluck('name')->toArray();

        if (in_array($groupName, $userGroups)) {
            abort(403, 'Acesso negado.');
        }

        return $next($request);
    }
}
