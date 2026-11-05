@extends('layouts.painel')
@section('titulo','Stands')
@section('conteudo')
<h1>Stands <a class="btn sm" href="{{ route('painel.stands.create') }}">+ Novo</a></h1>
<form class="filters" method="get">
  <input type="text" name="q" value="{{ $q }}" placeholder="Buscar nome ou nº">
  <select name="categoria"><option value="">Todas as categorias</option>
    @foreach(config('festival.categorias') as $c)<option @selected($cat===$c)>{{ $c }}</option>@endforeach</select>
  <select name="filtro"><option value="">Todos</option>
    <option value="incompletos" @selected($filtro==='incompletos')>Perfil incompleto</option>
    <option value="sem-expositor" @selected($filtro==='sem-expositor')>Sem expositor</option>
    <option value="inativos" @selected($filtro==='inativos')>Inativos</option></select>
  <button class="btn sec sm">Filtrar</button>
</form>
<div class="card"><table>
<tr><th>Stand</th><th>Categoria</th><th>Local</th><th>Expositor</th><th>Perfil</th><th></th></tr>
@foreach($stands as $s)
<tr>
  <td><b>{{ $s->nome }}</b> @unless($s->ativo)<span class="tag no">inativo</span>@endunless @unless($s->noMapa())<span class="tag no">fora do mapa</span>@endunless</td>
  <td>{{ $s->categoria }}</td><td class="mut">{{ $s->localizacao() }}</td>
  <td class="mut">{{ $s->usuarios->pluck('name')->join(', ') ?: '—' }}</td>
  <td><span class="tag {{ $s->perfilCompleto() ? 'ok' : '' }}">{{ $s->perfilCompleto() ? 'completo' : 'incompleto' }}</span></td>
  <td><a class="btn sec sm" href="{{ route('painel.stands.edit',$s) }}">Editar</a></td>
</tr>
@endforeach</table></div>
{{ $stands->links() }}
@endsection
