@extends('layouts.app')

@section('content')
<style>
    :root {
        --pastel-blue: #d0ebff;
        --accent-blue: #228be6;
        --pastel-green: #d3f9d8;
        --accent-green: #38b000;
        --pastel-pink: #ffe0e9;
        --accent-pink: #d6336c;
        --pastel-gray: #f1f3f5;
    }
    body { background: var(--pastel-gray); }
    .main-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 20px rgba(100,100,100,.09);
        padding: 32px 24px 24px 24px;
        margin-bottom: 32px;
        animation: fadeIn .7s;
        max-width: 520px;
        margin-left: auto;
        margin-right: auto;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(30px);} to { opacity: 1; transform: none;} }
    h1 {
        color: var(--accent-blue);
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 2rem;
        letter-spacing: -1px;
    }
    .wizard-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 24px;
    }
    .wizard-step {
        width: 32%;
        text-align: center;
        color: var(--accent-blue);
        background: var(--pastel-blue);
        border-radius: 14px;
        padding: 7px 0;
        font-weight: 600;
        font-size: 1.02rem;
        opacity: 0.6;
        transition: background .22s, color .22s, opacity .22s, box-shadow .22s;
    }
    .wizard-step.active {
        background: var(--pastel-green);
        color: var(--accent-green);
        opacity: 1;
        box-shadow: 0 2px 12px rgba(56,176,0,0.13);
    }
    .wizard-step.done {
        background: var(--pastel-pink);
        color: var(--accent-pink);
        opacity: 1;
    }
    label {
        color: var(--accent-blue);
        font-weight: 500;
        margin-bottom: 6px;
    }
    .form-control, select {
        border-radius: 12px;
        border: 1.5px solid var(--pastel-blue);
        background: #fff;
        color: var(--accent-blue);
        font-weight: 500;
        font-size: 1.09rem;
        margin-bottom: 18px;
        box-shadow: 0 2px 8px rgba(150,180,230,0.04);
        transition: border-color .18s, box-shadow .18s, transform .18s;
    }
    .form-control:focus,
    select:focus {
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 2px #b3dafe30;
        background: #f8fbfe;
        color: var(--accent-blue);
        transform: scale(1.04);
        z-index: 1;
        position: relative;
    }
    .wizard-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 24px;
    }
    .btn-pastel-next {
        background: var(--pastel-green);
        color: var(--accent-green);
        border: none;
        font-weight: 600;
        border-radius: 12px;
        padding: 10px 28px;
        font-size: 1.12rem;
        box-shadow: 0 2px 8px rgba(56,176,0,0.06);
        transition: box-shadow .17s, filter .17s, transform .17s;
    }
    .btn-pastel-next:focus,
    .btn-pastel-next:hover {
        filter: brightness(1.06);
        box-shadow: 0 4px 16px rgba(56,176,0,0.16);
        transform: scale(1.06);
    }
    .btn-pastel-back {
        background: var(--pastel-pink);
        color: var(--accent-pink);
        border: none;
        font-weight: 600;
        border-radius: 12px;
        padding: 10px 24px;
        font-size: 1.12rem;
        transition: box-shadow .17s, filter .17s, transform .17s;
    }
    .btn-pastel-back:focus,
    .btn-pastel-back:hover {
        filter: brightness(1.06);
        box-shadow: 0 4px 14px rgba(214,51,108,0.15);
        transform: scale(1.06);
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let step = 1;
        const totalSteps = 3;
        const form = document.getElementById('wizard-form');
        function showStep(s) {
            document.querySelectorAll('.wizard-step').forEach((el, i) => {
                el.classList.remove('active', 'done');
                if (i + 1 < s) el.classList.add('done');
                if (i + 1 === s) el.classList.add('active');
            });
            document.querySelectorAll('.wizard-pane').forEach((pane, i) => {
                pane.style.display = (i + 1 === s) ? 'block' : 'none';
            });
        }
        showStep(step);
        document.getElementById('btn-next').onclick = function(e){
            e.preventDefault();
            if(step < totalSteps) {
                step++;
                showStep(step);
            } else {
                form.submit();
            }
        };
        document.getElementById('btn-back').onclick = function(e){
            e.preventDefault();
            if(step > 1) {
                step--;
                showStep(step);
            }
        };
    });
</script>

<div class="container mt-4">
    <div class="main-card">
        <h1>Editar Checklist</h1>
        <div class="wizard-steps mb-3">
            <div class="wizard-step">1. Seleccionar Item</div>
            <div class="wizard-step">2. Información de Checklist</div>
            <div class="wizard-step">3. Confirmar y Guardar</div>
        </div>
        <form id="wizard-form" action="{{ route('checklists.update', $checklist) }}" method="POST" autocomplete="off">
            @csrf
            @method('PUT')

            <!-- Paso 1 -->
            <div class="wizard-pane">
                <div class="mb-3">
                    <label for="item_id">Item</label>
                    <select name="item_id" id="item_id" class="form-control" required>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ $checklist->item_id == $item->id ? 'selected' : '' }}>
                                {{ $item->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Paso 2 -->
            <div class="wizard-pane" style="display:none;">
                <div class="mb-3">
                    <label for="etapa">Etapa</label>
                    <select name="etapa" id="etapa" class="form-control" required>
                        <option value="ingenieria" {{ $checklist->etapa == 'ingenieria' ? 'selected' : '' }}>Ingeniería</option>
                        <option value="embalaje" {{ $checklist->etapa == 'embalaje' ? 'selected' : '' }}>Embalaje</option>
                        <option value="entrega" {{ $checklist->etapa == 'entrega' ? 'selected' : '' }}>Entrega</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="user_id">Responsable (User ID)</label>
                    <input type="number" name="user_id" id="user_id" class="form-control" value="{{ $checklist->user_id }}" required>
                </div>
                <div class="mb-3">
                    <label for="tipo_entrega">Tipo de entrega</label>
                    <select name="tipo_entrega" id="tipo_entrega" class="form-control">
                        <option value="">--</option>
                        <option value="paqueteria" {{ $checklist->tipo_entrega == 'paqueteria' ? 'selected' : '' }}>Paquetería</option>
                        <option value="hospital" {{ $checklist->tipo_entrega == 'hospital' ? 'selected' : '' }}>Hospital</option>
                    </select>
                </div>
            </div>

            <!-- Paso 3 -->
            <div class="wizard-pane" style="display:none;">
                <div class="mb-3">
                    <label>Confirme la información antes de actualizar</label>
                    <ul class="list-group mb-3" id="wizard-summary" style="font-size:1.05rem;">
                        <li class="list-group-item">Verifique los campos anteriores antes de guardar.</li>
                    </ul>
                </div>
            </div>

            <div class="wizard-buttons">
                <button type="button" id="btn-back" class="btn btn-pastel-back">Atrás</button>
                <button type="button" id="btn-next" class="btn btn-pastel-next">Siguiente</button>
            </div>
        </form>
    </div>
</div>
@endsection
