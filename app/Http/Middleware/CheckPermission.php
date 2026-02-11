<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Se não foram especificadas permissões, apenas verifica se está autenticado
        if (empty($permissions)) {
            return $next($request);
        }

        // Verifica se o usuário tem uma das permissões necessárias (ex: "2,3" = nível 2 ou 3)
        foreach ($permissions as $permission) {
            $levels = array_map('intval', explode(',', (string) $permission));
            foreach ($levels as $level) {
                if ($user->permission_level == $level) {
                    return $next($request);
                }
            }
        }

        // Se chegou aqui, o usuário não tem permissão
        return redirect()->route('dashboard')
            ->with('error', 'Você não tem permissão para acessar esta página.');
    }
}
