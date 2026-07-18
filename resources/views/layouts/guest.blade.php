<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" style="margin: 0; padding: 0;">
        <div style="background: linear-gradient(135deg, #004d2e 0%, #002b1a 100%); min-height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 2rem; box-sizing: border-box;">
            
            <div style="margin-bottom: 1.5rem; text-align: center;">
                <span style="color: #ffffff; font-size: 2.25rem; font-weight: 900; letter-spacing: 0.05em; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                    COTEC<span style="color: #f17a28;">NOVA</span>
                </span>
                <p style="color: rgba(240, 253, 250, 0.9); font-size: 0.8rem; margin-top: 6px; margin-bottom: 0; font-weight: 500; letter-spacing: 0.05em;">
                    Plataforma Inteligente de Alertas Tempranas
                </p>
            </div>

            {{ $slot }}
            
        </div>
    </body>
</html>