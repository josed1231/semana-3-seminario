<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- INTEGRACIÓN DE ALPINE.JS (Cargado de forma segura y diferida) -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { background-color: #f8fafc !important; color: #1e293b !important; }
            .bg-white { background-color: #ffffff !important; }
            h2, h3, th { color: #004d2e !important; font-weight: 700 !important; }
            .btn-primary, button[type="submit"]:not(.bg-red-600) {
                background-color: #f17a28 !important; color: #ffffff !important;
                border-radius: 12px !important; font-weight: 700 !important;
                border: none !important; transition: background-color 0.2s !important;
            }
            .btn-primary:hover, button[type="submit"]:not(.bg-red-600):hover { background-color: #d66213 !important; }
            input[type="text"], input[type="email"], input[type="password"], select, textarea {
                border: 1px solid #cbd5e1 !important; border-radius: 10px !important;
                color: #1e293b !important; background-color: #ffffff !important; padding: 10px 14px !important;
            }
            input:focus, select:focus, textarea:focus {
                border-color: #005a36 !important; box-shadow: 0 0 0 3px #dcece4 !important; outline: none !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen" style="background-color: #f4f6f8;">
            
            <div style="background-color: #004d2e;">
                @include('layouts.navigation')
            </div>

            <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>