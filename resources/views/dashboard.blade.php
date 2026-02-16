<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h1>Dashboard Barber 💈</h1>

    <p>Bienvenido {{ auth()->user()->name }}</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button>Cerrar sesión</button>
    </form>
</body>
</html>
