@extends('layouts.painel')
@section('titulo','Meu estande')
@section('conteudo')
<h1>Meu estande</h1>
@forelse($stands as $s)
<div class="card"><b>{{ $s->nome }}</b> <span class="mut">{{ $s->localizacao() }}</span>
<p><a class="btn" href="{{ route('meu-stand.edit',$s) }}">Editar informações</a></p></div>
@empty
<div class="card">Nenhum estande está vinculado ao seu acesso ainda. Fale com a organização do festival.</div>
@endforelse
@endsection
