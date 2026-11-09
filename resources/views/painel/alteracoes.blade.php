@extends('layouts.painel')
@section('titulo','Histórico')
@section('conteudo')
<h1>Histórico de alterações</h1>
<div class="card"><table><tr><th>Quando</th><th>Quem</th><th>Estande</th><th>O que</th></tr>
@foreach($itens as $a)
<tr><td>{{ $a->created_at->format('d/m H:i') }}</td><td>{{ $a->usuario?->name ?? '—' }}</td><td>{{ $a->stand?->nome ?? '—' }}</td><td>{{ $a->resumo }}</td></tr>
@endforeach</table></div>
{{ $itens->links() }}
@endsection
