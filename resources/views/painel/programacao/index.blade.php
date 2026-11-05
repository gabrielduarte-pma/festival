@extends('layouts.painel')
@section('titulo','Programação')
@section('conteudo')
<h1>Programação</h1>
@foreach($dias as $dia)
<div class="card">
  <form method="post" action="{{ route('painel.programacao.dia.update',$dia) }}" class="row">@csrf @method('PUT')
    <div><label>Sigla</label><input type="text" name="sigla" value="{{ $dia->sigla }}"></div>
    <div><label>Nome</label><input type="text" name="nome" value="{{ $dia->nome }}"></div>
    <div><label>Data (dd/mm)</label><input type="text" name="data" value="{{ $dia->data }}"></div>
    <div style="align-self:end"><button class="btn sec sm">Salvar dia</button></div>
  </form>
  <h2>Atrações</h2>
  @foreach($dia->atracoes as $a)
  <form method="post" enctype="multipart/form-data" action="{{ route('painel.programacao.atracao.update',$a) }}" style="border-top:1px solid var(--line);padding:8px 0">@csrf @method('PUT')
    <div class="row">
      <div><label>Nome</label><input type="text" name="nome" value="{{ $a->nome }}"></div>
      <div><label>Hora</label><input type="text" name="hora" value="{{ $a->hora }}"></div>
      <div><label>Palco</label><input type="text" name="palco" value="{{ $a->palco }}"></div>
      <div><label>Ordem</label><input type="number" name="ordem" value="{{ $a->ordem }}"></div>
    </div>
    <label class="inline"><input type="checkbox" name="destaque" value="1" @checked($a->destaque)> destaque</label>
    <label class="inline"><input type="checkbox" name="flip" value="1" @checked($a->flip)> espelhar</label>
    <label>Foto (PNG/WebP recortado)</label><input type="file" name="imagem" accept="image/*">
    @if($a->img)<label class="inline"><input type="checkbox" name="remover_imagem" value="1"> remover foto atual</label>@endif
    <p><button class="btn sm">Salvar</button></p>
  </form>
  <form method="post" action="{{ route('painel.programacao.atracao.destroy',$a) }}" onsubmit="return confirm('Remover?')">@csrf @method('DELETE')<button class="btn danger sm">Remover “{{ $a->nome }}”</button></form>
  @endforeach
  <h2>Nova atração</h2>
  <form method="post" enctype="multipart/form-data" action="{{ route('painel.programacao.atracao.store',$dia) }}">@csrf
    <div class="row">
      <div><input type="text" name="nome" placeholder="Nome" required></div>
      <div><input type="text" name="hora" placeholder="Hora"></div>
      <div><input type="text" name="palco" placeholder="Palco"></div>
    </div>
    <label class="inline"><input type="checkbox" name="destaque" value="1"> destaque</label>
    <input type="file" name="imagem" accept="image/*"> <button class="btn sm">Adicionar</button>
  </form>
</div>
@endforeach
@endsection
