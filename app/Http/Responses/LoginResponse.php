<?php

namespace App\Http\Responses;

use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Laravel\Fortify\Fortify;

/**
 * Depois do login, o painel (/painel) e a área do expositor (/meu-stand) são páginas Blade,
 * não páginas Inertia. Por isso, quando o login vem do formulário Inertia, forçamos uma
 * navegação completa (Inertia::location) em vez de deixar o Inertia abrir o HTML num modal.
 */
class LoginResponse implements LoginResponseContract, TwoFactorLoginResponseContract
{
    public function toResponse($request)
    {
        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json(['two_factor' => false]);
        }

        $redirect = redirect()->intended(Fortify::redirects('login'));

        return $request->header('X-Inertia')
            ? Inertia::location($redirect->getTargetUrl())
            : $redirect;
    }
}
