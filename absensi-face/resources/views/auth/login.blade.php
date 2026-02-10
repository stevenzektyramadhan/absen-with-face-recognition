<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Sistem Absensi</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<!-- BACKGROUND CIRCLES (DI LUAR KOTAK LOGIN) -->
<div class="bg-circle bg-circle-1"></div>
<div class="bg-circle bg-circle-2"></div>
<div class="bg-circle bg-circle-3"></div>
<div class="bg-circle bg-circle-4"></div>

<div class="container">
    <div class="login-box">

        <!-- LEFT SIDE -->
        <div class="login-left">
            <h1>LOGIN</h1>
            <p>
                Selamat datang di Sistem Absensi.<br>
                Semoga aktivitas kerja hari ini berjalan lancar.<br>
                Tetap semangat dan jaga kesehatan.
            </p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- EMAIL -->
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Masukkan email"
                    required
                >

                <!-- PASSWORD -->
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >

                <!-- BUTTON -->
                <button type="submit">Login</button>
            </form>

            <!-- ERROR MESSAGE -->
            @error('email')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <!-- RIGHT SIDE -->
        <div class="login-right">
            <img src="{{ asset('images/vektorlogin.png') }}" alt="Login Illustration">
        </div>

    </div>
</div>

</body>
</html>
