<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <script src="{{ asset('js/app.js') }}"></script>
</head>
<body>
    <h1>Login</h1>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Senha" required>
        <button type="submit">Entrar</button>
    </form>
    <a href="{{ route('register') }}">Criar conta</a>
</body>
</html>