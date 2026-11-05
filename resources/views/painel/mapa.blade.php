@extends('layouts.painel')
@section('titulo','Mapa / GPS')
@section('conteudo')
<h1>Calibração do GPS</h1>
<div class="card">
  <p>Informe a coordenada real (lat/lng) de pontos do mapa. Com <b>2 pontos</b> (INÍCIO e FIM) o app já posiciona o usuário; com <b>3 ou mais</b> a precisão melhora. Pegue as coordenadas no Google Maps (botão direito → copiar).</p>
  <form method="post" action="{{ route('painel.mapa.update') }}">@csrf @method('PUT')
    @foreach($linhas as $i => $p)
    <div class="row" style="margin-bottom:10px">
      <div style="grid-column:span 2"><label>Nome do ponto</label><input type="text" name="pontos[{{ $i }}][nome]" value="{{ $p['nome'] ?? '' }}"></div>
      <div><label>x no mapa</label><input type="number" step="any" name="pontos[{{ $i }}][x]" value="{{ $p['x'] ?? '' }}"></div>
      <div><label>y no mapa</label><input type="number" step="any" name="pontos[{{ $i }}][y]" value="{{ $p['y'] ?? '' }}"></div>
      <div><label>Latitude</label><input type="text" name="pontos[{{ $i }}][lat]" value="{{ $p['lat'] ?? '' }}" placeholder="-4.5600"></div>
      <div><label>Longitude</label><input type="text" name="pontos[{{ $i }}][lng]" value="{{ $p['lng'] ?? '' }}" placeholder="-37.7700"></div>
    </div>
    @endforeach
    <button class="btn">Salvar calibração</button>
  </form>
</div>
<div class="card"><b>Pontos de referência do mapa</b> <span class="mut">(use x/y destes para os pontos acima)</span>
<table><tr><th>Nome</th><th>x</th><th>y</th></tr>
@foreach($referencias as $r)<tr><td>{{ $r['nome'] ?? $r['name'] ?? '' }}</td><td>{{ $r['x'] ?? '' }}</td><td>{{ $r['y'] ?? '' }}</td></tr>@endforeach</table></div>
@endsection
