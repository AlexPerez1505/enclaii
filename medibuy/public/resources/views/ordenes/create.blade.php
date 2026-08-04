@extends('layouts.app')
@section('titulo','Orden Servicio')
@section('title','Nuevo Mantenimiento')

@section('content')
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">

<style>
/* ================= NAMESPACE AISLADO (estilo mint del diseño anterior) ================= */
#mto{
  --bg:#eaebec;
  --card:#ffffff;
  --line:#e9edf2;
  --ink:#2a2e35;
  --muted:#77808a;
  --brand:#48cfad; --brand-hover:#34c29e; --brand-ink:#1b2a2f;
  --radius:14px; --shadow:0 16px 40px rgba(18,38,63,.12); --softshadow:0 10px 24px rgba(18,38,63,.10);
}
#mto *{box-sizing:border-box}
#mto .page{font-family:"Open Sans",sans-serif;background:var(--bg)}
#mto .wrap{max-width:1100px;margin:110px auto 40px;padding:0 16px}

/* ===== Panel contenedor ===== */
#mto .panel{background:var(--card);border-radius:16px;box-shadow:var(--shadow);overflow:hidden;border:1px solid var(--line)}
#mto .head{padding:22px 26px;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:14px}
#mto .hgroup h2{margin:0;font-weight:700;color:var(--ink);letter-spacing:.2px}
#mto .hgroup p{margin:4px 0 0;color:var(--muted);font-size:14px}
#mto .back-link{display:inline-flex;align-items:center;gap:8px;color:var(--muted);text-decoration:none;padding:8px 12px;border-radius:10px;border:1px solid var(--line);background:#fff;transition:box-shadow .2s,border-color .2s,color .2s}
#mto .back-link:hover{color:var(--ink);border-color:#dfe3e8;box-shadow:var(--softshadow)}

