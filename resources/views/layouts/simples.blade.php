<!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('titulo') · Festival Aracati</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="/estilos/painel.css?v={{ filemtime(public_path('estilos/painel.css')) }}"></head>
<body><main class="auth"><h1>Festival Aracati 2026</h1>@include('partials.flash')@yield('conteudo')</main></body></html>
