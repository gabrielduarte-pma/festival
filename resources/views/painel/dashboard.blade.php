@extends('layouts.painel')
@section('titulo','Resumo')
@section('conteudo')
<h1>Resumo do festival</h1>
<div class="grid">
  <div class="card"><div class="num">{{ $total }}</div>estandes <div class="mut">{{ $ativos }} ativos</div></div>
  <div class="card"><div class="num">{{ $completos }}</div>perfis completos <div class="mut">sobre + foto + contato</div></div>
  <div class="card"><div class="num">{{ $comFoto }}</div>com foto</div>
  <div class="card"><div class="num">{{ $expositores }}</div>expositores <div class="mut">{{ $pendentes }} ainda não criaram senha</div></div>
</div>
<h2>E-mail</h2>
<div class="card" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
  <div style="flex:1;min-width:220px"><b>Teste de envio</b><div class="mut">Manda um e-mail de teste para {{ auth()->user()->email }} e confirma se o Brevo está funcionando.</div></div>
  <form method="post" action="{{ route('painel.email-teste') }}">@csrf<button class="btn sec">Enviar e-mail de teste</button></form>
</div>
<h2>Últimas alterações</h2>
<div class="card">
@forelse($recentes as $a)
  <div style="padding:6px 0;border-bottom:1px solid var(--line)"><b>{{ $a->usuario?->name ?? '—' }}</b> · {{ $a->stand?->nome }} <span class="mut">— {{ $a->resumo }} · {{ $a->created_at->diffForHumans() }}</span></div>
@empty<p class="mut">Nenhuma alteração ainda.</p>@endforelse
</div>
@endsection
