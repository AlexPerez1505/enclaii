@extends('layouts.app')
@section('title', 'Compras')
@section('titulo', 'Compras')
@section('content')
<link rel="stylesheet" href="{{ asset('css/compras.css') }}?v={{ time() }}">
<style>
    .container{
        margin-top:95px!important;
    }
</style>
<div class="container mt-5" >
    <h2 class="mb-4">📦 Crear Nueva Compra</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('pedidos.store') }}" method="POST" id="form-pedido">
        @csrf

        <div class="row">
            <!-- IZQUIERDA -->
            <div class="col-md-6">
                <div class="card-section">
                    <div class="mb-3">
                        <label for="fecha_programada" class="form-label">📅 Fecha Programada de Llegada</label>
                        <input type="date" name="fecha_programada" id="fecha_programada" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="creado_por" class="form-label">👤 Creado por (Jefe)</label>
                        <input type="text" name="creado_por" id="creado_por" class="form-control"
                               value="{{ Auth::user()->name }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="observaciones" class="form-label">📝 Observaciones</label>
                        <textarea name="observaciones" id="observaciones" class="form-control" rows="4"></textarea>
                    </div>
                </div>
            </div>

            <!-- DERECHA -->
            <div class="col-md-6">
                <div class="card-section">
                    <!-- 🔍 Selector de Paquete -->
                    <div class="mb-3">
                        <label for="paquete_select" class="form-label">📦 Seleccionar Paquete</label>
                        <select id="paquete_select" class="form-control">
                            <option value="">-- Selecciona un paquete --</option>
                            <option value="Cámara 1188HD">Paquete: Cámara 1188HD</option>
                            <option value="Cámara 1288HD">Paquete: Cámara 1288HD</option>
                            <option value="Cámara 1488HD">Paquete: Cámara 1488HD</option>
                            <option value="Cámara Precisión AC">Paquete: Cámara Precisión AC</option>
                            <option value="Cámara 1588AIM">Paquete: Cámara 1588AIM</option>
                            <option value="Cámara 1688 4K">Paquete: Cámara 1688 4K</option>
                            <option value="Fuente de luz L9000">Paquete: Fuente de luz L9000</option>
                            <option value="Fuente de luz L10">Paquete: Fuente de luz L10</option>
                            <option value="Fuente de luz L11">Paquete: Fuente de luz L11</option>
                            <option value="Insuflador 40 lts">Paquete: Insuflador 40 lts</option>
                            <option value="Insuflador 45 lts Pneumosure">Paquete: Insuflador 45 lts Pneumosure</option>
                            <option value="Grabador SDC3">Paquete: Grabador SDC3</option>
                            <option value="Monitor Grado Médico Wase">Paquete: Monitor Grado Médico Wase</option>
                            <option value="Monitor Grado Médico Vision Pro Led">Paquete: Monitor Grado Médico Vision Pro Led</option>
                            <option value="Lente 10mm">Paquete: Lente 10mm</option>
                            <option value="Lente 5mm">Paquete: Lente 5mm</option>
                            <option value="Lente 4mm">Paquete: Lente 4mm</option>
                            <option value="Clarity">Paquete: Clarity</option>
                            <option value="Transmisores">Paquete: Transmisores</option>
                            <option value="Crossfire2">Paquete: Crossfire2</option>
                            <option value="Core">Paquete: Core</option>
                            <option value="Systema 4">Paquete: Systema 4</option>
                            <option value="Systema 7">Paquete: Systema 7</option>
                            <option value="Systema 7 Charola">Paquete: Systema 7 Charola</option>
                            <option value="Systema 8 Charola">Paquete: Systema 8 Charola</option>
                            <option value="Ligasura S8">Paquete: Ligasura S8</option>
                            <option value="Force Triad">Paquete: Force Triad</option>
                            <option value="Gen11">Paquete: Gen11</option>
                            <option value="Electrocauterios">Paquete: Electrocauterios (FX, Force 2, Force EZ, ICC200)</option>
                            
                        </select>
                    </div>

                    <h4>🖥 Equipos que llegarán</h4>
                    <div id="equipos-container"></div>
                    <button type="button" class="btn btn-pastel mt-2" id="btn-add-equipo">+ Agregar Equipo</button>
                </div>
            </div>
        </div>

        <!-- COMPONENTES -->
        <div class="card-section">
            <h4>🔧 Componentes esperados (manual)</h4>
            <div id="componentes-container"></div>
            <button type="button" class="btn btn-pastel mt-2 mb-3" id="btn-add-componente">+ Agregar Componente</button>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">✅ Crear Pedido</button>
        </div>
    </form>
