@extends('layouts.painel')
@section('titulo','Expositores')
@section('conteudo')
<h1>Expositores <a class="btn sm" href="{{ route('painel.expositores.create') }}">+ Novo</a></h1>
<div class="card"><table><tr><th>Nome</th><th>E-mail</th><th>Estandes</th><th>Acesso</th><th></th></tr>
@foreach($expositores as $e)
<tr><td><b>{{ $e->name }}</b></td><td>{{ $e->email }}</td><td>{{ $e->stands_count }}</td>
<td>@if($e->invite_token)<span class="tag">convite pendente</span>@if($e->convite_enviado_em) <span class="tag ok" title="{{ $e->convite_enviado_em->format('d/m H:i') }}">e-mail enviado</span>@endif@else<span class="tag ok">ativo</span>@endif @unless($e->ativo)<span class="tag no">bloqueado</span>@endunless</td>
<td><a class="btn sec sm" href="{{ route('painel.expositores.edit',$e) }}">Editar</a></td></tr>
@endforeach</table></div>
{{ $expositores->links() }}
@endsection
