<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <title inertia>{{ config('app.name', 'MusicTools') }}</title>

    <!-- Meta para PWA -->
    <meta name="theme-color" content="#ffffff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <!-- Descrição do App -->
    <meta name="description" content="Aplicativo para músicos e bandas. Toque cifras, veja letras, aprenda com vídeos e organize seu repertório.">
    <meta name="keywords" content="música, bandas, cifras, letras, vídeos, músicos, app de música, estudo musical, ferramentas musicais">
    <meta name="application-name" content="MusicTools">
    <meta name="category" content="music tools">

    <!-- Open Graph para redes sociais -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="MusicTools - App para Bandas e Músicos">
    <meta property="og:description" content="Toque músicas com cifras e letras, veja vídeos para estudar e aprimore sua performance em banda.">
    <meta property="og:image" content="/pwa-512x512.png">

    <!-- Ícones e manifest -->
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="icon" href="/favicon.ico">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">

    <!-- Estilos globais -->
    @include('AssetsGlobal/globalCss')

    <!-- Scripts globais + analytics -->
    @include('configApp/analytics')

    <!-- Scripts principais do app -->
    @routes
    @vite(['resources/js/config/app.js', "resources/PagesVuejs/{$page['component']}.vue"])
    @inertiaHead
</head>

<body>
    @inertia
    @include('AssetsGlobal/globalJs')
</body>

</html>
