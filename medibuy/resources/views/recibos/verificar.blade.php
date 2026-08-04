<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificar Recibo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

        :root {
            --color-primary: #c2dbf0;
            --color-primary-dark: #3a73c2;
            --color-success-bg: #d7f5d7;
            --color-success-text: #2e7d32;
            --color-error-bg: #ffe3e3;
            --color-error-text: #c62828;
            --color-text: #333;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 2rem;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 480px;
            background: #fff;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.6s ease;
        }

        h2 {
            text-align: center;
            color: #444;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--color-text);
        }

        input[type="text"] {
            width: 100%;
            padding: 0.65rem;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus {
            border-color: var(--color-primary-dark);
            box-shadow: 0 0 0 3px rgba(58, 115, 194, 0.15);
            outline: none;
        }

        button {
            margin-top: 1.2rem;
            padding: 0.6rem 1.2rem;
            background: var(--color-primary);
            color: var(--color-primary-dark);
            font-weight: 600;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }

        button:hover {
            background: #aac6e9;
            color: #2d5fa8;
        }

        .status {
            margin-top: 2rem;
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
            font-size: 1rem;
            border: 1px solid #ddd;
            animation: slideFade 0.5s ease;
        }

        .success {
            background-color: var(--color-success-bg);
            color: var(--color-success-text);
        }

        .error {
            background-color: var(--color-error-bg);
            color: var(--color-error-text);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes slideFade {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 500px) {
            body {
                padding: 1rem;
            }

            .container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verificación de Recibo</h2>

        <form action="{{ route('recibo.verificar.post') }}" method="POST">
            @csrf
            <label for="codigo">Código de verificación</label>
            <input type="text" name="codigo" id="codigo" required placeholder="Ej: 1A2B3C4D5E6F">
            <button type="submit">Verificar</button>
        </form>

        {{-- Mensaje de resultado --}}
  @if(!is_null($codigo))
    @if($pagoValido)
        <div class="status success">
            <p>✅ Recibo válido.</p>
            <p><strong>Cliente:</strong> {{ $pagoValido->venta->cliente->nombre }} {{ $pagoValido->venta->cliente->apellido }}</p>
            <p><strong>Monto:</strong> ${{ number_format($pagoValido->monto, 2) }}</p>
            <p><strong>Fecha de pago:</strong> {{ \Carbon\Carbon::parse($pagoValido->fecha_pago)->format('d/m/Y') }}</p>
        </div>
    @else
        <div class="status error">
            <p>❌ El código ingresado no corresponde a un recibo válido.</p>
        </div>
    @endif

    <p style="font-weight: 600; font-size: 0.85rem; color: #555; margin-top: 1rem; text-align: center;">
        Si tienes duda, queja o aclaración, manda mensaje al 
        <a href="tel:+7224485191" style="color: #1565c0; text-decoration: none; font-weight: 700;">+52 722 448 5191</a> 
        o al correo 
        <a href="mailto:compras@grupomedibuy.com" style="color: #1565c0; text-decoration: none; font-weight: 700;">compras@grupomedibuy.com</a>.
    </p>
@endif


    </div>
</body>
</html>
