@extends('layouts.painel')
@section('titulo', $stand->exists ? $stand->nome : 'Novo stand')
@section('conteudo')
<h1>{{ $stand->exists ? $stand->nome : 'Novo stand' }}</h1>
<form method="post" enctype="multipart/form-data" action="{{ $stand->exists ? route('painel.stands.update',$stand) : route('painel.stands.store') }}">
@csrf @if($stand->exists) @method('PUT') @endif
<div class="card">
  <h2 style="margin-top:0">Dados do stand</h2>
  <div class="row">
    <div><label>Nome</label><input type="text" name="nome" required value="{{ old('nome',$stand->nome) }}"></div>
    <div><label>Categoria</label><select name="categoria">@foreach(config('festival.categorias') as $c)<option @selected(old('categoria',$stand->categoria)===$c)>{{ $c }}</option>@endforeach</select></div>
    <div><label>Nº do stand</label><input type="text" name="numero" value="{{ old('numero',$stand->numero) }}"></div>
  </div>
  <div class="row">
    <div><label>Ilha</label><input type="number" name="ilha" value="{{ old('ilha',$stand->ilha) }}"></div>
    <div><label>Posição</label><input type="number" name="posicao" value="{{ old('posicao',$stand->posicao) }}"></div>
    <div><label>Lado</label><select name="lado"><option value="">—</option><option value="L" @selected(old('lado',$stand->lado)==='L')>Esquerdo</option><option value="R" @selected(old('lado',$stand->lado)==='R')>Direito</option></select></div>
    <div><label>Referência</label><input type="text" name="referencia" value="{{ old('referencia',$stand->referencia) }}"></div>
  </div>
  <label>Retângulo no mapa <span class="mut">(x, y, largura, altura em px do SVG {{ config('festival.mapa.w') }}×{{ config('festival.mapa.h') }})</span></label>
  <div class="row">
    <input type="number" name="rect_x" required value="{{ old('rect_x',$stand->rect_x) }}" placeholder="x">
    <input type="number" name="rect_y" required value="{{ old('rect_y',$stand->rect_y) }}" placeholder="y">
    <input type="number" name="rect_w" required value="{{ old('rect_w',$stand->rect_w ?? 80) }}" placeholder="largura">
    <input type="number" name="rect_h" required value="{{ old('rect_h',$stand->rect_h ?? 60) }}" placeholder="altura">
  </div>
  @include('partials.mapa-picker')
  <label><input type="checkbox" name="ativo" value="1" @checked(old('ativo',$stand->ativo))> Ativo (aparece no app)</label>
</div>
<div class="card">
  <h2 style="margin-top:0">Perfil público</h2>
  @include('partials.perfil-campos')
</div>
<button class="btn">Salvar</button>
<a class="btn sec" href="{{ route('painel.stands.index') }}">Voltar</a>
</form>
@if($stand->exists)
<form method="post" action="{{ route('painel.stands.destroy',$stand) }}" style="margin-top:24px" onsubmit="return confirm('Remover este stand definitivamente?')">@csrf @method('DELETE')<button class="btn danger sm">Remover stand</button></form>
@endif
@endsection
