@extends('layouts.app')

@section('title', 'Checklists')
@section('titulo', 'Checklists')

@section('content')
<style>
    :root {
        --primary: #228be6;
        --success: #38b000;
        --warning: #f59f00;
        --danger: #d6336c;
        --light: #f8f9fa;
        --gray: #f1f3f5;
        --blue-light: #d0ebff;
        --green-light: #d3f9d8;
        --red-light: #ffe0e9;
    }
    body { background: var(--gray); }
    .wizard-container {
        max-width: 700px;
        margin: 40px auto;
        background: white;
        border-radius: 18px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        padding: 32px;
        animation: fadeIn .7s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    h2 {
        color: var(--primary);
        font-weight: bold;
        margin-bottom: 20px;
    }
    .progress-bar {
        height: 10px;
        border-radius: 10px;
        background: var(--gray);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, var(--primary) 80%, var(--blue-light) 100%);
        transition: width 0.4s cubic-bezier(.6, .2, .25, 1.2);
    }
    .form-section { display: none; }
    .form-section.active { display: block; animation: fadeIn .5s; }
    .form-control {
        border-radius: 12px;
        border: 1.5px solid var(--blue-light);
        font-size: 1rem;
        margin-bottom: 16px;
    }
    label {
        font-weight: 500;
        margin-bottom: 6px;
        display: block;
        color: var(--primary);
    }
    .btn {
        border-radius: 12px;
        padding: 10px 24px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.2s cubic-bezier(.6, .2, .25, 1.2);
    }
    .btn-next {
        background: var(--green-light);
        color: var(--success);
        border: none;
    }
    .btn-back {
        background: var(--red-light);
        color: var(--danger);
        border: none;
    }
    .btn:hover {
        transform: scale(1.05);
        filter: brightness(1.05);
    }
    canvas {
        border: 1px solid #ccc;
        border-radius: 8px;
        margin-bottom: 16px;
        width: 100%;
        height: 150px;
        touch-action: none;
    }
    .productos-lista {
        background: var(--gray);
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 24px;
        box-shadow: 0 2px 7px rgba(34,139,230,.03);
    }
    .productos-lista ul { margin:0; padding-left:16px; }
    .productos-lista li { margin-bottom:4px; color: #343a40; }
</style>

<div class="wizard-container">
    <h2>Checklist de Entrega de Venta #{{ $venta->id }}</h2>

    <div class="productos-lista">
        <strong>Productos:</strong>
        <ul>
            @foreach($productos as $prod)
                <li>
                    {{ $prod->tipo_equipo }} / {{ $prod->marca }} / {{ $prod->modelo }} (Cantidad: {{ $prod->cantidad }})
                </li>
            @endforeach
        </ul>
    </div>

    <div class="progress-bar">
        <div id="progress-fill" class="progress-bar-fill"></div>
    </div>

    <form id="wizard-form" action="{{ route('checklists.guardar', $venta->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Paso 1: Ingeniería -->
        <div class="form-section active">
            <label>Incidentes detectados en Ingeniería</label>
            <textarea name="ingenieria_incidente" class="form-control" placeholder="Describe incidentes, si hay."></textarea>

            <label>Firma Responsable (canvas)</label>
            <canvas id="firmaCanvas"></canvas>
            <input type="hidden" name="firma_responsable" id="firmaInput">

            <label>Evidencias (puede ser foto o archivo)</label>
            <input type="file" name="evidencias[]" class="form-control" multiple>
        </div>

        <!-- Paso 2: Embalaje -->
        <div class="form-section">
            <label>Observaciones de Embalaje</label>
            <input type="text" name="embalaje_observacion" class="form-control" placeholder="Ej: caja dañada, buen estado, etc">
        </div>

        <!-- Paso 3: Entrega/Tipo de entrega -->
        <div class="form-section">
            <label>Tipo de entrega</label>
            <select name="tipo_entrega" class="form-control">
                <option value="paqueteria">Paquetería</option>
                <option value="hospital">Hospital</option>
            </select>
            <label>Comentario final</label>
            <textarea name="comentario_final" class="form-control" placeholder="Comentario final..."></textarea>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-back" id="btn-back">Atrás</button>
            <button type="button" class="btn btn-next" id="btn-next">Siguiente</button>
            <button type="submit" class="btn btn-next" id="btn-submit" style="display:none">Finalizar</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let currentStep = 0;
        const steps = document.querySelectorAll('.form-section');
        const progress = document.getElementById('progress-fill');

        function updateStep() {
            steps.forEach((s, i) => s.classList.toggle('active', i === currentStep));
            progress.style.width = `${((currentStep + 1) / steps.length) * 100}%`;
            document.getElementById('btn-back').style.display = currentStep === 0 ? 'none' : 'inline-block';
            document.getElementById('btn-next').style.display = currentStep === steps.length - 1 ? 'none' : 'inline-block';
            document.getElementById('btn-submit').style.display = currentStep === steps.length - 1 ? 'inline-block' : 'none';
        }

        document.getElementById('btn-next').addEventListener('click', () => {
            if (currentStep < steps.length - 1) currentStep++;
            updateStep();
        });

        document.getElementById('btn-back').addEventListener('click', () => {
            if (currentStep > 0) currentStep--;
            updateStep();
        });

        updateStep();

        // Firma Canvas (simple & touch compatible)
        const canvas = document.getElementById('firmaCanvas');
        const ctx = canvas.getContext('2d');
        let drawing = false, lastX = 0, lastY = 0;

        function getPos(e) {
            if (e.touches) {
                return [e.touches[0].clientX - canvas.getBoundingClientRect().left,
                        e.touches[0].clientY - canvas.getBoundingClientRect().top];
            } else {
                return [e.offsetX, e.offsetY];
            }
        }

        function draw(e) {
            if (!drawing) return;
            e.preventDefault();
            let [x, y] = getPos(e);
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#222';
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(x, y);
            ctx.stroke();
            [lastX, lastY] = [x, y];
        }

        canvas.addEventListener('mousedown', function(e){
            drawing = true;
            [lastX, lastY] = getPos(e);
        });
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', ()=> drawing = false);
        canvas.addEventListener('mouseleave', ()=> drawing = false);

        // Touch support
        canvas.addEventListener('touchstart', function(e){
            drawing = true;
            [lastX, lastY] = getPos(e);
        });
        canvas.addEventListener('touchmove', draw);
        canvas.addEventListener('touchend', ()=> drawing = false);

        document.getElementById('wizard-form').addEventListener('submit', function(e) {
            document.getElementById('firmaInput').value = canvas.toDataURL('image/png');
        });
    });
</script>
@endsection
