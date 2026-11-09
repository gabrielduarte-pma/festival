<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ConviteController extends Controller
{
    public function show(string $token)
    {
        $user = User::porConvite($token);

        return $user
            ? view('auth.convite', ['user' => $user, 'token' => $token])
            : response()->view('auth.convite-invalido', [], 410);
    }

    public function aceitar(Request $request, string $token)
    {
        $user = User::porConvite($token);
        abort_unless($user, 410, 'Convite inválido ou expirado.');

        $dados = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->forceFill([
            'password' => $dados['password'],
            'invite_token' => null,
            'invite_expires_at' => null,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/meu-stand')->with('ok', 'Senha criada! Preencha as informações do seu estande.');
    }
}
