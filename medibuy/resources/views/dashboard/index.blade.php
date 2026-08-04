@extends('layouts.app')

@section('content')
@include('partials.submenu-cotizaciones')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<div class="medical-dashboard">
    <style>
        :root{
            --sidebar-w:88 px;
            --bg: #f6f8fc;
            --card: #ffffff;
            --line: #e6ebf2;
            --text: #0f172a;
            --muted: #6b7280;
            --blue: #2563eb;
            --blue-soft: #eaf1ff;
            --green: #10b981;
            --green-soft: #e9fbf4;
            --orange: #f59e0b;
            --orange-soft: #fff5df;
            /* Reemplazo de morado por un celeste/cian profesional (estilo Nexus) */
            --teal: #0ea5e9; 
            --teal-soft: #e0f2fe;
            --red: #ef4444;
            --red-soft: #ffeaea;
            --shadow: 0 10px 30px rgba(15, 23, 42, .06);
            --radius: 22px;
        }

        .medical-dashboard{
            background: var(--bg);
            min-height: 100vh;
            padding: 22px;
            /* Transición suave para evitar saltos bruscos al redimensionar la pantalla */
            transition: margin 0.3s ease, padding 0.3s ease;
        }

        /* --- Espaciado dinámico para empujar el contenido y no solapar el menú --- */
        @media (min-width: 992px) {
            .medical-dashboard {
                margin-left: 110px; /* 24px (left) + 80px (ancho menú) + espacio */
            }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .medical-dashboard {
                margin-left: 95px; /* 16px (left) + 70px (ancho menú) + espacio */
            }
        }
        /* -------------------------------------------------------------------------- */

        .dashboard-shell{
            max-width: 1500px;
            margin: 0 auto;
        }

        .dashboard-header{
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 22px;
        }

        .dashboard-title-wrap{
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .dashboard-icon{
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: linear-gradient(180deg, #eef4ff 0%, #e5efff 100%);
            color: var(--blue);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
        }

        .dashboard-title{
            margin: 0;
            font-size: 2rem;
            line-height: 1.1;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -.02em;
        }

        .dashboard-subtitle{
            margin: 4px 0 0;
            color: var(--muted);
            font-size: .98rem;
        }

        .dashboard-date-chip{
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 10px 14px;
            color: #475569;
            font-weight: 600;
            box-shadow: var(--shadow);
            white-space: nowrap;
        }

        .stats-grid{
            display: grid;
            grid-template-columns: repeat(4, minmax(0,1fr));
            gap: 16px;
            margin-bottom: 22px;
        }

        .stat-card{
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .stat-card:hover{
            transform: translateY(-3px);
            box-shadow: 0 16px 40px rgba(15, 23, 42, .10);
            border-color: #dbe5f3;
        }

        .stat-top{
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .stat-label{
            font-size: .85rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 10px;
        }

        .stat-value{
            font-size: 2rem;
            font-weight: 800;
            color: var(--text);
            line-height: 1;
            margin: 0 0 8px;
        }

        .stat-meta{
            color: var(--muted);
            font-size: .95rem;
        }

        .stat-icon{
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-blue .stat-icon{ background: var(--blue-soft); color: var(--blue); }
        .stat-green .stat-icon{ background: var(--green-soft); color: var(--green); }
        .stat-orange .stat-icon{ background: var(--orange-soft); color: #d97706; }
        .stat-teal .stat-icon{ background: var(--teal-soft); color: var(--teal); }

        .panel-grid{
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 22px;
            margin-bottom: 22px;
        }

        .panel{
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 24px;
            box-shadow: var(--shadow);
            padding: 20px;
            min-height: 390px;
        }

        .panel-header{
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .panel-title{
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text);
            margin: 0;
            letter-spacing: -.02em;
        }

        .panel-link{
            color: var(--blue);
            text-decoration: none;
            font-weight: 700;
            font-size: .95rem;
        }

        .panel-link:hover{
            color: #1d4ed8;
            text-decoration: none;
        }

        .chart-wrap{
            position: relative;
            height: 280px;
            margin-top: 12px;
        }

        .legend-list{
            display: flex;
            flex-wrap: wrap;
            gap: 12px 18px;
            margin-top: 18px;
        }

        .legend-item{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            font-weight: 600;
            font-size: .94rem;
        }

        .legend-dot{
            width: 11px;
            height: 11px;
            border-radius: 999px;
            display: inline-block;
        }

        .rentals-list{
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 8px;
        }

        .rental-row{
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 14px;
            border: 1px solid #edf2f7;
            border-radius: 18px;
            background: linear-gradient(180deg, #fff 0%, #fbfdff 100%);
            transition: all .18s ease;
        }

        .rental-row:hover{
            border-color: #dce7f7;
            transform: translateY(-2px);
        }

        .rental-main{
            min-width: 0;
        }

        .rental-name{
            font-weight: 800;
            color: var(--text);
            margin-bottom: 3px;
            font-size: 1rem;
            text-transform: capitalize;
        }

        .rental-meta{
            color: var(--muted);
            font-size: .95rem;
        }

        .badge-status{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 104px;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: .87rem;
            font-weight: 800;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .badge-programada{
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #dbeafe;
        }

        .badge-en-curso{
            background: #fff7ed;
            color: #d97706;
            border-color: #fed7aa;
        }

        .badge-finalizada{
            background: #ecfdf5;
            color: #059669;
            border-color: #bbf7d0;
        }

        .badge-cancelada{
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .empty-state{
            min-height: 260px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--muted);
            padding: 30px;
            border: 1px dashed #d9e2ef;
            border-radius: 20px;
            background: #fbfdff;
        }

        .quick-grid{
            display: grid;
            grid-template-columns: repeat(4, minmax(0,1fr));
            gap: 16px;
        }

        .quick-card{
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 18px 20px;
            text-decoration: none;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 14px;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, opacity .18s ease;
        }

        .quick-card:hover{
            transform: translateY(-3px);
            box-shadow: 0 16px 40px rgba(15, 23, 42, .10);
            border-color: #dbe5f3;
            text-decoration: none;
            color: var(--text);
        }

        .quick-card.is-disabled{
            opacity: .55;
            cursor: not-allowed;
            pointer-events: none;
        }

        .quick-icon{
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .quick-label{
            font-size: 1.05rem;
            font-weight: 800;
        }

        .quick-blue .quick-icon{ background: var(--blue-soft); color: var(--blue); }
        .quick-green .quick-icon{ background: var(--green-soft); color: var(--green); }
        .quick-orange .quick-icon{ background: var(--orange-soft); color: #d97706; }
        .quick-teal .quick-icon{ background: var(--teal-soft); color: var(--teal); }

        @media (max-width: 1200px){
            .stats-grid,
            .quick-grid{
                grid-template-columns: repeat(2, minmax(0,1fr));
            }

            .panel-grid{
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px){
            .medical-dashboard{
                padding: 14px;
                /* En móvil damos espacio arriba para que la píldora flotante no tape el contenido */
                padding-top: 100px; 
                margin-left: 0;
            }

            .dashboard-header{
                flex-direction: column;
                align-items: flex-start;
            }

            .stats-grid,
            .quick-grid{
                grid-template-columns: 1fr;
            }

            .dashboard-title{
                font-size: 1.6rem;
            }

            .panel{
                min-height: auto;
            }

            .rental-row{
                flex-direction: column;
                align-items: flex-start;
            }

            .badge-status{
                min-width: auto;
            }
        }
    </style>

    @php
        $rentalsIndexUrl = \Illuminate\Support\Facades\Route::has('rentals.index') ? route('rentals.index') : '#';
        $rentalsCreateUrl = \Illuminate\Support\Facades\Route::has('rentals.create') ? route('rentals.create') : $rentalsIndexUrl;
        $equipmentsCreateUrl = \Illuminate\Support\Facades\Route::has('equipments.create')
            ? route('equipments.create')
            : (\Illuminate\Support\Facades\Route::has('equipments.index') ? route('equipments.index') : '#');
        $logisticsIndexUrl = \Illuminate\Support\Facades\Route::has('logistics.index') ? route('logistics.index') : '#';

        $hasRentalsIndex = \Illuminate\Support\Facades\Route::has('rentals.index');
        $hasRentalsCreate = \Illuminate\Support\Facades\Route::has('rentals.create') || \Illuminate\Support\Facades\Route::has('rentals.index');
        $hasEquipments = \Illuminate\Support\Facades\Route::has('equipments.create') || \Illuminate\Support\Facades\Route::has('equipments.index');
        $hasLogistics = \Illuminate\Support\Facades\Route::has('logistics.index');
    @endphp

    <div class="dashboard-shell">
        <div class="dashboard-header">
            <div class="dashboard-title-wrap">
                <div class="dashboard-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                        <path d="M9 3H5a2 2 0 0 0-2 2v4h6V3Zm12 0h-8v6h8V5a2 2 0 0 0-2-2ZM3 13v6a2 2 0 0 0 2 2h4v-8H3Zm10 8h6a2 2 0 0 0 2-2v-6h-8v8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <div>
                    <h1 class="dashboard-title">Dashboard</h1>
                    <p class="dashboard-subtitle">Vista general del sistema de rentas de equipos médicos</p>
                </div>
            </div>

            <div class="dashboard-date-chip">
                {{ now()->translatedFormat('d \d\e F, Y') }}
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card stat-blue">
                <div class="stat-top">
                    <div>
                        <div class="stat-label">Equipos Totales</div>
                        <div class="stat-value">{{ number_format($totalEquipments ?? 0) }}</div>
                        <div class="stat-meta">{{ number_format($availableEquipments ?? 0) }} disponibles</div>
                    </div>
                    <div class="stat-icon">
                        <svg width="23" height="23" viewBox="0 0 24 24" fill="none">
                            <path d="m12 3 7 4v10l-7 4-7-4V7l7-4Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="m12 12 7-5M12 12 5 7m7 5v9" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card stat-blue">
                <div class="stat-top">
                    <div>
                        <div class="stat-label">Rentas Activas</div>
                        <div class="stat-value">{{ number_format($activeRentals ?? 0) }}</div>
                        <div class="stat-meta">Programadas y en curso</div>
                    </div>
                    <div class="stat-icon">
                        <svg width="23" height="23" viewBox="0 0 24 24" fill="none">
                            <path d="M8 3h8M9 3v2m6-2v2M5 7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M8 11h8M8 15h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card stat-teal">
                <div class="stat-top">
                    <div>
                        <div class="stat-label">Clientes</div>
                        <div class="stat-value">{{ number_format($totalClients ?? 0) }}</div>
                        <div class="stat-meta">Hospitales y clientes registrados</div>
                    </div>
                    <div class="stat-icon">
                        <svg width="23" height="23" viewBox="0 0 24 24" fill="none">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.8"/>
                            <circle cx="9.5" cy="7" r="3.5" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card stat-orange">
                <div class="stat-top">
                    <div>
                        <div class="stat-label">Por Cobrar</div>
                        <div class="stat-value">${{ number_format($pendingAmount ?? 0, 2) }}</div>
                        <div class="stat-meta">{{ number_format($pendingInvoices ?? 0) }} facturas pendientes</div>
                    </div>
                    <div class="stat-icon">
                        <svg width="23" height="23" viewBox="0 0 24 24" fill="none">
                            <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7H14.5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-grid">
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Estado de Equipos</h2>
                </div>

                <div class="chart-wrap">
                    <canvas id="equipmentStatusChart"></canvas>
                </div>

                <div class="legend-list">
                    @php
                        $legendColors = ['#2563eb', '#10b981', '#f59e0b', '#ef4444'];
                    @endphp

                    @foreach(($equipmentStatus ?? []) as $index => $status)
                        <div class="legend-item">
                            <span class="legend-dot" style="background: {{ $legendColors[$index] ?? '#64748b' }}"></span>
                            <span>{{ $status['label'] ?? 'N/D' }}: {{ $status['value'] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Rentas Recientes</h2>
                    @if($hasRentalsIndex)
                        <a href="{{ $rentalsIndexUrl }}" class="panel-link">Ver todas →</a>
                    @endif
                </div>

                @if(isset($recentRentals) && $recentRentals->count())
                    <div class="rentals-list">
                        @foreach($recentRentals as $rental)
                            @php
                                $statusClass = match($rental->status ?? '') {
                                    'Programada' => 'badge-programada',
                                    'En curso' => 'badge-en-curso',
                                    'Finalizada' => 'badge-finalizada',
                                    'Cancelada' => 'badge-cancelada',
                                    default => 'badge-programada',
                                };
                            @endphp

                            <div class="rental-row">
                                <div class="rental-main">
                                    <div class="rental-name">{{ $rental->client_name ?? 'Sin cliente' }}</div>
                                    <div class="rental-meta">
                                        {{ $rental->service_type ?? 'Sin tipo de servicio' }}
                                        @if(!empty($rental->start_date))
                                            · {{ \Carbon\Carbon::parse($rental->start_date)->format('Y-m-d') }}
                                        @endif
                                    </div>
                                </div>

                                <span class="badge-status {{ $statusClass }}">
                                    {{ $rental->status ?? 'Programada' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div>
                            <div style="font-weight:800; color:#0f172a; margin-bottom:6px;">Sin rentas recientes</div>
                            <div>Todavía no hay registros para mostrar.</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="quick-grid">
            <a href="{{ $rentalsCreateUrl }}"
               class="quick-card quick-blue {{ $hasRentalsCreate ? '' : 'is-disabled' }}"
               @if(!$hasRentalsCreate) aria-disabled="true" @endif>
                <span class="quick-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <path d="M8 3h8M9 3v2m6-2v2M5 7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7Z" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M12 10v6M9 13h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="quick-label">Nueva Renta</span>
            </a>

            <a href="{{ $equipmentsCreateUrl }}"
               class="quick-card quick-green {{ $hasEquipments ? '' : 'is-disabled' }}"
               @if(!$hasEquipments) aria-disabled="true" @endif>
                <span class="quick-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <path d="m12 3 7 4v10l-7 4-7-4V7l7-4Z" stroke="currentColor" stroke-width="1.8"/>
                        <path d="m12 12 7-5M12 12 5 7m7 5v9" stroke="currentColor" stroke-width="1.8"/>
                    </svg>
                </span>
                <span class="quick-label">Agregar Equipo</span>
            </a>

            <a href="{{ $logisticsIndexUrl }}"
               class="quick-card quick-orange {{ $hasLogistics ? '' : 'is-disabled' }}"
               @if(!$hasLogistics) aria-disabled="true" @endif>
                <span class="quick-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <path d="M3 7h11v9H3V7Zm11 3h3l4 3v3h-7v-6Z" stroke="currentColor" stroke-width="1.8"/>
                        <circle cx="7.5" cy="18.5" r="1.5" stroke="currentColor" stroke-width="1.8"/>
                        <circle cx="18.5" cy="18.5" r="1.5" stroke="currentColor" stroke-width="1.8"/>
                    </svg>
                </span>
                <span class="quick-label">Logística</span>
            </a>

            <a href="{{ $rentalsIndexUrl }}"
               class="quick-card quick-teal {{ $hasRentalsIndex ? '' : 'is-disabled' }}"
               @if(!$hasRentalsIndex) aria-disabled="true" @endif>
                <span class="quick-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M16 3v4M8 3v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="quick-label">Calendario</span>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (() => {
        const canvas = document.getElementById('equipmentStatusChart');
        if (!canvas) return;

        const labels = @json(collect($equipmentStatus ?? [])->pluck('label')->values());
        const values = @json(collect($equipmentStatus ?? [])->pluck('value')->values());

        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        titleFont: {
                            weight: '700'
                        },
                        bodyFont: {
                            weight: '600'
                        }
                    }
                }
            }
        });
    })();
</script>
@endsection