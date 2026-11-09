@extends('layouts.painel')
@section('titulo',$stand->nome)
@section('conteudo')
<h1>{{ $stand->nome }}</h1>
<p class="mut">{{ $stand->localizacao() }} · As alterações entram no ar no app na hora.</p>
<form method="post" enctype="multipart/form-data" action="{{ route('meu-stand.update',$stand) }}">@csrf @method('PUT')
  <div class="card">@include('partials.perfil-campos')</div>
  <button class="btn">Salvar e publicar</button>
</form>
@endsection
