
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¡Bienvenido!</title>
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-image: url('{{ asset('images/3.jpeg') }}');
            background-size: cover;
            background-position: center;
        }
        .overlay {
            background: rgba(11, 59, 233, 0.7);
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="overlay flex flex-col min-h-screen">
        <!-- Barra de navegación -->
        <nav class="flex justify-between items-center px-8 py-4 bg-white bg-opacity-80 shadow">
            <div>
                <img src="{{ asset('images/logo_uno_se.png') }}" alt="logo" class="h-16 w-16 object-contain"/>
            </div>
            <div class="flex gap-6">
                <a href="#" class="text-blue-900 hover:underline">Quiénes somos</a>
                <a href="#" class="text-blue-900 hover:underline">Contactos</a>
            </div>
            <form class="flex">
                <input type="text" placeholder="Buscar..." class="px-2 py-1 border rounded-l">
                <button type="submit" class="px-3 py-1 bg-blue-900 text-white rounded-r">Buscar</button>
            </form>
        </nav>
        <!-- Contenido principal -->
        <div class="flex flex-col items-center justify-center flex-1 text-center text-white">
            <h1 class="text-4xl font-bold mb-4">¡Bienvenido!</h1>
            <p class="mb-8 text-lg">Explora, conoce más sobre nosotros o ingresa a tu cuenta.</p>
            <div class="flex gap-4">
                <a href="{{ route('login') }}" class="px-6 py-2 bg-white text-blue-900 font-semibold rounded shadow hover:bg-blue-100">Ingresar</a>
                <a href="{{ route('register') }}" class="px-6 py-2 bg-blue-900 text-white font-semibold rounded shadow hover:bg-blue-700">Registrarse</a>
            </div>
        </div>
    </div>
</body>
</html>