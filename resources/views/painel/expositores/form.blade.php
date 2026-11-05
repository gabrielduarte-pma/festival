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
  <label>Stands que ele pode editar</label>
  <select name="stands[]" multiple size="12">
    @foreach($stands as $s)<option value="{{ $s->id }}" @selected($selecionados->contains($s->id))>{{ $s->nome }}{{ $s->numero ? ' · nº '.$s->numero : '' }}</option>@endforeach
  </select>
  <p class="mut">Segure Ctrl/⌘ para marcar mais de um.</p>
  <button class="btn">Salvar</button>
</div>
</form>
@if($user->exists)
<form method="post" action="{{ route('painel.expositores.convite',$user) }}" class="card">@csrf
  <b>Link de acesso</b>
  <p class="mut">Gera um novo link (o anterior deixa de valer). Serve para o primeiro acesso ou para quem esqueceu a senha.</p>
  <button class="btn sec">Gerar novo link de convite</button>
</form>
<form method="post" action="{{ route('painel.expositores.destroy',$user) }}" onsubmit="return confirm('Remover este expositor?')">@csrf @method('DELETE')<button class="btn danger sm">Remover expositor</button></form>
@endif
@endsection
