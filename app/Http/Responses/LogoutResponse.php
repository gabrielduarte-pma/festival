<?php

namespace App\Http\Responses;

use Inertia\Inertia;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

/** Ao sair, volta para o login com navegação completa (evita abrir HTML Blade dentro do Inertia). */
class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request)
    {
        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return new \Illuminate\Http\JsonResponse('', 204);
        }

        return $request->header('X-Inertia')
            ? Inertia::location(url('/login'))
            : redirect('/login');
    }
}
