@extends('layouts.app')

@section('title', 'Вход в систему')

@section('content')
    <div class="card" style="max-width:420px;margin:40px auto;">
        <h1 style="margin-top:0;">Вход</h1>
        <p style="color:#6b7280;margin-bottom:24px;">Пожалуйста, авторизуйтесь, чтобы работать с системой учёта.</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div style="margin-bottom:16px;">
                <label for="email">E-mail</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid #d1d5db;"
                >
                @error('email')
                    <div style="color:#b91c1c;font-size:0.85rem;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom:16px;">
                <label for="password">Пароль</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid #d1d5db;"
                >
                @error('password')
                    <div style="color:#b91c1c;font-size:0.85rem;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <label style="display:flex;align-items:center;gap:8px;margin-bottom:20px;font-size:0.9rem;color:#4b5563;">
                <input type="checkbox" name="remember">
                Запомнить меня
            </label>

            <button class="btn btn-primary" type="submit" style="width:100%;justify-content:center;">Войти</button>
        </form>

        <div style="margin-top:16px;font-size:0.85rem;color:#6b7280;">
            <p style="margin:0;">Демо-доступ:</p>
            <p style="margin:4px 0 0;">
                Начальник смены — <code>boss@example.com</code> / <code>password</code>
            </p>
        </div>
    </div>
@endsection
