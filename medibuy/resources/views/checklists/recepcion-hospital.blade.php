@extends('layouts.app')
@section('content')
<div class="wizard-step" style="max-width:640px;margin:40px auto;background:#fff;padding:32px 26px 38px 26px;border-radius:18px;box-shadow:0 8px 40px rgba(34,139,230,0.11);">

    <h2 class="mb-4 text-center" style="color:#228be6; font-weight:600;">
        Confirmación de recepción hospitalaria
    </h2>
    <div class="mb-3 text-center">
        <span style="color:#787fa0;">Escanea y confirma que el equipo llegó correctamente. Completa el checklist, firma y sube evidencia.</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success text-center" style="border-radius:1.2rem;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mb-4" style="border-radius:1.2rem;">
            <ul class="mb-0" style="list-style:none;">
                @foreach ($errors->all() as $error)
                    <li style="color:#cb2d6f;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="recepcion-hospital-form" method="POST" action="{{ route('recepcion-hospital.guardar', $checklist->id) }}" enctype="multipart/form-data">
        @csrf

        <label style="font-weight:500;">Nombre y puesto del responsable de recepción <span style="color:#cb2d6f">*</span></label>
        <input type="text" name="nombre_responsable" class="form-control mb-3" required placeholder="Ej: Juan Pérez, Enfermero(a)">

        {{-- Checklist dinámico, pastel --}}
        <div class="table-responsive mb-4">
            <table class="table animate-fadein" style="background: #f8fafc; border-radius: 12px;">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Componente</th>
                        <th>¿Recibido?</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($componentes as $productoId => $compArr)
                        @foreach($compArr as $componente => $det)
                            <tr>
                                <td>
                                    @php $prod = $productos->firstWhere('id', $productoId); @endphp
                                    {{ $prod ? ($prod->marca . ' ' . $prod->modelo) : $productoId }}
                                </td>
                                <td>{{ $componente }}</td>
                                <td>
                                    <select name="checklist[{{$productoId}}][{{$componente}}]" class="form-control" required style="background:#f4fbfd;">
                                        <option value="">Selecciona</option>
                                        <option value="ok">Recibido</option>
                                        <option value="falta">No llegó</option>
                                        <option value="dañado">Dañado</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <label>Observaciones adicionales (opcional)</label>
        <textarea name="observaciones" class="form-control mb-3" placeholder="¿Algún comentario o incidencia?"></textarea>

        {{-- Firma digital --}}
        <label style="font-weight:500;">Firma digital de recibido <span style="color:#cb2d6f">*</span></label>
        <div style="border:1.5px solid #cfd8dc;border-radius:11px;position:relative; margin-bottom:10px;">
            <canvas id="firmaCanvas" width="340" height="100" style="width:100%;height:100px;touch-action:none;"></canvas>
            <button type="button" id="limpiarFirma" class="btn btn-sm btn-light" style="position:absolute;top:2px;right:4px;">Limpiar</button>
        </div>
        <input type="hidden" name="firma_recepcion" id="firmaInput" required>

        <label>Evidencia fotográfica (opcional)</label>
        <input type="file" name="evidencias[]" class="form-control mb-4" multiple accept="image/*">

        <div class="d-flex justify-content-end mt-2">
            <button type="submit" class="btn btn-primary btn-lg animate-pop" style="border-radius:1.3rem;">Confirmar recepción</button>
        </div>
    </form>
</div>

<script>
function firmaCanvasInit(idCanvas, idInput, idBtn) {
    const canvas = document.getElementById(idCanvas);
    const ctx = canvas.getContext('2d');
    let drawing = false, lastX = 0, lastY = 0;

    function draw(x, y) {
        if(!drawing) return;
        ctx.lineWidth = 2.2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = "#228be6";
        ctx.lineTo(x, y);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(x, y);
    }

    canvas.addEventListener('mousedown', e => { drawing = true; [lastX,lastY]=[e.offsetX,e.offsetY]; ctx.beginPath(); ctx.moveTo(lastX,lastY); });
    canvas.addEventListener('mouseup', () => { drawing=false; ctx.beginPath(); });
    canvas.addEventListener('mouseout', () => { drawing=false; ctx.beginPath(); });
    canvas.addEventListener('mousemove', e => { draw(e.offsetX,e.offsetY); });

    canvas.addEventListener('touchstart', function(e){
        e.preventDefault();
        drawing = true;
        const rect = canvas.getBoundingClientRect();
        lastX = e.touches[0].clientX - rect.left;
        lastY = e.touches[0].clientY - rect.top;
        ctx.beginPath(); ctx.moveTo(lastX,lastY);
    },{passive:false});
    canvas.addEventListener('touchmove', function(e){
        e.preventDefault();
        if(!drawing) return;
        const rect = canvas.getBoundingClientRect();
        let x = e.touches[0].clientX - rect.left;
        let y = e.touches[0].clientY - rect.top;
        draw(x,y);
    },{passive:false});
    canvas.addEventListener('touchend', function(e){ drawing = false; ctx.beginPath(); },{passive:false});

    document.getElementById(idBtn).onclick = function(e){
        e.preventDefault();
        ctx.clearRect(0,0,canvas.width,canvas.height);
    };

    document.getElementById('recepcion-hospital-form').addEventListener('submit', function(){
        document.getElementById(idInput).value = canvas.toDataURL('image/png');
    });
}
firmaCanvasInit('firmaCanvas', 'firmaInput', 'limpiarFirma');
</script>
@endsection
