@extends('layouts.painel')
@section('titulo','Programação')
@section('conteudo')
<h1>Programação</h1>
<p class="mut">Cada dia tem o botão <b>+ Nova atração</b>. Para criar outro dia, use o formulário no final da página.</p>
@foreach($dias as $dia)
<div class="card">
  <form method="post" action="{{ route('painel.programacao.dia.update',$dia) }}" class="row">@csrf @method('PUT')
    <div><label>Sigla</label><input type="text" name="sigla" value="{{ $dia->sigla }}"></div>
    <div><label>Nome</label><input type="text" name="nome" value="{{ $dia->nome }}"></div>
    <div><label>Data (dd/mm)</label><input type="text" name="data" value="{{ $dia->data }}"></div>
    <div style="align-self:end"><button class="btn sec sm">Salvar dia</button></div>
  </form>
  <h2>Atrações <a class="btn sm" href="#nova-{{ $dia->id }}">+ Nova atração</a></h2>
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
  <h2 id="nova-{{ $dia->id }}">Nova atração em {{ $dia->nome }} ({{ $dia->data }})</h2>
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

<div class="card">
  <h2 style="margin-top:0">Novo dia</h2>
  <form method="post" action="{{ route('painel.programacao.dia.store') }}" class="row">@csrf
    <div><label>Código (ex.: 16)</label><input type="text" name="slug" required maxlength="10"></div>
    <div><label>Sigla (ex.: SEG)</label><input type="text" name="sigla" required maxlength="5"></div>
    <div><label>Nome (ex.: Segunda)</label><input type="text" name="nome" required maxlength="30"></div>
    <div><label>Data (dd/mm)</label><input type="text" name="data" required maxlength="10" placeholder="16/11"></div>
    <div style="align-self:end"><button class="btn">Criar dia</button></div>
  </form>
</div>
@endsection
