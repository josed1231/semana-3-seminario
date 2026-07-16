<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="text-center p-8 bg-white shadow-xl rounded-lg">
        <h1 class="text-6xl font-bold text-red-600">403</h1>
        <p class="text-xl text-gray-700 mt-4">No tienes permisos para visualizar esta sección.</p>
        
        <div class="mt-8 flex justify-center gap-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</body>
</html>