</div>

<script>
    // Mapa de paquetes → componentes
    const paqueteComponentes = {
      "Cámara 1188HD": ["Cable de alimentación","Cable de video"],
      "Cámara 1288HD": ["Cable de alimentación","Cable de video"],
      "Cámara 1488HD": ["Cable de alimentación","Cable de video"],
      "Cámara Precisión AC": ["Cable de alimentación","Cable de video"],
      "Cámara 1588AIM": ["Cable de alimentación","Cable de video"],
      "Cámara 1688 4K": ["Cable de alimentación","Cable de video"],
      "Fuente de luz L9000": ["Cable de alimentación","Fibra de luz blanca"],
      "Fuente de luz L10": ["Cable de alimentación","Fibra de luz verde","Interfaz (USB-USB)"],
      "Fuente de luz L11": ["Cable de alimentación","Fibra de luz verde","Interfaz (USB-CCU azul)"],
      "Insuflador 40 lts": ["Manguera","Yugo","Adaptador trasero de CO2"],
      "Insuflador 45 lts Pneumosure": ["Manguera","Yugo","Adaptador trasero de CO2"],
      "Grabador SDC3": ["Cable de alimentación","Cable de video","Remotos"],
      "Monitor Grado Médico Wase": ["Cable de alimentación","Eliminador"],
      "Monitor Grado Médico Vision Pro Led": ["Cable de alimentación","Eliminador"],
      "Lente 10mm": ["Barril adaptador para diferentes tipos de fibras"],
      "Lente 5mm": ["Barril adaptador para diferentes tipos de fibras"],
      "Lente 4mm": ["Barril adaptador para diferentes tipos de fibras"],
      "Clarity": ["Cable de alimentación","Cable de video"],
      "Transmisores": ["Cable de alimentación","Cable de video"],
      "Crossfire2": ["Cable de alimentación","Fórmula 180"],
      "Core": ["Cable de alimentación fórmula Core","Charola de sierras y taladros (opcional)"],
      "Systema 4": ["Destornillador","Sierra sagital 5","pinza pasador","pinza de alambre","taladro pequeño","taladro 1/4","taladro 5/32","llave corta y larga"],
      "Systema 7": ["Sierra recíproca","Sierra sagital","Pieza de mano rotatoria de doble gatillo","Llave larga","Porta broca de bloque sin llave","Taladro pequeño","Hudson modificado","Pasador de gatillo doble","Pasador grande ajustable con gatillo doble"],
      "Systema 7 Charola": ["Sierra recíproca","Sierra sagital","Taladro rotatorio de doble gatillo","Mandril con llave 1/4 y 5/32","Taladro pequeño","Hudson modificado","Hudson","Escorado largo","Pinza de alambre","Pinza de pasador","Trinkle","2 llaves"],
      "Systema 8 Charola": ["Sierra recíproca","Sierra sagital","Taladro rotatorio de doble gatillo","Mandril con llave 1/4 y 5/32","Taladro pequeño","Hudson modificada","Hudson","Escorado largo","Pinza de alambre","Pinza de pasador","Trinkle","2 llaves"],
      "Ligasura S8": ["Cable de alimentación","Adaptador para pinzas"],
      "Force Triad": ["Cable de alimentación","Pedal monopolar","Lápiz","Placa"],
      "Gen11": ["Cable de alimentación","Adaptador Harmónico","Pieza de mano gris"],
      "Electrocauterios": [] 
    };

    const paqueteSelect      = document.getElementById('paquete_select');
    const equiposContainer   = document.getElementById('equipos-container');
    const componentesContainer = document.getElementById('componentes-container');
    const btnAddEquipo       = document.getElementById('btn-add-equipo');
    const btnAddComponente   = document.getElementById('btn-add-componente');

    // Handlers originales
    btnAddEquipo.addEventListener('click', agregarEquipo);
    btnAddComponente.addEventListener('click', agregarComponente);

    // Cuando cambia el paquete
    paqueteSelect.addEventListener('change', () => {
        const pkg = paqueteSelect.value;
        if (!pkg) return;

        // limpiar
        equiposContainer.innerHTML    = '';
        componentesContainer.innerHTML = '';

        // inyectar equipo del paquete
        agregarEquipoConDatos(0, pkg, 1);

        // inyectar componentes
        (paqueteComponentes[pkg]||[]).forEach((c, i) => {
            agregarComponenteConDatos(i, c, 0, 1);
        });

        actualizarSelectsEquipos();
    });

    // Funciones helper
    function agregarEquipo() {
      const idx = equiposContainer.children.length;
      agregarEquipoConDatos(idx,'',1);
    }
    function agregarComponente() {
      const idx = componentesContainer.children.length;
      agregarComponenteConDatos(idx,'','',1);
    }
    function agregarEquipoConDatos(idx,nombre,cantidad){
      const div = document.createElement('div');
      div.classList.add('equipo-card'); div.dataset.id=idx;
      div.innerHTML = `
        <label>Nombre del Equipo:</label>
        <input type="text" name="equipos[${idx}][nombre]" class="form-control equipo-nombre" required value="${nombre}">
        <label class="mt-2">Cantidad:</label>
        <input type="number" name="equipos[${idx}][cantidad]" class="form-control" min="1" value="${cantidad}" required>
        <button type="button" class="btn btn-danger btn-sm mt-3 btn-remove">Eliminar</button>
      `;
      equiposContainer.appendChild(div);
      div.querySelector('.btn-remove').addEventListener('click',()=>{
        div.remove(); actualizarSelectsEquipos();
      });
      div.querySelector('.equipo-nombre').addEventListener('input', actualizarSelectsEquipos);
    }
    function agregarComponenteConDatos(idx,nombre,equipoId,cant){
      const div = document.createElement('div');
      div.classList.add('componente-card');
      div.innerHTML=`
        <label>Nombre del Componente:</label>
        <input type="text" name="componentes[${idx}][nombre]" class="form-control" required value="${nombre}">
        <label class="mt-2">Equipo Relacionado:</label>
        <select name="componentes[${idx}][equipo_id]" class="form-control componente-equipo">
          <option value="">-- Selecciona un equipo --</option>
        </select>
        <label class="mt-2">Cantidad Esperada:</label>
        <input type="number" name="componentes[${idx}][cantidad_esperada]" class="form-control" min="0" value="${cant}" required>
        <button type="button" class="btn btn-danger btn-sm mt-3 btn-remove">Eliminar</button>
      `;
      componentesContainer.appendChild(div);
      div.querySelector('.btn-remove').addEventListener('click',()=>div.remove());
    }
    function actualizarSelectsEquipos(){
      const equipos = Array.from(equiposContainer.querySelectorAll('.equipo-card'))
        .map(div=>({
          id: div.dataset.id,
          nombre: div.querySelector('.equipo-nombre').value.trim()
        }))
        .filter(e=>e.nombre);
      document.querySelectorAll('.componente-equipo').forEach(select=>{
        const cur=select.value;
        select.innerHTML=`<option value="">-- Selecciona un equipo --</option>`;
        equipos.forEach(e=>{
          const o = document.createElement('option');
          o.value=e.id; o.textContent=e.nombre;
          if(e.id===cur) o.selected=true;
          select.appendChild(o);
        });
      });
    }

    // Inicial
    agregarEquipo(); 
    agregarComponente();
</script>
@endsection
