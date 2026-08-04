@extends('layouts.app')

@section('content')
<div class="reset-password-container">
    <h2>Restablecer contraseña</h2>
    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <label for="email">Correo electrónico:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Nueva contraseña:</label>
        <input type="password" name="password" id="password" required>

        <label for="password_confirmation">Confirmar contraseña:</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>

        <button type="submit">Cambiar contraseña</button>
    </form>
</div>
@endsection
