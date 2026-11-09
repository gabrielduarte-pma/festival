<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('titulo', 'Painel') · Festival Aracati</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700;800&display=swap" rel="stylesheet">
<meta name="theme-color" content="#012468">
<link rel="stylesheet" href="/estilos/painel.css?v={{ filemtime(public_path('estilos/painel.css')) }}">
</head>
<body>
<header class="top">
  <span class="logo">FESTIVAL ARACATI<small>Painel</small></span>
  @if(auth()->user()?->isAdmin())
    <a href="{{ route('painel.dashboard') }}" @class(['on'=>request()->routeIs('painel.dashboard')])>Resumo</a>
    <a href="{{ route('painel.stands.index') }}" @class(['on'=>request()->routeIs('painel.stands.*')])>Estandes</a>
    <a href="{{ route('painel.expositores.index') }}" @class(['on'=>request()->routeIs('painel.expositores.*')])>Expositores</a>
    <a href="{{ route('painel.programacao.index') }}" @class(['on'=>request()->routeIs('painel.programacao.*')])>Programação</a>
    <a href="{{ route('painel.mapa.edit') }}" @class(['on'=>request()->routeIs('painel.mapa.*')])>Mapa/GPS</a>
    <a href="{{ route('painel.alteracoes') }}" @class(['on'=>request()->routeIs('painel.alteracoes')])>Histórico</a>
  @else
    <a href="{{ route('meu-stand.index') }}" class="on">Meu estande</a>
  @endif
  <span class="sp"></span>
  <a href="/" target="_blank">Ver o app ↗</a>
  <form method="post" action="{{ url('/logout') }}" class="inline">@csrf<button class="btn sec sm">Sair</button></form>
</header>
<main>
  @include('partials.flash')
  @yield('conteudo')
</main>
</body>
</html>
