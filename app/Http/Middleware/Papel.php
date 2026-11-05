<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Papel
{
    public function handle(Request $request, Closure $next, string $papel): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->ativo || $user->role !== $papel) {
            // Admin que entra na área do expositor (ou vice-versa) volta para a própria área.
            if ($user->ativo) {
                return redirect($user->isAdmin() ? '/painel' : '/meu-stand');
            }
            abort(403, 'Conta desativada.');
        }

        return $next($request);
    }
}
