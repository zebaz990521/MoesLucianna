<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta charset="utf-8">
        <title>Iniciar Sesión | MoesLucianna - Sistema de Compra y Venta</title>
        <meta content="Sistema de gestión de compras y ventas" name="description" />
        <meta content="MoesLucianna" name="author" />

        <!-- Ícono del sitio -->
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

        <!-- Configuración del Layout -->
        <script src="{{ asset('assets/js/layout.js') }}"></script>

        <!-- Estilos -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

        {{ $css ?? '' }}

        <!-- Scripts -->
        {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    </head>
    <body>

        <div class="auth-page-wrapper pt-5">

            <!-- Header slot -->
        {{ $header ?? '' }}

        {{ $slot }}

         <!-- Footer slot -->
         {{ $footer ?? '' }}

        </div>



        {{ $js ?? '' }}


        <!-- JAVASCRIPT -->
        <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
        <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
        <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
        <script src="{{ asset('assets/js/plugins.js') }}"></script>

        <!-- Particles.js (Efecto de partículas en el fondo) -->
        <script src="{{ asset('assets/libs/particles.js/particles.js') }}"></script>
        <script src="{{ asset('assets/js/pages/particles.app.js') }}"></script>

        <!-- Inicialización del visor de contraseña -->
        <script src="{{ asset('assets/js/pages/password-addon.init.js') }}"></script>
    </body>
</html>
