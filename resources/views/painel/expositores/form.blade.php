@extends('layouts.painel')
@section('titulo','Expositor')
@section('conteudo')
<h1>{{ $user->exists ? $user->name : 'Novo expositor' }}</h1>
<form method="post" action="{{ $user->exists ? route('painel.expositores.update',$user) : route('painel.expositores.store') }}">
@csrf @if($user->exists) @method('PUT') @endif
<div class="card">
  <div class="row">
    <div><label>Nome</label><input type="text" name="name" required value="{{ old('name',$user->name) }}"></div>
    <div><label>E-mail (login)</label><input type="email" name="email" required value="{{ old('email',$user->email) }}"></div>
    <div><label>Telefone</label><input type="tel" name="telefone" value="{{ old('telefone',$user->telefone) }}"></div>
  </div>
  @if($user->exists)<label><input type="checkbox" name="ativo" value="1" @checked(old('ativo',$user->ativo))> Acesso liberado</label>@endif
  <label>Estandes que ele pode editar</label>
  <select name="stands[]" multiple size="12">
    @foreach($stands as $s)<option value="{{ $s->id }}" @selected($selecionados->contains($s->id))>{{ $s->nome }}{{ $s->numero ? ' · nº '.$s->numero : '' }}</option>@endforeach
  </select>
  <p class="mut">Segure Ctrl/⌘ para marcar mais de um.</p>
  @unless($user->exists)
    <label class="inline" style="margin-top:14px"><input type="checkbox" name="enviar_email" value="1" @checked(old('enviar_email', true))> Enviar o convite por e-mail agora</label>
    <p class="mut" style="margin:4px 0 0">O link também aparece na tela depois de salvar, para você mandar pelo WhatsApp.</p>
  @endunless
  <p style="margin-top:14px"><button class="btn">{{ $user->exists ? 'Salvar' : 'Criar expositor' }}</button></p>
</div>
</form>
@if($user->exists)
<div class="card">
  <b>Link de acesso</b>
  <p class="mut">
    @if($user->convite_enviado_em)
      ✔ Último e-mail de convite enviado em {{ $user->convite_enviado_em->format('d/m/Y \à\s H:i') }}.
    @else
      Nenhum e-mail de convite foi enviado ainda.
    @endif
    @if($user->invite_token) · Convite pendente (ainda não criou a senha). @else · Já criou a senha. @endif
  </p>
  <p class="mut">Gerar um novo link serve para o primeiro acesso ou para quem esqueceu a senha. O link anterior deixa de valer.</p>
  <form method="post" action="{{ route('painel.expositores.convite',$user) }}" style="display:flex;gap:8px;flex-wrap:wrap">@csrf
    <button class="btn" name="enviar" value="1">Gerar novo link e enviar por e-mail</button>
    <button class="btn sec" name="enviar" value="0">Só gerar o link</button>
  </form>
</div>
<form method="post" action="{{ route('painel.expositores.destroy',$user) }}" onsubmit="return confirm('Remover este expositor?')">@csrf @method('DELETE')<button class="btn danger sm">Remover expositor</button></form>
@endif
@endsection
