@extends('layouts.simples')
@section('titulo','Criar senha')
@section('conteudo')
<div class="card">
  <h2 style="margin-top:0">Olá, {{ $user->name }}!</h2>
  <p>Crie uma senha para acessar a área do expositor e preencher as informações do seu estande no app do festival.</p>
  <form method="post" action="{{ route('convite.aceitar', $token) }}">@csrf
    <label>E-mail de acesso</label><input type="email" value="{{ $user->email }}" disabled>
    <label>Senha (mín. 8 caracteres)</label><input type="password" name="password" required minlength="8" autocomplete="new-password">
    <label>Repita a senha</label><input type="password" name="password_confirmation" required autocomplete="new-password">
    <p><button class="btn">Criar senha e entrar</button></p>
  </form>
</div>
@endsection