/* ===== Layout interno ===== */
#mto .layout{display:grid;gap:18px;grid-template-columns:1.6fr .9fr}
@media (max-width: 980px){#mto .layout{grid-template-columns:1fr}}

/* ===== Body del formulario ===== */
#mto .form{padding:26px}
#mto .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px}
@media (max-width: 820px){#mto .grid{grid-template-columns:1fr}}
#mto .g2{grid-template-columns:1fr 1fr}
#mto .mb-3{margin-bottom:14px}
#mto .mb-2{margin-bottom:10px}

/* ===== Wizard: visibilidad por paso ===== */
#mto .step{display:none;animation:fade .28s ease both}
#mto .step.active{display:block}
@keyframes fade{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}

/* Campos flotantes */
#mto .field{position:relative;background:#fff;border:1px solid var(--line);border-radius:12px;padding:16px 14px 12px;transition:box-shadow .2s,border-color .2s}
#mto .field:focus-within{border-color:#d2dbe6;box-shadow:0 8px 24px rgba(18,38,63,.08)}
#mto .field input,#mto .field select,#mto .field textarea{width:100%;border:0;outline:0;background:transparent;font-size:15px;color:var(--ink);padding-top:8px}
#mto .field textarea{resize:vertical}
#mto .field label{position:absolute;left:14px;top:14px;color:var(--muted);font-size:13px;transition:transform .15s ease,color .15s ease,font-size .15s ease,top .15s ease;pointer-events:none}
#mto .field input::placeholder{color:transparent}
#mto .field input:focus + label,
#mto .field input:not(:placeholder-shown) + label,
#mto .field select:focus + label,
#mto .field select:not(:placeholder-shown) + label,
#mto .field textarea:focus + label,
#mto .field textarea:not(:placeholder-shown) + label{top:8px;transform:translateY(-10px);font-size:11px;color:var(--brand-hover)}

/* Ayudas y errores */
#mto .hint{color:var(--muted);font-size:.9rem;margin-top:6px}
#mto .is-invalid{border-color:#f9c0c0 !important}
#mto .error{color:#cc4b4b;font-size:12px;margin-top:6px}

/* Uploader simple */
#mto .block{border:1px dashed #dfe3e8;border-radius:14px;padding:16px;background:#fafbfc}
#mto .uploader{display:grid;grid-template-columns:140px 1fr;gap:16px;align-items:center}
@media (max-width: 600px){#mto .uploader{grid-template-columns:1fr}}
#mto .thumb{width:140px;height:140px;border-radius:12px;overflow:hidden;background:#f0f2f5;display:grid;place-items:center;border:1px solid #edf0f3}
#mto .thumb img{width:100%;height:100%;object-fit:cover}
#mto .drop{display:flex;align-items:center;gap:14px;flex-wrap:wrap}
#mto .input-file{display:none}
#mto .drop .btn{background:var(--brand);color:#fff;border:1px solid var(--brand);border-radius:999px;padding:10px 16px;cursor:pointer;box-shadow:0 10px 20px rgba(72,207,173,.25);transition:background .2s,color .2s,box-shadow .2s}
#mto .drop .btn:hover{background:#fff;color:#111;box-shadow:0 10px 22px rgba(18,38,63,.18)}

/* Botones */
#mto .actions,.btn-row{display:flex;gap:12px;justify-content:flex-end;margin-top:10px}
#mto .btn{border:1px solid transparent;border-radius:12px;padding:12px 18px;font-weight:700;cursor:pointer;transition:transform .05s ease, box-shadow .2s ease, background .2s ease,color .2s ease,border-color .2s}
#mto .btn:active{transform:translateY(1px)}
#mto .btn-primary{background:var(--brand);color:#fff;border-color:var(--brand);box-shadow:0 12px 22px rgba(72,207,173,.26)}
#mto .btn-primary:hover{background:#fff;color:#111;border-color:var(--brand);box-shadow:0 12px 24px rgba(18,38,63,.16)}
#mto .btn-ghost{background:#fff;color:var(--ink);border:1px solid var(--line)}
#mto .btn-ghost:hover{border-color:#dfe3e8;box-shadow:0 10px 22px rgba(18,38,63,.10)}

/* Listas y chips */
#mto .list{border:1px dashed var(--line);border-radius:14px;padding:10px}
#mto .item{display:block;padding:10px 12px;border-radius:12px;border:1px solid var(--line);margin-bottom:8px;background:#fff}
#mto .item:last-child{margin-bottom:0}
#mto .chips{display:flex;flex-wrap:wrap;gap:8px;margin-top:8px}
#mto .chip{display:inline-flex;align-items:center;gap:8px;padding:.42rem .66rem;border-radius:999px;background:#f3f6fb;border:1px dashed var(--line);font-weight:800;font-size:.78rem;color:#0f172a}

/* Combobox cliente */
#mto .combo{position:relative}
#mto .combo .busy{position:absolute;right:10px;top:50%;transform:translateY(-50%);width:16px;height:16px;border:2px solid #cfe1ff;border-top-color:#1e88e5;border-radius:50%;animation:spin .7s linear infinite;display:none}
#mto .combo.busy .busy{display:inline-block}
#mto .combo-list{position:absolute;left:0;right:0;top:calc(100% + 6px);background:#fff;border:1px solid var(--line);border-radius:12px;box-shadow:var(--shadow);max-height:240px;overflow:auto;z-index:50;display:none}
#mto .combo-list.show{display:block}
#mto .combo-item{padding:10px 12px;border-bottom:1px solid #eef2f8;cursor:pointer}
#mto .combo-item:last-child{border-bottom:none}
#mto .combo-item:hover,.combo-item.active{background:#edf9f6}
#mto .chipSel{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border:1px solid var(--line);border-radius:999px;background:#f0fffa;color:#1b2a2f;font-weight:800}
#mto .chipSel .x{cursor:pointer;font-weight:900}
@keyframes spin{to{transform:rotate(360deg)}}

/* Botones de borrar */
#mto .del{display:inline-flex;align-items:center;gap:6px;padding:.34rem .6rem;border-radius:999px;border:1px dashed #fee2e2;background:#fff5f5;color:#b91c1c;font-weight:800;cursor:pointer;transition:background .2s}
#mto .del:hover{background:#ffe8e8}

/* Sidebar (progreso + resumen) */
#mto .side .card{position:sticky;top:16px;background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow);overflow:hidden}
#mto .side .pad{padding:16px}
#mto .side .title{font-weight:900;color:var(--ink);font-size:1rem;margin:0 0 6px}
#mto .side .muted{color:var(--muted);font-size:.86rem;margin:0}
#mto .meter{position:relative;height:14px;border-radius:999px;background:#edf2fb;overflow:hidden;box-shadow:inset 0 1px 0 rgba(255,255,255,.8),0 1px 2px rgba(2,8,23,.06)}
#mto .meter .fill{position:absolute;inset:0 auto 0 0;width:0%;background:linear-gradient(90deg,#48cfad,#8de8d6);transition:width .45s cubic-bezier(.22,.61,.36,1)}
#mto .progress-label{display:flex;justify-content:space-between;align-items:center}
#mto .progress-label .pct{font-weight:900;color:#0f172a;background:#fff;border:1px solid var(--line);border-radius:999px;padding:.18rem .5rem;box-shadow:0 6px 14px rgba(2,8,23,.06)}
#mto .steps{margin-top:8px;display:grid;gap:8px}
#mto .stepdot{display:flex;align-items:center;gap:10px}
#mto .dot{width:10px;height:10px;border-radius:50%;background:#dbe1f1;border:1px solid #dbe1f1;box-shadow:0 0 0 2px rgba(72,207,173,.12)}
#mto .stepdot.active .dot{background:var(--brand);border-color:var(--brand)}
#mto .stepdot .txt{font-weight:800;color:#0f172a;font-size:.9rem}
#mto .stepdot .small{color:#6b7280;font-size:.78rem}

/* Vista previa (step 4) */
#mto .preview{display:grid;gap:18px;grid-template-columns: 1.5fr .9fr}
@media(max-width:900px){#mto .preview{grid-template-columns:1fr}}
#mto .pv-card{background:#fff;border:1px solid var(--line);border-radius:16px;padding:16px;box-shadow:var(--shadow)}
#mto .pv-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;border-bottom:1px dashed var(--line);padding-bottom:12px;margin-bottom:12px}
#mto .pv-title{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
#mto .pv-badge{display:inline-flex;align-items:center;gap:8px;padding:.42rem .7rem;border-radius:999px;font-weight:900;font-size:.8rem;background:#f0fffa;color:#19795f;border:1px solid #bbefe3}
#mto .pv-badge.accent{background:#eef7ff;color:#165a72;border-color:#cfeaff}
#mto .kv{display:grid;grid-template-columns:1fr 1fr;gap:10px}
@media(max-width:560px){#mto .kv{grid-template-columns:1fr}}
#mto .kv .item{background:linear-gradient(180deg,#fff,#fbfdff);border:1px solid var(--line);border-radius:12px;padding:12px}
#mto .kv .k{color:var(--muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.5px}
#mto .kv .v{color:var(--ink);font-weight:900;margin-top:4px;word-break:break-word}
#mto .pv-table{width:100%;border-collapse:separate;border-spacing:0 8px}
#mto .pv-table thead th{text-align:left;font-size:.8rem;color:var(--muted);font-weight:800;padding:0 10px}
#mto .pv-row{background:#fff;border:1px solid var(--line);border-radius:12px;overflow:hidden}
#mto .pv-cell{padding:10px;vertical-align:top}
#mto .pv-chip{display:inline-flex;align-items:center;gap:8px;padding:.38rem .66rem;border-radius:999px;font-weight:800;font-size:.78rem;border:1px solid var(--line);background:#f6f8fc;color:#475569}
#mto .pv-chip.ok{background:#e9fff7;color:#19795f;border-color:#c6f6e7}
#mto .pv-chip.rev{background:#fff6e6;color:#b46911;border-color:#ffe5b4}
#mto .pv-chip.aj{ background:#eef9f5;color:#1b8a6b;border-color:#c9efdf}
#mto .pv-chip.rep{background:#fff0f2;color:#b4233b;border-color:#ffd5db}
#mto .pv-chip.rpl{background:#f3ecff;color:#6d28d9;border-color:#e3d7ff}
#mto .pv-chip.na{ background:#f1f5f9;color:#334155;border-color:#e2e8f0}
#mto .pv-photo{width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:12px;border:1px solid var(--line);background:#f6f8fc}

/* Overlay IA */
#mto .overlay{position:fixed;inset:0;z-index:9999;display:none;align-items:center;justify-content:center;background:
  radial-gradient(1000px 600px at 20% -10%, rgba(174,208,255,.18) 0%, transparent 55%),
  radial-gradient(900px 700px at 120% 10%, rgba(255,201,222,.18) 0%, transparent 55%),
  rgba(250,252,255,.35);backdrop-filter: blur(14px) saturate(120%)}
#mto .overlay.show{display:flex}
#mto .ai-badge{position:fixed;top:18px;left:18px;padding:.48rem .8rem;border-radius:999px;background:rgba(255,255,255,.7);border:1px solid rgba(255,255,255,.9);font-weight:800;font-size:.78rem;color:#1e88e5;box-shadow:0 10px 24px rgba(2,8,23,.10)}
#mto .ai-caption{position:fixed;bottom:16px;left:50%;transform:translateX(-50%);padding:.56rem .96rem;border-radius:12px;background:rgba(255,255,255,.78);border:1px solid rgba(255,255,255,.92);color:#0e1726;font-weight:700;font-size:.86rem;box-shadow:0 10px 24px rgba(2,8,23,.10)}
#mto .skeleton .item{background:linear-gradient(90deg,#f3f6fb 25%,#eaf0fb 37%,#f3f6fb 63%);background-size:400% 100%;animation:sheen 1.2s ease-in-out infinite;border-style:dashed;height:42px}
@keyframes sheen{0%{background-position:100% 0}100%{background-position:-100% 0}}

/* ==== Animaciones extra ==== */
#mto .item.pop-in{opacity:0; transform:translateY(6px)}
#mto .item.pop-in.show{opacity:1; transform:none; transition:opacity .35s ease, transform .35s ease}

/* Canvas SVG del overlay */
#mto .ai-stage{ position:relative; width:min(1100px, 96vw); aspect-ratio: 4 / 3; }
#mto .ai-stage svg{ width:100%; height:100%; visibility:hidden; display:block }
#mto .ell, #mto #ai{ fill:none }
</style>

<div id="mto" class="page" style="margin-top:-65px;">
  <div class="wrap">
    <div class="panel">
      <div class="head">
        <div class="hgroup">
          <h2>Orden de Servicio / Mantenimiento</h2>
          <p>Captura los datos, genera el checklist y confirma antes del PDF.</p>
        </div>
        <a href="{{ route('productos.cards') }}" class="back-link" title="Volver">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
          Volver
        </a>
      </div>

      <div class="form">
        <div class="layout">
          {{-- ===================== COLUMNA IZQUIERDA: WIZARD ===================== --}}
          <form id="wizard-form" action="{{ route('ordenes.store') }}" method="POST" novalidate enctype="multipart/form-data">
            @csrf

            {{-- STEP 1 --}}
            <div class="mb-3 step active" data-step="1">
              <div class="mb-3">
                <div class="field combo" id="cliente-combo" aria-haspopup="listbox" aria-expanded="false">
                  <input type="text" id="cliente_search" placeholder=" " autocomplete="off" role="combobox" aria-autocomplete="list" aria-controls="cliente_list" aria-activedescendant="">
                  <label for="cliente_search">Cliente (nombre, empresa, email…)</label>
                  <div class="busy" aria-hidden="true"></div>
                  <div class="combo-list" id="cliente_list" role="listbox"></div>
                </div>
                <input type="hidden" name="cliente_id" id="cliente_id" required>
                <div id="cliente_chip" style="margin-top:8px"></div>
                <div class="hint">Escribe al menos 2 caracteres y selecciona de la lista.</div>
                @error('cliente_id')<div class="error">{{ $message }}</div>@enderror
              </div>

              <div class="grid">
                <div class="field @error('fecha_entrada') is-invalid @enderror">
                  <input type="date" name="fecha_entrada" id="fecha_entrada" placeholder=" " required>
                  <label for="fecha_entrada">Fecha de entrada</label>
                </div>
                @error('fecha_entrada')<div class="error">{{ $message }}</div>@enderror

                <div class="field">
                  <input type="text" id="fecha_mantenimiento" placeholder=" " readonly value="{{ now()->format('Y-m-d') }}">
                  <label for="fecha_mantenimiento">Fecha de mantenimiento</label>
                  <input type="hidden" name="fecha_mantenimiento" id="fecha_mantenimiento_input" value="{{ now()->format('Y-m-d') }}">
                </div>
              </div>

              <div class="actions">
                <button type="button" class="btn btn-primary next">Siguiente →</button>
              </div>
            </div>

            {{-- STEP 2 --}}
            <div class="mb-3 step" data-step="2">
              <div class="grid">
                <div class="field">
                  <select name="proximo_mantenimiento" id="proximo_mantenimiento" placeholder=" " required>
                    <option value="" hidden></option>
                    <option value="3">3 meses</option>
                    <option value="6">6 meses</option>
                    <option value="12">12 meses</option>
                  </select>
                  <label for="proximo_mantenimiento">Próximo mantenimiento (meses)</label>
                </div>

                <div class="field">
                  <input type="text" name="equipo" id="equipo" placeholder=" " required>
                  <label for="equipo">Equipo</label>
                </div>

                <div class="field">
                  <input type="text" name="marca" id="marca" placeholder=" ">
                  <label for="marca">Marca</label>
                </div>

                <div class="field">
                  <input type="text" name="modelo" id="modelo" placeholder=" ">
                  <label for="modelo">Modelo</label>
                </div>

                <div class="field">
                  <input type="text" name="numero_serie" id="numero_serie" placeholder=" ">
                  <label for="numero_serie">Número de serie</label>
                </div>
              </div>

              <div class="field" style="margin-top:12px">
                <textarea name="observaciones" id="observaciones" rows="3" placeholder=" "></textarea>
                <label for="observaciones">Observaciones</label>
              </div>

              <div class="block" style="margin-top:16px">
                <div class="uploader">
                  <div class="thumb">
                    <img id="foto_preview" src="https://via.placeholder.com/280x280.png?text=Sin+imagen" alt="Previsualización">
                  </div>
                  <div class="drop">
                    <label class="btn" for="foto_equipo">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;"><path d="M12 5v14M5 12h14"/></svg>
                      Subir imagen
                    </label>
                    <input id="foto_equipo" class="input-file" type="file" name="foto_equipo" accept="image/*">
                    <span class="hint">Formatos: JPG/PNG/WebP · Máx 5MB</span>
                  </div>
                </div>
              </div>

              <div class="actions">
                <button type="button" class="btn btn-ghost prev">← Anterior</button>
                <button type="button" class="btn btn-primary next">Siguiente →</button>
              </div>
            </div>

            {{-- STEP 3 --}}
            <div class="mb-3 step" data-step="3">
              <div class="grid">
                <div class="field">
                  <input type="text" id="nombre_equipo_libre" placeholder=" ">
                  <label for="nombre_equipo_libre">Equipo (opcional para IA)</label>
                </div>
                <div class="field">
                  <select id="servicio" placeholder=" ">
                    <option value="preventivo">Preventivo</option>
                    <option value="correctivo">Correctivo</option>
                    <option value="mixto">Mixto</option>
                  </select>
                  <label for="servicio">Tipo de servicio</label>
                </div>
                <div class="field">
                  <input type="text" id="sintomas" placeholder=" ">
                  <label for="sintomas">Síntomas / Observaciones adicionales</label>
                </div>
              </div>

              <div class="actions" style="justify-content:flex-start;margin-top:8px">
                <button type="button" class="btn btn-primary" id="btn-sugerir-ia">
                  <span class="btn-text">✨ Sugerir checklist (IA)</span>
                </button>
              </div>

              <input type="hidden" name="template_slug" value="ia-dynamic">

              <div class="mb-3">
                <div class="hgroup" style="margin:6px 0 8px"><p class="h" style="margin:0;color:var(--ink);font-weight:800">Checklist sugerido</p></div>
                <div id="preventivo-box" class="list">
                  <div class="hint">Pulsa el botón de IA para generar secciones e ítems…</div>
                </div>
              </div>

              <div class="mb-3">
                <div class="hgroup" style="margin:6px 0 8px"><p class="h" style="margin:0;color:var(--ink);font-weight:800">Mantenimiento realizado</p></div>
                <div id="realizado-box" class="list"></div>
                <div class="actions" style="justify-content:flex-start;margin-top:8px">
                  <div class="field" style="max-width:520px;width:100%">
                    <input type="text" id="accion_libre" placeholder=" ">
                    <label for="accion_libre">Agregar acción manual (Enter)</label>
                  </div>
                </div>
              </div>

              {{-- Diagnóstico / Ingeniería / Riesgos / Notas (resumen IA) --}}
              <div class="mb-3">
                <div class="hgroup" style="margin:6px 0 8px"><p class="h" style="margin:0;color:var(--ink);font-weight:800">Diagnóstico preliminar (IA)</p></div>
                <div id="diag-box" class="list"><div class="hint">Aquí aparecerá el diagnóstico basado en el modelo, marca y síntomas.</div></div>
              </div>
              <div class="mb-3">
                <div class="hgroup" style="margin:6px 0 8px"><p class="h" style="margin:0;color:var(--ink);font-weight:800">Plan de revisión para Ingeniería</p></div>
                <div id="eng-box" class="list"><div class="hint">Pasos sugeridos para revisar, aislar y corregir la falla.</div></div>
              </div>

              <div class="actions">
                <button type="button" class="btn btn-ghost prev">← Anterior</button>
                <button type="button" class="btn btn-primary next">Siguiente →</button>
              </div>
            </div>

            {{-- STEP 4 --}}
            <div class="mb-3 step" data-step="4">
              <div class="preview">
                <section class="pv-card">
                  <div class="pv-head">
                    <div class="pv-title">
                      <span class="pv-badge">Revisión final</span>
                      <span class="pv-badge accent" id="pvBadgeCliente">Cliente</span>
                    </div>
                    <div class="chips">
                      <span class="pv-chip ok">Bueno y Funcional</span>
                      <span class="pv-chip rev">Revisado</span>
                      <span class="pv-chip aj">Ajustado</span>
                      <span class="pv-chip rep">Reparado</span>
                      <span class="pv-chip rpl">Reemplazado</span>
                      <span class="pv-chip na">No aplica</span>
                    </div>
                  </div>

                  <div class="kv">
                    <div class="item"><div class="k">Cliente</div><div class="v" id="c_cliente"></div></div>
                    <div class="item"><div class="k">Equipo</div><div class="v" id="c_equipo"></div></div>
                    <div class="item"><div class="k">Fecha de entrada</div><div class="v" id="c_fentrada"></div></div>
                    <div class="item"><div class="k">Fecha de mantenimiento</div><div class="v" id="c_fmanto"></div></div>
                    <div class="item"><div class="k">Marca</div><div class="v" id="c_marca"></div></div>
                    <div class="item"><div class="k">Modelo</div><div class="v" id="c_modelo"></div></div>
                    <div class="item"><div class="k">N° de serie</div><div class="v" id="c_serie"></div></div>
                    <div class="item"><div class="k">Observaciones</div><div class="v" id="c_obs"></div></div>
                  </div>

                  <div style="margin-top:12px">
                    <h4 style="margin:0 0 8px;font-size:1.02rem;color:var(--ink);font-weight:900">Checklist del mantenimiento</h4>
                    <table class="pv-table" id="pvChecklist">
                      <thead>
                        <tr><th style="width:42%">Ítem</th><th style="width:28%">Sección</th><th style="width:30%">Estatus</th></tr>
                      </thead>
                      <tbody><!-- dinámico --></tbody>
                    </table>
                  </div>

                  <div style="margin-top:14px">
                    <h4 style="margin:0 0 6px;font-size:1.02rem;color:var(--ink);font-weight:900">Diagnóstico</h4>
                    <div id="pvDiag">
                      <div class="chips" style="margin:8px 0" id="pvDiagHallazgos"></div>
                      <div style="margin:6px 0"><span class="pv-chip" id="pvDiagPrioridad">Prioridad: —</span></div>
                      <div class="grid" style="grid-template-columns:1fr 1fr;gap:10px">
                        <div class="item"><div class="k">Hipótesis</div><div class="v" id="pvDiagHipotesis">—</div></div>
                        <div class="item"><div class="k">Pruebas sugeridas</div><div class="v" id="pvDiagPruebas">—</div></div>
                        <div class="item"><div class="k">Piezas posibles</div><div class="v" id="pvDiagPiezas">—</div></div>
                      </div>
                    </div>
                  </div>

                  <div style="margin-top:12px">
                    <h4 style="margin:0 0 6px;font-size:1.02rem;color:var(--ink);font-weight:900">Acciones realizadas</h4>
                    <div class="chips" id="pvAcciones"><!-- chips dinámicos --></div>
                  </div>
                </section>

                <aside class="pv-card">
                  <img id="pv_foto" alt="Foto del equipo" class="pv-photo" style="display:none;margin-bottom:10px;">
                  <div class="chips" style="margin-bottom:10px">
                    <span class="chip">Próximo mantenimiento: <b id="c_prox">—</b></span>
                    <span class="chip">Total ítems: <b id="pvTotalItems">0</b></span>
                    <span class="chip">“Bueno y Funcional”: <b id="pvOkCount">0</b></span>
                  </div>
                  <div class="actions" style="border-top:1px dashed var(--line);padding-top:12px">
                    <button type="button" class="btn btn-ghost prev">← Editar</button>
                    <button type="submit" class="btn btn-primary">✔️ Generar PDF</button>
                  </div>
                </aside>
              </div>
            </div>

          </form>

          {{-- ===================== COLUMNA DERECHA: PROGRESO + RESUMEN ===================== --}}
          <div class="side">
            <div class="card">
              <div class="pad">
                <div class="progress-label">
                  <p class="title" id="progress-title" style="margin:0">Progreso</p>
                  <span class="pct" id="ringLabel">25%</span>
                </div>
                <p class="muted" id="progress-sub" style="margin:2px 0 6px">Paso 1 de 4 · Cliente & Fecha</p>

                <div class="meter" aria-hidden="true" style="margin:8px 0 10px">
                  <div class="fill" id="bar"></div>
                  <div class="shine"></div>
                </div>

                <div class="steps" id="stepsList">
                  <div class="stepdot active"><span class="dot"></span><div><div class="txt">Cliente & Fecha</div><div class="small">Datos básicos</div></div></div>
                  <div class="stepdot"><span class="dot"></span><div><div class="txt">Equipo & Datos</div><div class="small">Ficha técnica</div></div></div>
                  <div class="stepdot"><span class="dot"></span><div><div class="txt">Checklist (IA)</div><div class="small">Generar y editar</div></div></div>
                  <div class="stepdot"><span class="dot"></span><div><div class="txt">Confirmar</div><div class="small">Previsualización</div></div></div>
                </div>
              </div>
            </div>

            <div class="card" style="margin-top:12px">
              <div class="pad">
                <p class="title">Resumen en vivo</p>
                <p class="muted">Se actualiza conforme llenas el wizard.</p>
                <div class="chips" id="r_chips" style="margin-top:10px"></div>
                <div class="list" style="margin-top:10px;border-style:solid">
                  <div class="item" style="display:flex;justify-content:space-between"><span style="color:var(--muted);font-weight:700">Cliente</span><span id="r_cliente" style="font-weight:900;color:var(--ink)">—</span></div>
                  <div class="item" style="display:flex;justify-content:space-between"><span style="color:var(--muted);font-weight:700">Equipo</span><span id="r_equipo" style="font-weight:900;color:var(--ink)">—</span></div>
                  <div class="item" style="display:flex;justify-content:space-between"><span style="color:var(--muted);font-weight:700">Entrada</span><span id="r_fentrada" style="font-weight:900;color:var(--ink)">—</span></div>
                  <div class="item" style="display:flex;justify-content:space-between"><span style="color:var(--muted);font-weight:700">Mantenimiento</span><span id="r_fmanto" style="font-weight:900;color:var(--ink)">{{ now()->format('Y-m-d') }}</span></div>
                  <div class="item" style="display:flex;justify-content:space-between"><span style="color:var(--muted);font-weight:700">Próximo (meses)</span><span id="r_prox" style="font-weight:900;color:var(--ink)">—</span></div>
                  <div class="item" style="display:flex;justify-content:space-between"><span style="color:var(--muted);font-weight:700">Ítems checklist</span><span id="r_items" style="font-weight:900;color:var(--ink)">0</span></div>
                  <div class="item" style="display:flex;justify-content:space-between"><span style="color:var(--muted);font-weight:700">Acciones</span><span id="r_acciones" style="font-weight:900;color:var(--ink)">0</span></div>
                </div>
              </div>
            </div>

          </div> {{-- /side --}}
        </div> {{-- /layout --}}
      </div> {{-- /form --}}
    </div> {{-- /panel --}}
  </div> {{-- /wrap --}}

  {{-- Overlay IA con animación --}}
  <div class="overlay" id="ai-overlay" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="ai-badge">Generando checklist…</div>

    <div class="ai-stage" aria-hidden="true">
      <svg id="mtoSVG" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600">
        <defs>
          <linearGradient id="mtoGrad" x1="513.98" y1="290" x2="479.72" y2="320" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#000" stop-opacity="0"/>
            <stop offset=".15" stop-color="#EF476F"/>
            <stop offset=".4"  stop-color="#359eee"/>
            <stop offset=".6"  stop-color="#03cea4"/>
            <stop offset=".78" stop-color="#FFC43D"/>
            <stop offset="1" stop-color="#000" stop-opacity="0"/>
          </linearGradient>
        </defs>

        @for($i=0;$i<30;$i++)
          <ellipse class="ell" cx="400" cy="300" rx="80" ry="80"/>
        @endfor

        <path id="ai" opacity="0" d="m417.17,323.85h-34.34c-3.69,0-6.67-2.99-6.67-6.67v-34.34c0-3.69,2.99-6.67,6.67-6.67h34.34c3.69,0,6.67,2.99,6.67,6.67v34.34c0,3.69-2.99,6.67-6.67,6.67Zm-5.25-12.92v-21.85c0-.55-.45-1-1-1h-21.85c-.55,0-1,.45-1,1v21.85c0,.55.45,1,1,1h21.85c.55,0,1-.45,1-1Zm23.08-16.29h-11.15m-47.69,0h-11.15m70,10.73h-11.15m-47.69,0h-11.15m40.37,29.63v-11.15m0-47.69v-11.15m-10.73,70v-11.15m0-47.69v-11.15"
          stroke="url(#mtoGrad)" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2"/>
      </svg>
    </div>

    <div class="ai-caption">Analizando equipo y creando secciones…</div>
  </div>
</div>

<!-- GSAP (solo animación visual del overlay) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/CustomEase.min.js" defer></script>

<script>
(function(){
  const $ = s => document.querySelector(s);
  const $$ = s => [...document.querySelectorAll(s)];

  /* ===== Navegación de pasos ===== */
  const steps = $$('#mto .step'); const total = steps.length; let current = 0;
  const names = ['Cliente & Fecha','Equipo & Datos','Checklist (IA)','Confirmar'];
  const bar = $('#mto #bar'); const pctLabel = $('#mto #ringLabel'); const pSub = $('#mto #progress-sub');
  const dots = $$('#mto .steps .stepdot');

  function updateProgress(){
    const pct = Math.round(((current+1)/total)*100);
    pctLabel.textContent = pct + '%';
    bar.style.width = pct + '%';
    pSub.textContent = `Paso ${current+1} de ${total} · ${names[current]}`;
    dots.forEach((d,i)=> d.classList.toggle('active', i<=current));
  }
  function show(i){
    steps.forEach((s,idx)=> s.classList.toggle('active', idx===i));
    current = i;
    if(i===3) fillConfirm();
    updateProgress();
    updateLive();
  }
  function validate(i){
    let ok = true;
    steps[i].querySelectorAll('[required]').forEach(x=>{ if(!x.value){ ok=false; }});
    if(i===0 && !$('#mto #cliente_id').value){ ok = false; }
    if(i===1 && !$('#mto #equipo').value.trim()){ ok = false; }
    return ok;
  }
  $$('#mto .next').forEach(b=> b.addEventListener('click', ()=>{
    if(!validate(current)) return;
    $('#mto #fecha_mantenimiento_input').value = $('#mto #fecha_mantenimiento').value || $('#mto #fecha_mantenimiento_input').value;
    show(Math.min(total-1,current+1));
  }));
  $$('#mto .prev').forEach(b=> b.addEventListener('click', ()=> show(Math.max(0,current-1))));

  /* ===== Uploader preview ===== */
  const inpFoto = $('#mto #foto_equipo'); const imgPrev = $('#mto #foto_preview'); let fotoDataURL = null;
  inpFoto?.addEventListener('change', e=>{
    const f = e.target.files?.[0]; if(!f) return;
    const reader = new FileReader();
    reader.onload = ev => { fotoDataURL = ev.target.result; imgPrev.src = fotoDataURL; };
    reader.readAsDataURL(f);
  });

  /* ===== Combobox Cliente (AJAX robusto con teclado y normalización) ===== */
  const combo = $('#mto #cliente-combo');
  const list  = $('#mto #cliente_list');
  const chip  = $('#mto #cliente_chip');
  const fCliente = $('#mto #cliente_search');
  const fClienteId = $('#mto #cliente_id');
  let itemsCache = [], activeIndex=-1, debounceId=null, ctrl=null;

  function setBusy(b){ combo.classList.toggle('busy', b); }
  function openList(){ list.classList.add('show'); combo.setAttribute('aria-expanded','true'); }
  function closeList(){ list.classList.remove('show'); combo.setAttribute('aria-expanded','false'); activeIndex=-1; fCliente.setAttribute('aria-activedescendant',''); }
  function normalizeApiData(data){
    const rows = Array.isArray(data) ? data : (Array.isArray(data?.items) ? data.items : []);
    return rows.map(r=>({
      id: r.id ?? r.value ?? r.uuid ?? r.ID ?? r.pk ?? '',
      label: r.label ?? r.nombre ?? r.name ?? r.razon_social ?? r.email ?? '—',
      desc: r.desc ?? r.empresa ?? r.email ?? r.telefono ?? ''
    })).filter(x=>x.id!=='' && x.label!=='—');
  }
  function renderList(items){
    list.innerHTML=''; itemsCache = items||[]; activeIndex=-1;
    if(!itemsCache.length){
      const el=document.createElement('div'); el.className='combo-item'; el.style.opacity=.75; el.textContent='Sin resultados';
      el.setAttribute('role','option'); el.id='opt-empty'; list.appendChild(el);
    }else{
      itemsCache.forEach((it,i)=>{
        const el=document.createElement('div'); el.className='combo-item'; el.setAttribute('role','option'); el.id='opt-'+i;
        el.innerHTML=`<div style="font-weight:800;color:#0e1726">${it.label}</div><div style="color:#6b7280;font-size:12px">${it.desc||''}</div>`;
        el.addEventListener('mouseenter',()=> setActive(i));
        el.addEventListener('mousedown', ev=>{ ev.preventDefault(); pick(i); });
        list.appendChild(el);
      });
    }
    openList();
  }
  function setActive(i){
    const items=[...list.querySelectorAll('.combo-item')];
    items.forEach((el,idx)=> el.classList.toggle('active', idx===i));
    activeIndex=i;
    if(items[i]){ fCliente.setAttribute('aria-activedescendant', items[i].id); items[i].scrollIntoView({block:'nearest'}); }
  }
  function pick(i){
    const it = itemsCache[i]; if(!it) return;
    fClienteId.value = it.id; fCliente.value = it.label; closeList();
    chip.innerHTML = `<span class="chipSel">${it.label} <span class="x" title="Quitar">×</span></span>`;
    chip.querySelector('.x').addEventListener('click', ()=>{
      fClienteId.value=''; fCliente.value=''; chip.innerHTML=''; fCliente.focus(); updateLive();
    });
    updateLive();
  }
  function doSearch(q){
    if(ctrl) ctrl.abort();
    ctrl = new AbortController();
    setBusy(true);
    const url = new URL(`{{ route('clientes.search') }}`);
    url.searchParams.set('q', q);
    fetch(url.toString(),{headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},signal:ctrl.signal})
      .then(r=> r.ok ? r.json() : Promise.reject(new Error('HTTP '+r.status)))
      .then(json=> renderList(normalizeApiData(json)))
      .catch(e=>{ if(e?.name!=='AbortError'){ renderList([]); }})
      .finally(()=> setBusy(false));
  }
  fCliente.addEventListener('input', ()=>{
    const q = fCliente.value.trim(); fClienteId.value=''; chip.innerHTML='';
    if(debounceId) clearTimeout(debounceId);
    if(q.length < 2){ closeList(); updateLive(); return; }
    debounceId = setTimeout(()=> doSearch(q), 250);
  });
  fCliente.addEventListener('keydown', (e)=>{
    if(!list.classList.contains('show')){ if(e.key==='ArrowDown'){ openList(); } return; }
    const count = itemsCache.length;
    if(e.key==='ArrowDown'){ e.preventDefault(); if(count===0) return; setActive( (activeIndex+1) % count ); }
    else if(e.key==='ArrowUp'){ e.preventDefault(); if(count===0) return; setActive( (activeIndex-1+count) % count ); }
    else if(e.key==='Enter'){ if(activeIndex>=0){ e.preventDefault(); pick(activeIndex); } }
    else if(e.key==='Escape'){ closeList(); }
  });
  document.addEventListener('click', (e)=>{ if(!combo.contains(e.target)) closeList(); });

  /* ===== IA checklist ===== */
  const preventivoBox = $('#mto #preventivo-box');
  const realizadoBox  = $('#mto #realizado-box');
  const btnIA         = $('#mto #btn-sugerir-ia');
  const overlay       = $('#mto #ai-overlay');
  const btnText       = btnIA?.querySelector('.btn-text');
  let lastAIData = null;

  function setOverlay(on){ overlay.classList.toggle('show', on); }
  function setBtnLoading(on){ if(!btnIA) return; if(on){ btnIA.setAttribute('disabled','disabled'); btnText.innerHTML = `Generando…`; } else { btnIA.removeAttribute('disabled'); btnText.textContent = '✨ Sugerir checklist (IA)'; } }
  function showSkeleton(){
    preventivoBox.classList.add('skeleton'); preventivoBox.innerHTML='';
    for(let i=0;i<6;i++){ const sk=document.createElement('div'); sk.className='item'; preventivoBox.appendChild(sk); }
    realizadoBox.innerHTML='';
  }
  function hideSkeleton(){ preventivoBox.classList.remove('skeleton'); }

  function renderPreventivo(secciones){
    preventivoBox.innerHTML = '';
    let idx=0;
    (secciones||[]).forEach(sec=>{
      const head=document.createElement('div'); head.className='item'; head.style.fontWeight='900'; head.textContent=sec.titulo||'Sección';
      preventivoBox.appendChild(head);
      (sec.items||[]).forEach(it=>{
        const row=document.createElement('div'); row.className='item';
        row.innerHTML = `
          <div style="display:flex;justify-content:space-between;gap:8px;align-items:center;flex-wrap:wrap">
            <div>${it.nombre}</div>
            <div style="display:flex;align-items:center;gap:8px">
              <select name="mto_preventivo[${idx}][estatus]" style="max-width:220px;border:1px solid var(--line);border-radius:12px;padding:8px 10px">
                <option ${it.resultado_sugerido==='Bueno y Funcional'?'selected':''}>Bueno y Funcional</option>
                <option ${it.resultado_sugerido==='Revisado'?'selected':''}>Revisado</option>
                <option ${it.resultado_sugerido==='Ajustado'?'selected':''}>Ajustado</option>
                <option ${it.resultado_sugerido==='Reparado'?'selected':''}>Reparado</option>
                <option ${it.resultado_sugerido==='Reemplazado'?'selected':''}>Reemplazado</option>
                <option ${it.resultado_sugerido==='No aplica'?'selected':''}>No aplica</option>
              </select>
              <button type="button" class="del del-item" title="Eliminar ítem">🗑</button>
            </div>
          </div>
          <input type="hidden" name="mto_preventivo[${idx}][seccion]" value="${(sec.titulo||'').replaceAll('"','&quot;')}">
          <input type="hidden" name="mto_preventivo[${idx}][item]" value="${(it.nombre||'').replaceAll('"','&quot;')}">`;
        preventivoBox.appendChild(row); idx++;
      });
    });
    if(!preventivoBox.children.length){ preventivoBox.innerHTML = '<div class="hint">Sin ítems</div>'; }
    updateLive();
  }
  function renderRealizado(list){
    realizadoBox.innerHTML = '';
    (list||[]).forEach(txt=>{
      const el=document.createElement('div'); el.className='item';
      el.innerHTML = `<label style="display:flex;align-items:center;gap:10px;justify-content:space-between">
        <span><input type="checkbox" name="mto_realizado[]" value="${(txt||'').replaceAll('"','&quot;')}"> ${txt}</span>
        <button type="button" class="del del-act" title="Eliminar acción">🗑</button></label>`;
      realizadoBox.appendChild(el);
    });
    updateLive();
  }
  function renderDiagnosis(diag, riesgos, notas){
    const diagBox = $('#mto #diag-box'); const engBox = $('#mto #eng-box');
    diagBox.innerHTML='';
    const block=document.createElement('div'); block.className='item';
    const hall = (diag?.hallazgos_probables||[]).join(' · ') || '—';
    const pruebas = (diag?.pruebas_sugeridas||[]).join(' · ') || '—';
    const piezas = (diag?.piezas_posibles||[]).join(' · ') || '—';
    block.innerHTML=`
      <div style="display:flex;flex-direction:column;gap:8px">
        <div><strong>Hipótesis:</strong> ${diag?.hipotesis || '—'}</div>
        <div><strong>Hallazgos probables:</strong> ${hall}</div>
        <div><strong>Pruebas sugeridas:</strong> ${pruebas}</div>
        <div><strong>Piezas posibles:</strong> ${piezas}</div>
        <div><span class="chip">Prioridad: <b>${diag?.prioridad||'—'}</b></span></div>
      </div>`;
    diagBox.appendChild(block);

    if((riesgos||[]).length){
      const r = document.createElement('div'); r.className='item';
      r.innerHTML = `<div><strong>Riesgos de seguridad:</strong></div>`;
      const wrap = document.createElement('div'); wrap.className='chips'; r.appendChild(wrap);
      riesgos.forEach(t=>{ const s=document.createElement('span'); s.className='chip'; s.textContent=t; wrap.appendChild(s); });
      diagBox.appendChild(r);
    }
    if(notas){ const n=document.createElement('div'); n.className='item'; n.innerHTML=`<div><strong>Notas:</strong> ${notas}</div>`; diagBox.appendChild(n); }

    engBox.innerHTML='';
    const plan = diag?.plan_ingenieria || [];
    if(plan.length){
      const wrap=document.createElement('div'); wrap.className='item';
      const ol=document.createElement('ol'); ol.style.margin='0 0 0 18px';
      plan.forEach(p=>{ const li=document.createElement('li'); li.textContent=p; ol.appendChild(li); });
      wrap.appendChild(ol); engBox.appendChild(wrap);
    }
  }

  btnIA?.addEventListener('click', async ()=>{
    const equipo = $('#mto #equipo').value.trim();
    const libre  = $('#mto #nombre_equipo_libre').value.trim();
    if(!equipo && !libre){ alert('Escribe el Equipo (Paso 2) o un texto opcional.'); return; }

    const servicio = $('#mto #servicio').value;
    const sintomas = $('#mto #sintomas').value.trim();
    const marca    = $('#mto #marca').value.trim();
    const modelo   = $('#mto #modelo').value.trim();
    const nserie   = $('#mto #numero_serie').value.trim();
    const obs      = $('#mto #observaciones').value.trim();

    setBtnLoading(true); setOverlay(true); showSkeleton();
    const ctrl = new AbortController(); const t = setTimeout(()=>ctrl.abort(), 45000);
    try{
      const body = libre ? { nombre_equipo: libre, servicio, sintomas }
                         : { equipo, marca, modelo, numero_serie:nserie, observaciones:obs, servicio, sintomas };
      const res = await fetch(`{{ route('ai.checklist') }}`,{
        method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
        body:JSON.stringify(body), signal:ctrl.signal
      });
      if(!res.ok) throw new Error('Network');
      const data = await res.json(); lastAIData = data || null;
      hideSkeleton();
      renderPreventivo(data.secciones||[]); renderRealizado(data.acciones_sugeridas||[]);
      // Diagnóstico + plan (si tu servicio ya los envía, los toma; si no, arma algo básico)
      renderDiagnosis(
        data.diagnostico || {
          hipotesis: data?.equipo?.marca_modelo ? `Revisión para ${data.equipo.marca_modelo}` : null,
          hallazgos_probables: [], pruebas_sugeridas: [], piezas_posibles: [], prioridad:'—',
          plan_ingenieria: data.resumen_ingenieria || []
        },
        data.riesgos_seguridad || [],
        data.notas || null
      );
    }catch(err){
      console.error(err); hideSkeleton();
      preventivoBox.innerHTML = `<div class="item" style="border-color:#fecaca;background:#fff5f5;color:#991b1b">Error generando el checklist. Intenta nuevamente.</div>`;
    }finally{
      clearTimeout(t); setBtnLoading(false); setOverlay(false);
    }
  });

  // acción manual (Enter)
  $('#mto #accion_libre').addEventListener('keydown', e=>{
    if(e.key==='Enter'){
      e.preventDefault();
      const txt=e.target.value.trim(); if(!txt) return;
      const wrap=document.createElement('div'); wrap.className='item';
      wrap.innerHTML=`<label style="display:flex;align-items:center;gap:10px;justify-content:space-between">
        <span><input type="checkbox" name="mto_realizado[]" value="${txt.replaceAll('"','&quot;')}" checked> ${txt}</span>
        <button type="button" class="del del-act" title="Eliminar acción">🗑</button></label>`;
      $('#mto #realizado-box').appendChild(wrap); e.target.value=''; updateLive();
    }
  });
  $('#mto #realizado-box').addEventListener('click', e=>{
    const btn = e.target.closest('.del-act'); if(!btn) return;
    btn.closest('.item')?.remove(); updateLive(); if(current===3) fillConfirm();
  });

  // eliminar ítems preventivo
  $('#mto #preventivo-box').addEventListener('click', e=>{
    const btn = e.target.closest('.del-item'); if(!btn) return;
    const row = btn.closest('.item'); if(!row) return;
    const hasHidden = row.querySelector('input[name^="mto_preventivo"][name$="[item]"]');
    if(!hasHidden) return;
    row.remove(); // reindex
    const rows = [...document.querySelectorAll('#mto #preventivo-box .item')]; let idx=0;
    rows.forEach(el=>{
      const it = el.querySelector('input[name^="mto_preventivo"][name$="[item]"]');
      const sc = el.querySelector('input[name^="mto_preventivo"][name$="[seccion]"]');
      const st = el.querySelector('select[name^="mto_preventivo"][name$="[estatus]"]');
      if(it && sc && st){ it.name=`mto_preventivo[${idx}][item]`; sc.name=`mto_preventivo[${idx}][seccion]`; st.name=`mto_preventivo[${idx}][estatus]`; idx++; }
    });
    updateLive(); if(current===3) fillConfirm();
  });

  /* ===== Confirm (Step 4) ===== */
  function cls(st){
    const s=(st||'').toLowerCase();
    if(s.includes('bueno')) return 'ok';
    if(s.includes('revisado')) return 'rev';
    if(s.includes('ajust')) return 'aj';
    if(s.includes('repar')) return 'rep';
    if(s.includes('reempl')) return 'rpl';
    if(s.includes('no aplica')) return 'na';
    return '';
  }
  function fillConfirm(){
    const val = s => $(s)?.value?.trim() || '';
    const cliente = $('#mto #cliente_search')?.value?.trim() || '';
    $('#mto #pvBadgeCliente').textContent = cliente || 'Cliente';
    $('#mto #c_cliente').textContent = cliente || '—';
    $('#mto #c_fentrada').textContent = val('#mto #fecha_entrada') || '—';
    $('#mto #c_fmanto').textContent = val('#mto #fecha_mantenimiento_input') || '—';
    $('#mto #c_equipo').textContent = val('#mto #equipo') || '(no especificado)';
    $('#mto #c_marca').textContent = val('#mto #marca') || '—';
    $('#mto #c_modelo').textContent = val('#mto #modelo') || '—';
    $('#mto #c_serie').textContent = val('#mto #numero_serie') || '—';
    $('#mto #c_obs').textContent = val('#mto #observaciones') || '—';
    $('#mto #c_prox').textContent = $('#mto #proximo_mantenimiento option:checked')?.textContent || '—';

    const pvFoto = $('#mto #pv_foto');
    if(pvFoto && window.fotoDataURL){ pvFoto.src = window.fotoDataURL; pvFoto.style.display='block'; }

    // checklist
    const tbody = $('#mto #pvChecklist tbody'); tbody.innerHTML='';
    const items = [...document.querySelectorAll('[name^="mto_preventivo"][name$="[item]"]')];
    const get = (i,k)=> document.querySelector(`[name="mto_preventivo[${i}][${k}]"]`)?.value || '';
    let okCount=0;
    items.forEach((it,i)=>{
      const tr=document.createElement('tr'); tr.className='pv-row';
      const est = get(i,'estatus'); const c = cls(est); if(c==='ok') okCount++;
      tr.innerHTML=`<td class="pv-cell"><strong>${it.value}</strong></td>
                    <td class="pv-cell">${get(i,'seccion')}</td>
                    <td class="pv-cell"><span class="pv-chip ${c}">${est}</span></td>`;
      tbody.appendChild(tr);
    });
    $('#mto #pvTotalItems').textContent = items.length;
    $('#mto #pvOkCount').textContent   = okCount;

    // acciones
    const actsWrap = $('#mto #pvAcciones'); actsWrap.innerHTML='';
    const acts = [...document.querySelectorAll('#mto input[name="mto_realizado[]"]:checked')].map(x=>x.value);
    if(acts.length){ acts.forEach(t=>{ const ch=document.createElement('span'); ch.className='chip'; ch.textContent=t; actsWrap.appendChild(ch); }); }
    else { actsWrap.innerHTML='<span class="chip" style="opacity:.7)">Sin acciones seleccionadas</span>'; }

    // diagnóstico en la preview
    const pvHall = $('#mto #pvDiagHallazgos'); pvHall.innerHTML='';
    const pvPrio = $('#mto #pvDiagPrioridad'); pvPrio.textContent = 'Prioridad: —';
    $('#mto #pvDiagHipotesis').textContent = '—';
    $('#mto #pvDiagPruebas').textContent   = '—';
    $('#mto #pvDiagPiezas').textContent    = '—';

    if(lastAIData){
      const d=lastAIData.diagnostico||{};
      (d.hallazgos_probables||[]).forEach(t=>{ const s=document.createElement('span'); s.className='chip'; s.textContent=t; pvHall.appendChild(s); });
      if(d.prioridad) pvPrio.textContent=`Prioridad: ${d.prioridad}`;
      if(d.hipotesis) $('#mto #pvDiagHipotesis').textContent=d.hipotesis;
      if(d.pruebas_sugeridas?.length) $('#mto #pvDiagPruebas').textContent=d.pruebas_sugeridas.join(' · ');
      if(d.piezas_posibles?.length)   $('#mto #pvDiagPiezas').textContent=d.piezas_posibles.join(' · ');
    }

    updateLive();
  }

  /* ===== Resumen lateral ===== */
  function updateLive(){
    $('#mto #r_cliente').textContent = $('#mto #cliente_search').value.trim() || '—';
    $('#mto #r_equipo').textContent  = $('#mto #equipo').value.trim() || '—';
    $('#mto #r_fentrada').textContent= $('#mto #fecha_entrada').value || '—';
    $('#mto #r_fmanto').textContent  = $('#mto #fecha_mantenimiento').value || '{{ now()->format('Y-m-d') }}';
    $('#mto #r_prox').textContent    = $('#mto #proximo_mantenimiento option:checked')?.textContent?.replace(' meses','') || '—';
    const items = [...document.querySelectorAll('#mto [name^="mto_preventivo"][name$="[item]"]')];
    $('#mto #r_items').textContent = items.length;
    const acts = [...document.querySelectorAll('#mto input[name="mto_realizado[]"]:checked')].map(x=>x.value);
    $('#mto #r_acciones').textContent = acts.length;
    const chips = $('#mto #r_chips'); chips.innerHTML='';
    acts.slice(0,6).forEach(t=>{ const s=document.createElement('span'); s.className='chip'; s.textContent=t; chips.appendChild(s); });
    if(acts.length>6){ const more=document.createElement('span'); more.className='chip'; more.textContent=`+${acts.length-6} más`; chips.appendChild(more); }
  }
  ['#cliente_search','#fecha_entrada','#fecha_mantenimiento','#proximo_mantenimiento','#equipo','#marca','#modelo','#numero_serie','#observaciones'].forEach(sel=>{
    const el=$(sel); el?.addEventListener('input',updateLive); el?.addEventListener('change',updateLive);
  });

  // Submit debug
  $('#mto #wizard-form').addEventListener('submit', function(){
    const fd=new FormData(this); console.log('🛠️ Enviando formulario:');
    for (let [k,v] of fd.entries()) console.log(k,'=',v);
  });

  // Init
  show(0);
})();
</script>

<!-- Script de animación (overlay y entrada de ítems) SIN tocar lógica existente -->
<script>
// GSAP overlay animation
window.addEventListener('load', ()=>{
  if(!window.gsap) return;
  try{
    const svg = document.querySelector('#mto #mtoSVG'); if(!svg) return;
    gsap.set(svg, {visibility:'visible'});

    const rings = gsap.utils.toArray('#mto .ell');
    const easeA = CustomEase.create("ea","M0,0 C0.2,0 0.432,0.147 0.507,0.374 0.59,0.629 0.822,1 1,1 ");
    const easeB = CustomEase.create("eb","M0,0 C0.266,0.412 0.297,0.582 0.453,0.775 0.53,0.87 0.78,1 1,1 ");
    const easeC = CustomEase.create("ec","M0,0 C0.594,0.062 0.79,0.698 1,1 ");
    const interp = gsap.utils.interpolate(["#359EEE","#FFC43D","#EF476F","#03CEA4"]);

    function ringAnim(el, i){
      gsap.set(el,{opacity:1-(i/rings.length), stroke:interp(i/rings.length)});
      const tl = gsap.timeline({defaults:{ease:easeA}, repeat:-1}).timeScale(.5);
      tl.to(el,{attr:{ry:`-=${(i+1)*2.3}`, rx:`+=${(i+1)*1.4}`}, ease:easeC})
        .to(el,{attr:{ry:`+=${(i+1)*2.3}`, rx:`-=${(i+1)*1.4}`}, ease:easeB})
        .to(el,{duration:1, rotation:-180, transformOrigin:"50% 50%"}, 0);
    }
    rings.forEach((el, i)=>{ gsap.delayedCall(i/(rings.length-1), ringAnim, [el, i]); });

    gsap.to('#mto #mtoGrad',{duration:4,delay:.75,attr:{x1:"-=300",x2:"-=300"},scale:1.2,transformOrigin:"50% 50%",repeat:-1,ease:"none"});
    gsap.to('#mto #ai',{duration:1,scale:1.08,transformOrigin:"50% 50%",repeat:-1,yoyo:true,ease:easeA, opacity:1});
  }catch(err){ console.error('GSAP init error', err); }
});

// Pop-in animation for newly added checklist/acciones items
(function(){
  const preventivoBox = document.querySelector('#mto #preventivo-box');
  const realizadoBox  = document.querySelector('#mto #realizado-box');
  if(!preventivoBox || !realizadoBox) return;

  const animate = (nodes)=>{
    nodes.forEach(node=>{
      if(node.nodeType===1 && node.classList && node.classList.contains('item')){
        node.classList.add('pop-in');
        void node.offsetWidth;
        setTimeout(()=> node.classList.add('show'), 20);
      }
    });
  };
  const opt = {childList:true};
  new MutationObserver(muts=> muts.forEach(m=> m.addedNodes?.length && animate(m.addedNodes))).observe(preventivoBox, opt);
  new MutationObserver(muts=> muts.forEach(m=> m.addedNodes?.length && animate(m.addedNodes))).observe(realizadoBox, opt);
})();
</script>
@endsection
