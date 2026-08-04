@foreach($rentals as $rental)
                            @php
                                $statusClass = match($rental->status) {
                                    'Programada' => 'status-programada',
                                    'En curso' => 'status-en-curso',
                                    'Finalizada' => 'status-finalizada',
                                    'Cancelada' => 'status-cancelada',
                                    default => 'status-programada',
                                };

                                $invoice = $rental->invoices->sortByDesc('id')->first();

                                $rentalPayload = [
                                    'id' => $rental->id,
                                    'cliente_id' => (string) $rental->cliente_id,
                                    'cliente_nombre' => $rental->cliente_nombre,
                                    'start_date' => optional($rental->start_date)->format('Y-m-d'),
                                    'end_date' => optional($rental->end_date)->format('Y-m-d'),
                                    'service_type' => $rental->service_type,
                                    'service_location' => $rental->service_location,
                                    'responsible' => $rental->responsible,
                                    'status' => $rental->status,
                                    'subtotal' => (float) $rental->subtotal,
                                    'iva' => (float) $rental->iva,
                                    'total' => (float) $rental->total,
                                    'notes' => $rental->notes,
                                    'invoice_id' => $invoice?->id,
                                    'invoice_status' => $invoice?->status,
                                    'items' => $rental->items->map(function ($item) {
                                        $equipment = $item->equipment;
                                        return [
                                            'id' => $item->id,
                                            'equipment_id' => (string) $item->equipment_id,
                                            'equipment_name' => $item->equipment_name,
                                            'serial_number' => $item->serial_number,
                                            'applied_price' => (float) $item->applied_price,
                                            'quantity' => (int) $item->quantity,
                                            'hours_used' => $item->hours_used,
                                            'observations' => $item->observations,
                                            'is_package' => (bool) ($equipment?->is_package),
                                            'components' => $equipment ? $equipment->components->map(function ($comp) {
                                                return [
                                                    'id' => $comp->id,
                                                    'name' => $comp->name,
                                                    'brand' => $comp->brand,
                                                    'model' => $comp->model,
                                                    'serial_number' => $comp->serial_number,
                                                    'quantity' => $comp->pivot->quantity ?? 1,
                                                    'condition' => $comp->pivot->condition ?? 'Buenas condiciones',
                                                ];
                                            })->values()->toArray() : [],
                                        ];
                                    })->values()->toArray(),
                                ];
                            @endphp

                            <tr class="rental-row" data-search="{{ strtolower($rental->cliente_nombre . ' ' . $rental->service_location) }}" data-status="{{ $rental->status }}">
                                td>
                                    <p class="rental-client">{{ $rental->cliente_nombre }}</p>
                                    <p class="rental-location">{{ $rental->service_location ?? 'Sin ubicación' }}</p>
                                </td>
                                <td><span class="rental-type">{{ $rental->service_type }}</span></td>
                                td>
                                    <span class="rental-dates">
                                        {{ optional($rental->start_date)->format('d/m/Y') }} - {{ optional($rental->end_date)->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td><span class="status-badge {{ $statusClass }}">{{ $rental->status }}</span></td>
                                <td class="money-text">${{ number_format($rental->total, 2) }}</td>
                                td>
                                    <div class="actions">
                                        <button type="button" class="icon-action view-details-btn" data-rental="{{ json_encode($rentalPayload) }}" title="Ver Detalles">
                                            <svg viewBox="0 0 24 24" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg>
                                        </button>
                                        <button type="button" class="icon-action edit-rental-btn" data-rental="{{ json_encode($rentalPayload) }}" title="Editar">
                                            <svg viewBox="0 0 24 24" fill="none"><path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                        <button type="button" class="icon-action delete trigger-delete" data-id="{{ $rental->id }}" title="Eliminar">
                                            <svg viewBox="0 0 24 24" fill="none"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                        <form id="delete-form-{{ $rental->id }}" action="{{ route('rentals.destroy', $rental->id) }}" method="POST" class="danger-hidden-form">
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>

{{-- MODAL: CREAR / EDITAR RENTA --}}
<div class="modal-overlay" id="rentalFormModal">
    <div class="modal-card form">
        <div class="modal-header">
            <h3 class="modal-title" id="formModalTitle">Nueva Renta</h3>
            <button type="button" class="modal-close closeModal" data-target="rentalFormModal">&times;</button>
        </div>
        <form id="rentalActionForm" method="POST" action="{{ route('rentals.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div class="modal-body">
                <div class="error-box" id="formErrorBox" style="display:none;">
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul id="errorList"></ul>
                </div>

                <div class="form-grid">
                    <div class="field-group">
                        <label class="field-label">Cliente *</label>
                        <select name="cliente_id" id="field_cliente_id" class="field-control" required>
                            <option value="">Selecciona un cliente</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">
                                    {{ $client->nombre_renta ?? trim(($client->nombre ?? '') . ' ' . ($client->apellido ?? '')) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Tipo de Servicio *</label>
                        <input type="text" name="service_type" id="field_service_type" class="field-control" placeholder="Ej. Operación, Renta seca" required>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Fecha Inicio *</label>
                        <input type="date" name="start_date" id="field_start_date" class="field-control" required>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Fecha Fin *</label>
                        <input type="date" name="end_date" id="field_end_date" class="field-control" required>
                    </div>
                    <div class="field-group span-2">
                        <label class="field-label">Ubicación del Servicio</label>
                        <input type="text" name="service_location" id="field_service_location" class="field-control" placeholder="Dirección o planta destino">
                    </div>
                    <div class="field-group">
                        <label class="field-label">Responsable</label>
                        <input type="text" name="responsible" id="field_responsible" class="field-control" placeholder="Nombre del operador/encargado">
                    </div>
                    <div class="field-group">
                        <label class="field-label">Estado Inicial</label>
                        <select name="status" id="field_status" class="field-control">
                            <option value="Programada">Programada</option>
                            <option value="En curso">En curso</option>
                            <option value="Finalizada">Finalizada</option>
                            <option value="Cancelada">Cancelada</option>
                        </select>
                    </div>

                    {{-- AGREGAR EQUIPOS --}}
                    <div class="field-group span-2" style="margin-top:14px;">
                        <div style="display:flex; justify-content:between; align-items:center; margin-bottom:10px;">
                            <label class="field-label" style="margin:0;">Equipos Asignados</label>
                            <button type="button" class="secondary-btn" id="btnToggleEquipBox" style="height:32px; padding:0 10px; font-size:.85rem;">+ Agregar Equipo</button>
                        </div>

                        <div class="equip-add-box" id="equipAddBox">
                            <div class="form-grid" style="grid-template-columns: 2fr 1fr 1fr auto; align-items: end;">
                                <div>
                                    <label class="field-label" style="font-size:.85rem;">Seleccionar Equipo/Paquete</label>
                                    <select id="tmp_equipment_select" class="field-control">
                                        <option value="">-- Elige un equipo --</option>
                                        @foreach($equipments as $equip)
                                            <option value="{{ $equip->id }}" data-price="{{ $equip->rental_price_day }}">
                                                {{ $equip->name }} (S/N: {{ $equip->serial_number ?? 'N/A' }}) - ${{ number_format($equip->rental_price_day, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="field-label" style="font-size:.85rem;">Precio/Día</label>
                                    <input type="number" id="tmp_applied_price" class="field-control" step="0.01">
                                </div>
                                <div>
                                    <label class="field-label" style="font-size:.85rem;">Cantidad</label>
                                    <input type="number" id="tmp_quantity" class="field-control" value="1" min="1">
                                </div>
                                <button type="button" class="main-btn" id="btnAddEquipmentToList" style="height:42px; padding:0 14px;">Añadir</button>
                            </div>
                        </div>

                        <div class="table-card" style="margin-top:8px;">
                            <table class="rentals-table" style="font-size:.9rem;">
                                <thead style="background:#f8fafc;">
                                    <tr>
                                        <th>Equipo</th>
                                        <th style="width:110px;">Precio/Día</th>
                                        <th style="width:80px; text-align:center;">Cant.</th>
                                        <th style="width:110px; text-align:right;">Subtotal</th>
                                        <th style="width:50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="selectedEquipmentsTableBody">
                                    {{-- Se renderiza vía JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="field-group span-2">
                        <label class="field-label">Notas / Observaciones</label>
                        <textarea name="notes" id="field_notes" class="field-textarea" placeholder="Detalles adicionales del contrato..."></textarea>
                    </div>
                </div>

                {{-- DESGLOSE FINANCIERO --}}
                <div style="margin-top:20px; background:#f8fafc; border-radius:14px; padding:14px; border:1px solid var(--line); max-width:320px; margin-left:auto;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:.9rem; color:var(--muted);">
                        <span>Subtotal:</span>
                        <span id="summary_subtotal">$0.00</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:.9rem; color:var(--muted);">
                        <span>IVA (16%):</span>
                        <span id="summary_iva">$0.00</span>
                    </div>
                    <hr style="border:0; border-top:1px solid var(--line); margin:8px 0;">
                    <div style="display:flex; justify-content:space-between; font-weight:900; font-size:1.1rem; color:var(--text);">
                        <span>Total:</span>
                        <span id="summary_total">$0.00</span>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="secondary-btn closeModal" data-target="rentalFormModal">Cancelar</button>
                <button type="submit" class="main-btn" style="height:42px;">Guardar Renta</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: DETALLES DE RENTA --}}
<div class="modal-overlay" id="rentalDetailModal">
    <div class="modal-card detail">
        <div class="modal-header">
            <div class="modal-title-row">
                <h3 class="modal-title" style="font-size:1.3rem;" id="detailClientName">Nombre del Cliente</h3>
                <span class="status-badge" id="detailStatusBadge">Estado</span>
            </div>
            <button type="button" class="modal-close closeModal" data-target="rentalDetailModal">&times;</button>
        </div>
        <div class="modal-body">
            
            {{-- FLUJO DE ESTADOS RÁPIDO --}}
            <div class="detail-status-flow">
                <p class="detail-status-flow-title">Cambiar estado de la orden</p>
                <div class="flow-row" id="statusFlowContainer">
                    {{-- Dinámico mediante JS dependiente del ID de la renta --}}
                </div>
                <form id="statusUpdateForm" method="POST" action="" class="danger-hidden-form">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" id="statusUpdateValue">
                </form>
            </div>

            <div class="grid-3" style="margin-bottom:18px;">
                <div class="mini-stat">
                    <div class="mini-stat-label">TIPO Y RESPONSABLE</div>
                    <div class="mini-stat-value" id="detailTypeAndResp">-</div>
                </div>
                <div class="mini-stat">
                    <div class="mini-stat-label">PERIODO DE RENTA</div>
                    <div class="mini-stat-value" id="detailDates">-</div>
                </div>
                <div class="mini-stat">
                    <div class="mini-stat-label">UBICACIÓN</div>
                    <div class="mini-stat-value" id="detailLocation">-</div>
                </div>
            </div>

            <div class="form-grid" style="grid-template-columns: 2fr 1fr; gap:16px;">
                
                {{-- COLUMNA IZQUIERDA: EQUIPOS --}}
                <div>
                    <label class="field-label">Estructura de Equipos y Componentes</label>
                    <div class="equip-list" id="detailEquipmentsList">
                        {{-- Inyectado dinámicamente --}}
                    </div>
                </div>

                {{-- COLUMNA DERECHA: DOCUMENTACIÓN --}}
                <div>
                    <label class="field-label">Documentación Vinculada</label>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        
                        <a href="#" class="doc-btn" id="doc_remision_btn" target="_blank">
                            <div class="doc-icon blue">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </div>
                            <div>
                                <p class="doc-title">Nota de Remisión</p>
                                <p class="doc-subtitle" id="doc_remision_sub">Generar PDF de salida</p>
                            </div>
                        </a>

                        <a href="#" class="doc-btn" id="doc_factura_btn" target="_blank">
                            <div class="doc-icon green" id="doc_factura_icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M12 8v8m-4-4h8"/></svg>
                            </div>
                            <div>
                                <p class="doc-title">Factura (CFDI)</p>
                                <p class="doc-subtitle" id="doc_factura_sub">No enlazada</p>
                            </div>
                        </a>

                        <div class="warn-box" style="margin-top:8px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>El desglose de IVA y totales se congela al timbrar la factura o finalizar el servicio.</span>
                        </div>
                    </div>

                    <div class="soft-card" style="margin-top:14px;">
                        <label class="field-label" style="font-size:.85rem; margin-bottom:4px;">Bitácora / Notas de campo</label>
                        <p id="detailNotesText" style="margin:0; font-size:.9rem; color:var(--text); white-space:pre-wrap;">Sin anotaciones.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Pasar catálogos globales de Blade a JS
    const globalEquipments = @json($equipmentsJson);
    let selectedItems = []; // Almacena ítems del formulario reactivo

    // --- MANEJO DE FILTROS ---
    const searchInput = document.getElementById('rentalSearch');
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('.rental-row');

    function filterTable() {
        const query = searchInput.value.toLowerCase();
        const status = statusFilter.value;

        rows.forEach(row => {
            const matchesSearch = row.getAttribute('data-search').includes(query);
            const matchesStatus = (status === 'all' || row.getAttribute('data-status') === status);
            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
    }
    if(searchInput) searchInput.addEventListener('input', filterTable);
    if(statusFilter) statusFilter.addEventListener('change', filterTable);

    // --- ACCIONES DE MODALES ---
    const closeButtons = document.querySelectorAll('.closeModal');
    closeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById(btn.getAttribute('data-target')).classList.remove('is-open');
        });
    });

    // --- LÓGICA DEL FORMULARIO DE RENTA (CREAR / EDITAR) ---
    const formModal = document.getElementById('rentalFormModal');
    const actionForm = document.getElementById('rentalActionForm');
    const formMethod = document.getElementById('formMethod');
    const formModalTitle = document.getElementById('formModalTitle');
    const btnToggleEquipBox = document.getElementById('btnToggleEquipBox');
    const equipAddBox = document.getElementById('equipAddBox');

    // Inicializar modal para creación
    document.getElementById('openCreateRental').addEventListener('click', () => {
        formModalTitle.textContent = "Nueva Renta";
        actionForm.action = "{{ route('rentals.store') }}";
        formMethod.value = "POST";
        actionForm.reset();
        selectedItems = [];
        renderSelectedEquipments();
        document.getElementById('formErrorBox').style.display = 'none';
        formModal.classList.add('is-open');
    });

    // Desplegar panel para añadir equipos
    btnToggleEquipBox.addEventListener('click', () => {
        equipAddBox.classList.toggle('is-open');
    });

    const tmpSelect = document.getElementById('tmp_equipment_select');
    const tmpPrice = document.getElementById('tmp_applied_price');
    const tmpQty = document.getElementById('tmp_quantity');

    // Cambiar precio sugerido según catálogo
    tmpSelect.addEventListener('change', () => {
        const opt = tmpSelect.options[tmpSelect.selectedIndex];
        tmpPrice.value = opt.dataset.price ? parseFloat(opt.dataset.price).toFixed(2) : '';
    });

    // Agregar equipo al listado temporal
    document.getElementById('btnAddEquipmentToList').addEventListener('click', () => {
        const eqId = tmpSelect.value;
        if(!eqId) return;

        const opt = tmpSelect.options[tmpSelect.selectedIndex];
        const fullText = opt.text.split(' - ')[0]; // Limpiar string

        const existing = selectedItems.find(i => i.equipment_id === eqId);
        if(existing) {
            existing.quantity += parseInt(tmpQty.value) || 1;
        } else {
            selectedItems.push({
                equipment_id: eqId,
                equipment_name: fullText,
                applied_price: parseFloat(tmpPrice.value) || 0,
                quantity: parseInt(tmpQty.value) || 1
            });
        }

        // Reset inputs auxiliares
        tmpSelect.value = '';
        tmpPrice.value = '';
        tmpQty.value = '1';
        equipAddBox.classList.remove('is-open');

        renderSelectedEquipments();
    });

    function renderSelectedEquipments() {
        const tbody = document.getElementById('selectedEquipmentsTableBody');
        tbody.innerHTML = '';
        let subtotal = 0;

        selectedItems.forEach((item, index) => {
            const itemSub = item.applied_price * item.quantity;
            subtotal += itemSub;

            tbody.innerHTML += `
                <tr>
                    <td>
                        <strong>${item.equipment_name}</strong>
                        <input type="hidden" name="items[${index}][equipment_id]" value="${item.equipment_id}">
                        <input type="hidden" name="items[${index}][applied_price]" value="${item.applied_price}">
                        <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                    </td>
                    <td>$${item.applied_price.toFixed(2)}</td>
                    <td style="text-align:center;">${item.quantity}</td>
                    <td style="text-align:right; font-weight:700;">$${itemSub.toFixed(2)}</td>
                    <td style="text-align:center;">
                        <button type="button" style="border:0; background:transparent; color:var(--red); cursor:pointer; font-size:1.1rem;" onclick="window.removeEquipmentItem(${index})">&times;</button>
                    </td>
                </tr>
            `;
        });

        if(selectedItems.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:var(--muted); padding:20px;">No hay equipos asignados a esta orden.</td></tr>`;
        }

        // Calcular impuestos globales
        const iva = subtotal * 0.16;
        const total = subtotal + iva;

        document.getElementById('summary_subtotal').textContent = `$${subtotal.toFixed(2)}`;
        document.getElementById('summary_iva').textContent = `$${iva.toFixed(2)}`;
        document.getElementById('summary_total').textContent = `$${total.toFixed(2)}`;
    }

    window.removeEquipmentItem = (index) => {
        selectedItems.splice(index, 1);
        renderSelectedEquipments();
    };

    // --- ACCIÓN: EDITAR RENTA ---
    document.querySelectorAll('.edit-rental-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const data = JSON.parse(btn.getAttribute('data-rental'));
            formModalTitle.textContent = "Editar Renta";
            actionForm.action = `/rentals/${data.id}`;
            formMethod.value = "PUT";

            document.getElementById('field_cliente_id').value = data.cliente_id;
            document.getElementById('field_service_type').value = data.service_type || '';
            document.getElementById('field_start_date').value = data.start_date || '';
            document.getElementById('field_end_date').value = data.end_date || '';
            document.getElementById('field_service_location').value = data.service_location || '';
            document.getElementById('field_responsible').value = data.responsible || '';
            document.getElementById('field_status').value = data.status;
            document.getElementById('field_notes').value = data.notes || '';

            selectedItems = data.items.map(i => ({
                equipment_id: i.equipment_id,
                equipment_name: i.equipment_name,
                applied_price: i.applied_price,
                quantity: i.quantity
            }));

            renderSelectedEquipments();
            document.getElementById('formErrorBox').style.display = 'none';
            formModal.classList.add('is-open');
        });
    });

    // --- MODAL: DETALLES DE LA ORDEN ---
    const detailModal = document.getElementById('rentalDetailModal');
    
    document.querySelectorAll('.view-details-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const data = JSON.parse(btn.getAttribute('data-rental'));

            document.getElementById('detailClientName').textContent = data.cliente_nombre;
            document.getElementById('detailNotesText').textContent = data.notes || 'Sin anotaciones particulares.';
            document.getElementById('detailLocation').textContent = data.service_location || 'No especificada';
            document.getElementById('detailTypeAndResp').textContent = `${data.service_type} ${data.responsible ? '(Resp: ' + data.responsible + ')' : ''}`;
            
            // Formatear fechas legibles
            const dStart = data.start_date ? data.start_date.split('-').reverse().join('/') : '';
            const dEnd = data.end_date ? data.end_date.split('-').reverse().join('/') : '';
            document.getElementById('detailDates').textContent = `${dStart} al ${dEnd}`;

            // Configurar el Badge de Estado principal
            const badge = document.getElementById('detailStatusBadge');
            badge.textContent = data.status;
            badge.className = "status-badge " + (
                data.status === 'Programada' ? 'status-programada' :
                data.status === 'En curso' ? 'status-en-curso' :
                data.status === 'Finalizada' ? 'status-finalizada' : 'status-cancelada'
            );

            // Armar los botones del flujo rápido de estados
            const flowContainer = document.getElementById('statusFlowContainer');
            flowContainer.innerHTML = '';
            document.getElementById('statusUpdateForm').action = `/rentals/${data.id}/status`;
            
            const estados = ['Programada', 'En curso', 'Finalizada', 'Cancelada'];
            estados.forEach(est => {
                let btnClass = "flow-btn next";
                if(est === data.status) btnClass = "flow-btn current";
                if(est === 'Cancelada') btnClass = "flow-btn cancel" + (data.status === 'Cancelada' ? ' current' : '');

                flowContainer.innerHTML += `<button type="button" class="${btnClass}" onclick="window.updateRentalStatus('${est}')">${est}</button>`;
            });

            // Configurar enlaces a PDFs e Invoice
            document.getElementById('doc_remision_btn').href = `/rentals/${data.id}/download-delivery`;
            
            const factBtn = document.getElementById('doc_factura_btn');
            const factSub = document.getElementById('doc_factura_sub');
            const factIcon = document.getElementById('doc_factura_icon');

            if (data.invoice_id) {
                factBtn.href = `/invoices/${data.invoice_id}`;
                factSub.textContent = `Folio asignado. Estado: ${data.invoice_status}`;
                factIcon.className = "doc-icon green";
                factBtn.removeAttribute('disabled');
            } else {
                factBtn.href = `/rentals/${data.id}/generate-invoice`;
                factSub.textContent = "Haga clic para timbrar orden de cobro";
                factIcon.className = "doc-icon amber";
            }

            // Renderizar árbol de Equipos y sus Componentes internos
            const equipListContainer = document.getElementById('detailEquipmentsList');
            equipListContainer.innerHTML = '';

            if(data.items.length === 0) {
                equipListContainer.innerHTML = '<p style="color:var(--muted); font-size:.95rem;">No hay componentes enlazados.</p>';
            } else {
                data.items.forEach(item => {
                    let componentsHtml = '';
                    if(item.is_package && item.components && item.components.length > 0) {
                        componentsHtml = `
                            <div class="component-list">
                                <p class="component-list-title">Componentes integrados en paquete</p>
                                ${item.components.map(c => `
                                    <div class="component-row">
                                        <div class="component-dot"></div>
                                        <span>${c.quantity}x ${c.name} (${c.brand ?? ''} S/N: ${c.serial_number ?? 'N/A'}) — <small>${c.condition}</small></span>
                                    </div>
                                `).join('')}
                            </div>
                        `;
                    }

                    equipListContainer.innerHTML += `
                        <div class="equip-item">
                            <div class="equip-item-head">
                                <div>
                                    <h4 class="equip-item-name">${item.equipment_name} ${item.is_package ? '<span class="package-chip">Paquete</span>' : ''}</h4>
                                    <p class="equip-item-meta">Cant: ${item.quantity} ${item.serial_number ? ' | S/N: ' + item.serial_number : ''}</p>
                                </div>
                                <span class="equip-item-price">$${(item.applied_price * item.quantity).toFixed(2)}</span>
                            </div>
                            ${componentsHtml}
                        </div>
                    `;
                });
            }

            detailModal.classList.add('is-open');
        });
    });

    // Cambiar estado mediante la barra de flujo rápido
    window.updateRentalStatus = (newStatus) => {
        document.getElementById('statusUpdateValue').value = newStatus;
        document.getElementById('statusUpdateForm').submit();
    };

    // --- ACCIÓN: ELIMINAR ORDEN (CONFIRMACIÓN) ---
    document.querySelectorAll('.trigger-delete').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const id = btn.getAttribute('data-id');
            if(confirm('¿Estás completamente seguro de que deseas eliminar este registro de renta? Esta acción puede alterar inventarios activos.')) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    });
});
</script>