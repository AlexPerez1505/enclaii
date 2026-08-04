
<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\GuiaController;
use App\Http\Controllers\EntregaGuiaController;
use App\Http\Controllers\ProcesoEquipoController;
use Illuminate\Support\Facades\Route;
use App\Models\Cotizacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Http\Controllers\CamionetaController;
use App\Http\Controllers\FichaTecnicaController;
use App\Http\Controllers\SolicitudMaterialController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\MovimientoController;
use App\Models\User;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\AgendaEventController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ValoracionController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\RemisionController;
use App\Models\Cliente;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CartaGarantiaController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\PropuestaController;
use App\Http\Controllers\RecepcionController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\AparatoController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemParteController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ChecklistFirmaController;
use App\Http\Controllers\IncidenteController;
use App\Http\Controllers\EvidenciaController;
use App\Http\Controllers\WhatsappPromotionController;
use App\Http\Controllers\WhatsappInboxController;
use App\Http\Controllers\WhatsappWebhookController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\NotificacionPagoController;
use App\Http\Controllers\EnvioGastoController;
use App\Http\Controllers\OrdenController;
// routes/web.php
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\AiChecklistController;
use App\Http\Controllers\AiClienteController;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationsController; // 👈 ESTE FALTABA
use App\Http\Controllers\CronPublicController;
use App\Http\Controllers\PublicRemisionPdfController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryCategoryController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\AiTicketController;
use App\Http\Controllers\TicketChecklistController;

use App\Http\Controllers\ComponentController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ComplianceLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BentoController;
use App\Http\Controllers\ImportProductosController;

// Rutas de autenticación (sin middleware 'auth')
Route::redirect('/', '/login');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('Login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/inventario/servicio', [ServicioController::class, 'index'])
        ->name('inventario.servicio.index');
// Rutas para recuperación de contraseña (sin middleware 'auth')
Route::get('/reset-password/{token}', function ($token) {
    return view('reset_password', ['token' => $token]);
})->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Agrupar todas las demás rutas dentro de 'auth'
Route::middleware(['auth'])->group(function () {

Route::get('/clientes/{cliente}/seguimientos', [SeguimientoController::class, 'index'])->name('seguimientos.index');
    Route::get('/inventario', [RegistroController::class, 'mostrarProductos'])->name('inventario');
    Route::post('/registro/guardar', [RegistroController::class, 'guardarRegistro'])->name('registro.guardar');
    Route::get('/obtener-detalles/{id}', [RegistroController::class, 'obtenerDetalles']);

    Route::get('/auth/change-password', [UserController::class, 'showChangePasswordForm'])->name('auth.change-password');
    Route::post('/auth/change-password', [UserController::class, 'updatePassword'])->name('auth.update-password');

    Route::get('/buscar-registros', [CotizacionController::class, 'buscarRegistros']);

    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');


    Route::get('/remisiones', function () { return view('remisiones'); });




    Route::get('/cotizaciones', [ProductoController::class, 'index'])->name('cotizaciones.index');

    Route::get('/productos/buscar', [ProductoController::class, 'buscar']);
    Route::get('/generar-cotizacion/{id}', [CotizacionController::class, 'generarCotizacion'])->name('generarCotizacion');
    Route::post('/guardar-cotizacion', [CotizacionController::class, 'guardarCotizacion']);
    
    Route::get('/cotizacion/pdf', [CotizacionController::class, 'generarCotizacionPDF'])->name('cotizacion.pdf');
    Route::post('/generar-cotizacion-pdf', [CotizacionController::class, 'generarCotizacionPDF']);

    Route::get('/cotizacion/prueba', [CotizacionController::class, 'generarPDF'])->name('cotizacion.prueba');
    Route::post('/guardar-cotizacion', [CotizacionController::class, 'store']);

    Route::get('/descargar-cotizacion/{id}', [CotizacionController::class, 'descargarPDF']);
    Route::get('/productos/buscar', [ProductoController::class, 'search'])->name('productos.search');

    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');
    Route::put('/perfil/actualizar', [PerfilController::class, 'update'])->name('perfil.update');
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::post('/perfil/foto', [PerfilController::class, 'updatePhoto'])->name('perfil.updatePhoto');

    Route::get('/usuarios', [PerfilController::class, 'allUsers']);

    Route::get('/historial-cotizaciones', function () {
        $cotizaciones = Cotizacion::all();
        return view('historial', compact('cotizaciones'));
    })->name('historial-cotizaciones');

    Route::get('/cotizaciones/descargarPDF/{id}', [CotizacionController::class, 'descargarPDF'])->name('cotizaciones.descargarPDF');

 Route::controller(EventoController::class)->group(function () {
    // API de agenda
    Route::get('/eventos',        'index')->name('eventos.index');
    Route::post('/eventos',       'store')->name('eventos.store');
    Route::get('/evento/{id}',    'show')->name('eventos.show');
    Route::put('/eventos/{id}',   'update')->name('eventos.update');
    Route::delete('/eventos/{id}','destroy')->name('eventos.destroy');

    // <<< NUEVO: endpoint para invitados
    Route::get('/agenda/usuarios','usuarios')->name('agenda.users');
});

    Route::get('/servicio', function () { return view('servicio'); });
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/agregar', function () { return view('agregar'); })->name('users.create');

    // Rutas para asistencias
    Route::get('/asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index');
    Route::post('/asistencias', [AsistenciaController::class, 'store'])->name('asistencias.store');
    Route::get('/asistencias/quincena', [AsistenciaController::class, 'obtenerAsistenciasQuincena'])->name('asistencias.quincena');

    Route::get('/paquetes', [PaqueteController::class, 'index']);
    Route::post('/paquetes', [PaqueteController::class, 'store']);
    Route::get('/cotizaciones', [PaqueteController::class, 'index'])->name('cotizaciones');
    Route::get('/paquete/{id}/productos', [PaqueteController::class, 'getProductosDePaquete']);
    Route::get('/paquetes/search', [PaqueteController::class, 'search'])->name('paquetes.search');
    Route::get('/paquetes', [PaqueteController::class, 'index'])->name('paquetes.index');
Route::get('/paquetes/create', [PaqueteController::class, 'create'])->name('paquetes.create');



    
});
Route::get('/inventario/buscar', [RegistroController::class, 'vistaBuscar'])->name('inventario.buscar');
Route::get('/inventario/buscar/resultado', [RegistroController::class, 'buscarSubmit'])->name('inventario.buscar.submit');
// Ruta para mostrar el detalle de un equipo/registro por su ID
Route::get('/inventario/{id}', [App\Http\Controllers\RegistroController::class, 'mostrarProductoDetalle'])->name('inventario.detalle');
Route::get('/inventario', [RegistroController::class, 'mostrarProductos'])->name('inventario');
Route::get('/inventario/main', [RegistroController::class, 'mostrarProductos'])->name('inventario.main');
Route::get('/registros/{id}/imprimir-barcode', [\App\Http\Controllers\RegistroController::class, 'imprimirBarcode'])
    ->name('registros.imprimirBarcode');


Route::get('/procesos/{id}', [ProcesoEquipoController::class, 'mostrarProceso'])->name('procesos.mostrar');
Route::post('/procesos/{id}/guardar', [ProcesoEquipoController::class, 'guardarProceso'])->name('procesos.guardar');

Route::get('/procesos/{id}/hojalateria', function ($id) {
    return view('procesos.hojalateria', ['id' => $id]);
})->name('proceso.hojalateria');
Route::get('/procesos/{id}/vendido', function ($id) {
    return view('procesos.vendido', ['id' => $id]);
})->name('proceso.vendido');

Route::get('/procesos/{id}/mantenimiento', function ($id) {
    return view('procesos.mantenimiento', ['id' => $id]);
})->name('proceso.mantenimiento');

Route::get('/procesos/{id}/stock', function ($id) {
    return view('procesos.stock', ['id' => $id]);
})->name('proceso.stock');

Route::get('/procesos/{id}/defectuoso', function ($id) {
    return view('procesos.defectuoso', ['id' => $id]);
})->name('proceso.defectuoso');
Route::post('/producto/{id}/defectuoso', [ProductoController::class, 'marcarComoDefectuoso']);
Route::get('/procesos/{id}/completados', [ProcesoEquipoController::class, 'obtenerProcesosCompletados']);
Route::get('/camionetas/agregar', [CamionetaController::class, 'create'])->name('camionetas.create');
Route::post('/camionetas/store', [CamionetaController::class, 'store'])->name('camionetas.store');

Route::post('/camionetas', [CamionetaController::class, 'store'])->name('camionetas.store');
Route::get('/camionetas/{camioneta}/editar', [CamionetaController::class, 'edit'])->name('camionetas.edit');
Route::put('/camionetas/{camioneta}', [CamionetaController::class, 'update'])->name('camionetas.update');
Route::delete('/camionetas/{camioneta}', [CamionetaController::class, 'destroy'])->name('camionetas.destroy');
// Ruta para mostrar los detalles de la camioneta
Route::get('/camionetas/{id}', [CamionetaController::class, 'show'])->name('camionetas.show');
Route::get('/camionetas', [CamionetaController::class, 'index'])->name('camionetas.index');
Route::put('/camionetas/{id}', [CamionetaController::class, 'update'])->name('camionetas.update');


Route::resource('fichas', FichaTecnicaController::class);
Route::get('fichas/{ficha}/download', [FichaTecnicaController::class, 'download'])->name('fichas.download');
Route::get('procesos/{id}/stock', [ProcesoEquipoController::class, 'stock'])->name('stock');

Route::get('/solicitudes', [SolicitudMaterialController::class, 'index'])->name('solicitudes.index');
Route::get('/solicitudes/crear', [SolicitudMaterialController::class, 'create'])->name('solicitudes.create');
Route::post('/solicitudes', [SolicitudMaterialController::class, 'store'])->name('solicitudes.store');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/solicitudes', [SolicitudMaterialController::class, 'pendientes'])->name('solicitudes.admin');
    Route::put('/solicitudes/{solicitud}/planta', [SolicitudMaterialController::class, 'marcarComoEnPlanta'])->name('solicitudes.marcarEnPlanta');
    Route::put('/solicitudes/{solicitud}/entregar', [SolicitudMaterialController::class, 'entregar'])->name('solicitudes.entregar');
    Route::put('/solicitudes/{solicitud}/rechazar', [SolicitudMaterialController::class, 'rechazar'])->name('solicitudes.rechazar');
    Route::get('/mis-solicitudes/ajax', [SolicitudMaterialController::class, 'misSolicitudesAjax'])->name('solicitudes.ajax');
    Route::get('/solicitudes/listado', [SolicitudMaterialController::class, 'listado'])->name('solicitudes.listado');
    Route::get('/registros/buscar-serie', [RegistroController::class, 'buscarSerie']) ->name('registros.buscar-serie');
    
});

Route::get('/eventos/usuarios', [EventoController::class, 'usuarios']);


Route::post('/servicio', [ServicioController::class, 'store'])->name('servicio.store');

Route::get('/detalles/{id}', [ServicioController::class, 'detalles'])->name('detalles.equipo');


// Mostrar formulario de cada tipo de movimiento
Route::get('/movimientos/salida-mantenimiento/{id}', [MovimientoController::class, 'salidaMantenimiento'])->name('movimientos.salidaMantenimiento');
Route::get('/movimientos/entrada-mantenimiento/{id}', [MovimientoController::class, 'entradaMantenimiento'])->name('movimientos.entradaMantenimiento');
Route::get('/movimientos/salida-dueno/{id}', [MovimientoController::class, 'salidaDueno'])->name('movimientos.salidaDueno');
Route::get('/movimientos/entrada-dueno/{id}', [MovimientoController::class, 'entradaDueno'])->name('movimientos.entradaDueno');

// Guardar movimiento (envío del formulario)
Route::post('/movimientos/guardar/{id}', [MovimientoController::class, 'guardar'])->name('movimientos.guardar');


Route::get('/publicaciones', [PublicacionController::class, 'index'])->name('publicaciones.index');
Route::get('/publicaciones/crear', [PublicacionController::class, 'create'])->name('publicaciones.create');
Route::post('/publicaciones', [PublicacionController::class, 'store'])->name('publicaciones.store');
Route::post('/publicaciones/{id}/like', [PublicacionController::class, 'like'])->name('publicaciones.like');
Route::get('/publicaciones/fetch', [PublicacionController::class, 'fetchPublicaciones'])->name('publicaciones.fetch');
Route::get('/publicaciones/ultima-actualizacion', [PublicacionController::class, 'ultimaActualizacion'])->name('publicaciones.ultimaActualizacion');
Route::get('/publicaciones/{id}', [PublicacionController::class, 'show'])->name('publicaciones.show');

Route::post('/valorar', [ValoracionController::class, 'guardarValoracion'])->name('valorar')->middleware('auth');
Route::get('/cotizaciones', [CotizacionController::class, 'mostrarFormulario']);

// Mostrar un registro específico (GET para llenar el formulario)
Route::get('/registro/{id}', [App\Http\Controllers\RegistroController::class, 'mostrarRegistro'])->name('registro.mostrar');

// Actualizar un registro (PUT con AJAX)
Route::put('/registro/{id}', [App\Http\Controllers\RegistroController::class, 'actualizarRegistro'])->name('registro.actualizar');

Route::delete('/registro/{id}', [RegistroController::class, 'eliminarRegistro'])->name('registro.eliminar');

Route::resource('remisions', RemisionController::class);
Route::get('remisions/{remision}/descargar-pdf', [RemisionController::class, 'descargarPdf'])->name('remisions.descargarPdf');
Route::get('/validar-telefono', function (\Illuminate\Http\Request $request) {
    $valor = preg_replace('/\D/', '', $request->valor);
    $existe = Cliente::where('telefono', $valor)->exists();
    return response()->json(['existe' => $existe]);
});

Route::get('/validar-email', function (\Illuminate\Http\Request $request) {
    $valor = $request->valor;
    $existe = Cliente::where('email', $valor)->exists();
    return response()->json(['existe' => $existe]);
});

// Ver historial de asistencias de un usuario
Route::get('/asistencias/historial', [AsistenciaController::class, 'verHistorial'])->name('asistencias.historial');
Route::get('/asistencias/quincena', [AsistenciaController::class, 'obtenerAsistenciasQuincena'])->name('asistencias.quincena');
Route::get('/asistencia/verificar', [App\Http\Controllers\AsistenciaController::class, 'verificarAsistencia'])->name('asistencia.verificar');
Route::get('/mi-historial', [AsistenciaController::class, 'miHistorial'])
    ->name('mi-historial')
    ->middleware('auth');


Route::get('/cuentas-por-cobrar', [RemisionController::class, 'cuentasPorCobrar'])->name('remisions.cuentasPorCobrar');
Route::put('/pagos_financiamiento/{id}/marcar-pagado', [VentaController::class, 'marcarPagado'])->name('pagos.marcarPagado');
Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
Route::get('/pagos/{pago}/recibo', [VentaController::class, 'reciboPago'])->name('pagos.recibo');
Route::get('/pagos/{item}', [PagoController::class, 'index'])->name('pagos.index');
Route::get('/pagos/{item}/recibo', [PagoController::class, 'generarPDF'])->name('pagos.recibo.pdf');
Route::get('/pagos/generar-pdf', [PagoController::class, 'generarPDF'])->name('pagos.generarPDF');
Route::middleware('auth')->group(function () {
    Route::get('/ventas/crear', [VentaController::class, 'create'])->name('ventas.create');
    Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
    Route::get('/ventas/deudores', [VentaController::class, 'deudores'])->name('ventas.deudores');
    // Nueva ruta para PDF
    Route::get('/ventas/{venta}/pdf', [VentaController::class, 'pdf'])->name('ventas.pdf');
});
Route::middleware('web')->get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');

Route::get('ventas/{venta}/edit', [VentaController::class, 'edit'])->name('ventas.edit');
Route::put('ventas/{venta}', [VentaController::class, 'update'])->name('ventas.update');
Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
Route::get('/api/ventas', [VentaController::class, 'apiVentas']);
Route::get('/carta-garantia', [CartaGarantiaController::class, 'index'])->name('carta.index');
Route::get('/carta-garantia/create', [CartaGarantiaController::class, 'create'])->name('carta.create');
Route::post('/carta-garantia', [CartaGarantiaController::class, 'store'])->name('carta.store');
Route::get('/carta-garantia/{id}/descargar', [CartaGarantiaController::class, 'descargar'])->name('carta.descargar');
Route::delete('/carta-garantia/{id}', [CartaGarantiaController::class, 'destroy'])->name('carta.destroy');
Route::get('/pagos/seguimiento/{item_id}', [PagoController::class, 'seguimientoInteligente'])->name('pagos.seguimiento');
Route::get('/ventas/{venta}/pagos', [VentaController::class, 'indexPagos'])->name('ventas.pagos.index');
Route::post('/ventas/{venta}/pagos', [VentaController::class, 'storePago'])->name('ventas.pagos.store');



Route::get('/verificar-recibo', [ReciboController::class, 'formularioVerificacion'])->name('recibo.verificar');
Route::post('/verificar-recibo', [ReciboController::class, 'verificar'])->name('recibo.verificar.post');

Route::get('/venta/{venta}/recibo-final', [VentaController::class, 'reciboFinal'])->name('venta.recibo.final');
// --- NOTIFICACIONES ---
Route::middleware(['auth'])->group(function () {
    Route::post(
        '/financiamientos/notificar/{pago}',
        [NotificacionPagoController::class, 'reenviar']
    )->name('financiamientos.notificar');
});
// Ruta para importar productos
Route::middleware(['auth'])->group(function () {
    Route::get('/import-productos', [ImportProductosController::class, 'showImportForm'])->name('import.productos.form');
    Route::post('/import-productos', [ImportProductosController::class, 'import'])->name('import.productos.procesar');
    Route::get('/import-plantilla', [ImportProductosController::class, 'downloadTemplate'])->name('import.plantilla');
});

Route::get('/buscar-clientes', [VentaController::class, 'buscarClientes'])->name('clientes.buscar');

// Seguimientos

Route::post('/clientes/{cliente}/seguimientos', [SeguimientoController::class, 'store'])->name('seguimientos.store');
Route::delete('/seguimientos/{id}', [SeguimientoController::class, 'destroy'])->name('seguimientos.destroy');

Route::get('/encontrar-clientes', [PropuestaController::class, 'encontrarClientes'])->name('clientes.encontrar');
Route::get('/buscar-clientes', [VentaController::class, 'buscarClientes'])->name('clientes.buscar');


Route::get('/whatsapp/enviar/{venta}', [App\Http\Controllers\WhatsAppController::class, 'enviarRecordatorio']);
Route::patch('/seguimientos/{id}/completar', [SeguimientoController::class, 'completar'])->name('seguimientos.completar');

Route::get('/cuentas/crear', [CuentaController::class, 'create'])->name('cuentas.create');
Route::post('/cuentas', [CuentaController::class, 'store'])->name('cuentas.store');
Route::get('/cuentas', [CuentaController::class, 'index'])->name('cuentas.index');
Route::delete('/cuentas/{id}', [CuentaController::class, 'destroy'])->name('cuentas.destroy');
Route::post('/cuentas/exportar-pdf', [CuentaController::class, 'exportarPdf'])->name('cuentas.exportar.pdf');
Route::get('/cuentas/{cuenta}/edit', [CuentaController::class, 'edit'])->name('cuentas.edit');
Route::put('/cuentas/{cuenta}', [CuentaController::class, 'update'])->name('cuentas.update');

// Mostrar listado de propuestas
Route::get('propuestas', [PropuestaController::class, 'index'])->name('propuestas.index');

// Formulario para crear nueva propuesta
Route::get('propuestas/create', [PropuestaController::class, 'create'])->name('propuestas.create');

// Guardar nueva propuesta
Route::post('propuestas', [PropuestaController::class, 'store'])->name('propuestas.store');

// Ver detalles de una propuesta
Route::get('propuestas/{propuesta}', [PropuestaController::class, 'show'])->name('propuestas.show');

// Formulario para editar propuesta
Route::get('propuestas/{propuesta}/edit', [PropuestaController::class, 'edit'])->name('propuestas.edit');


// Actualizar propuesta
Route::put('propuestas/{propuesta}', [PropuestaController::class, 'update'])->name('propuestas.update');
Route::patch('propuestas/{propuesta}', [PropuestaController::class, 'update']);

// Eliminar propuesta
Route::delete('propuestas/{propuesta}', [PropuestaController::class, 'destroy'])->name('propuestas.destroy');

// Generar PDF de la propuesta
Route::get('propuestas/{propuesta}/pdf', [PropuestaController::class, 'pdf'])->name('propuestas.pdf');
Route::get('/recepciones/create', [RecepcionController::class, 'create'])->name('recepciones.create');
Route::post('/recepciones', [RecepcionController::class, 'store'])->name('recepciones.store');

Route::get('/recepciones', [RecepcionController::class, 'index'])->name('recepciones.index');
Route::get('/pedidos/create', [PedidoController::class, 'create'])->name('pedidos.create');
Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
Route::get('/recepciones/create/{pedido}', [RecepcionController::class, 'createDesdePedido'])->name('recepciones.createDesdePedido');
Route::post('/recepciones/store', [RecepcionController::class, 'storeDesdePedido'])->name('recepciones.storeDesdePedido');
Route::get('/recepciones/timeline', [RecepcionController::class, 'showHistorialGlobal'])->name('recepciones.timeline.global');
Route::get('/reporte-pedidos', [\App\Http\Controllers\PedidoController::class, 'formReporte'])->name('pedidos.reporte.form');
Route::get('/recepciones/pdf', [RecepcionController::class, 'exportarPDF'])->name('recepciones.timeline.pdf');


use App\Models\ModuloUso;
use Illuminate\Support\Facades\Auth;


Route::get('/home', function () {
    $tusAccesos = collect([
        '/publicaciones',
        '/inventario',
        '/ventas',
        '/propuestas',
        '/remisions',
        '/ventas/deudores',
        '/clientes',
        '/agenda',
        '/camionetas',
        '/perfil',
        '/fichas',
        '/carta-garantia',
        '/solicitudes/crear',
        '/cuentas',
        '/prestamos',
        '/usuarios',
        '/pedidos',
        route('asistencias.index', [], false),
    ]);

    $modulosRecientes = ModuloUso::where('user_id', Auth::id())
        ->where('updated_at', '>=', now()->subHours(12))
        ->orderByDesc('updated_at')
        ->get()
        ->filter(function ($modulo) use ($tusAccesos) {
            return !$tusAccesos->contains(url($modulo->ruta));
        })
        ->take(6);

    return view('home', compact('modulosRecientes'));
})->middleware('auth'); // ← ESTE ES EL CAMBIO



// Clientes
Route::post('/clientes/check_unique', [ClienteController::class, 'checkUnique'])->name('clientes.check_unique');
Route::get('/clientes', [ClienteController::class, 'getClients'])->name('clientes.get');
Route::get('/clientes/vista', [ClienteController::class, 'index'])->name('clientes.index');
Route::post('/clientes/update-asesor', [ClienteController::class, 'updateAsesor'])->name('clientes.updateAsesor');
Route::get('/clientes', [VentaController::class, 'clientes'])->name('clientes.search');
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create'); // NUEVA RUTA
Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');        // NUEVA RUTA
Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
Route::put('/clientes/{id}', [ClienteController::class, 'update'])->name('clientes.update');
Route::post('/clientes/update-asesor', [ClienteController::class, 'updateAsesor']);
// Categorías
Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
Route::get('/categorias/create', [CategoriaController::class, 'create'])->name('categorias.create');
Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
Route::post('/clientes/check-unique', [ClienteController::class, 'checkUnique'])->name('clientes.check-unique');

Route::get('/registros-disponibles', [RegistroController::class, 'registrosStock']);
Route::get('/registro-info/{id}', [RegistroController::class, 'info']);


Route::put('/ventas/{venta}/pagos-financiamiento', [VentaController::class, 'updatePagosFinanciamiento'])
    ->name('ventas.pagosFinanciamiento.update');



Route::get('registros/{id}/imprimir-barcode', [App\Http\Controllers\RegistroController::class, 'imprimirBarcode'])->name('registros.imprimir-barcode');


// GET: Mostrar el formulario de recepción hospitalaria
Route::get('/recepcion-hospital/{checklist}', [App\Http\Controllers\ChecklistController::class, 'recepcionHospital'])->name('recepcion-hospital');

// POST: Guardar la recepción hospitalaria
Route::post('/recepcion-hospital/{checklist}', [App\Http\Controllers\ChecklistController::class, 'guardarRecepcionHospital'])->name('recepcion-hospital.guardar');

Route::get('/checklists/{checklist}/descargar-pdf', [App\Http\Controllers\ChecklistController::class, 'descargarPdf'])->name('checklists.descargar-pdf');

Route::get('registros/{id}/imprimir-barcode', [App\Http\Controllers\RegistroController::class, 'imprimirBarcode'])->name('registros.imprimir-barcode');

Route::put('/ventas/{venta}/pagos-financiamiento', [VentaController::class, 'updatePagosFinanciamiento'])
    ->name('ventas.pagosFinanciamiento.update');


Route::prefix('checklists/{venta}')->middleware('auth')->group(function() {
    Route::get('wizard',        [ChecklistController::class, 'wizard'])->name('checklists.wizard');
    Route::get('ingenieria',    [ChecklistController::class, 'ingenieria'])->name('checklists.ingenieria');
    Route::post('ingenieria',   [ChecklistController::class, 'guardarIngenieria'])->name('checklists.guardarIngenieria');
    Route::get('embalaje',      [ChecklistController::class, 'embalaje'])->name('checklists.embalaje');
    Route::post('embalaje',     [ChecklistController::class, 'guardarEmbalaje'])->name('checklists.guardarEmbalaje');
    Route::get('entrega',       [ChecklistController::class, 'entrega'])->name('checklists.entrega');
    Route::post('entrega',      [ChecklistController::class, 'guardarEntrega'])->name('checklists.guardarEntrega');
});


Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
Route::get('/productos/cards', [ProductoController::class, 'cardsVista'])->name('productos.cards');
Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');



// routes/web.php
Route::resource('prestamos', PrestamoController::class); // incluye create/show/etc
Route::post('registros/lookup', [PrestamoController::class, 'lookupBySerie'])->name('registros.lookup');

// (Opcional) alias por si tienes enlaces viejos a 'prestamos.wizard'
Route::get('prestamos/wizard', fn () => redirect()->route('prestamos.create'))
     ->name('prestamos.wizard');
// routes/web.php
Route::get('prestamos/{id}/pdf', [PrestamoController::class, 'pdf'])->name('prestamos.pdf');



Route::get('/ventas/{venta}/etiqueta', [VentaController::class, 'etiqueta'])->name('ventas.etiqueta');

// routes/web.php
Route::resource('prestamos', PrestamoController::class); // index/create/store/show/edit/update/destroy
Route::post('registros/lookup', [PrestamoController::class, 'lookupBySerie'])->name('registros.lookup');

// (Opcional) alias por si tienes enlaces viejos a 'prestamos.wizard'
Route::get('prestamos/wizard', fn () => redirect()->route('prestamos.create'))
     ->name('prestamos.wizard');

// PDF del préstamo
Route::get('prestamos/{id}/pdf', [PrestamoController::class, 'pdf'])->name('prestamos.pdf');


/*
|----------------------------------------------------------------------
| Nueva página de escaneo (salida / devolución / vendido)
|----------------------------------------------------------------------
| UI moderna en /prestamos/{id}/scan con KPIs en vivo
*/
Route::get('prestamos/{prestamo}/scan', [PrestamoController::class, 'returnPage'])->name('prestamos.scan');
Route::get('prestamos/{prestamo}/scan-resumen', [PrestamoController::class, 'returnResumen'])->name('prestamos.scan.resumen');

Route::post('prestamos/{prestamo}/scan-salida', [PrestamoController::class, 'scanSalida'])->name('prestamos.scan.salida');
Route::post('prestamos/{prestamo}/scan-devolucion', [PrestamoController::class, 'scanDevolucion'])->name('prestamos.scan.devolucion');
Route::post('prestamos/{prestamo}/scan-vendido', [PrestamoController::class, 'scanVendido'])->name('prestamos.scan.vendido');


/*
|----------------------------------------------------------------------
| Compatibilidad con rutas antiguas (/return/*)
|----------------------------------------------------------------------
| Mantén tus enlaces viejos funcionando. Redirigimos a la UI nueva
| y conservamos el POST de "return/scan" que ahora reutiliza la lógica
| de scanDevolucion dentro del controlador.
*/
Route::get('prestamos/{prestamo}/return', function ($prestamo) {
    return redirect()->route('prestamos.scan', $prestamo);
})->name('prestamos.return.page');

Route::get('prestamos/{prestamo}/return/resumen', function ($prestamo) {
    return redirect()->route('prestamos.scan.resumen', $prestamo);
})->name('prestamos.return.resumen');

// Alias legacy: sigue aceptando POST a /return/scan (marca DEVOLUCIÓN)
Route::post('prestamos/{prestamo}/return/scan', [PrestamoController::class, 'returnScan'])->name('prestamos.return.scan');

Route::middleware(['auth'])->group(function () {
    // Formulario + filtros
    Route::get('/promos/whatsapp/direct', [WhatsappPromotionController::class, 'directCreate'])
        ->name('promos.whatsapp.direct.create');

    // Envío
    Route::post('/promos/whatsapp/direct', [WhatsappPromotionController::class, 'directSend'])
        ->name('promos.whatsapp.direct.send');
        // ->middleware('throttle:wa-promos'); // <- opcional si quieres limitar la tasa
});

// ✅ Envío por WhatsApp (Plantilla fija doc_pdf_utility_v1 – botón único en la vista)
Route::post('/propuestas/{propuesta}/whatsapp/plantilla', [PropuestaController::class, 'sendWhatsappTemplateRemision'])
    ->name('propuestas.whatsapp.plantilla');

// (Opcional) Endpoints usados para depurar / cargar plantillas
Route::get('/whatsapp/templates', [PropuestaController::class, 'whatsappTemplates'])
    ->name('whatsapp.templates');

Route::get('/whatsapp/debug', [PropuestaController::class, 'whatsappDebug'])
    ->name('whatsapp.debug');

Route::get('/ventas/{venta}/pdf', [VentaController::class, 'pdf'])->name('ventas.pdf');
Route::post('/ventas/{venta}/whatsapp/plantilla', [VentaController::class, 'sendWhatsappTemplateRemision'])
    ->name('ventas.whatsapp.plantilla');



// ✅ Chat interno (requiere auth)
Route::middleware('auth')->group(function () {
    Route::get ('/whatsapp/inbox', [WhatsappInboxController::class, 'index'])->name('wa.inbox');
    Route::get('/whatsapp/inbox/fetch', [WhatsappInboxController::class, 'fetchThreads'])->name('wa.inbox.fetch');
    Route::get ('/whatsapp/chat/{msisdn}', [WhatsappInboxController::class, 'show'])->name('wa.chat');
    Route::post('/whatsapp/chat/{msisdn}', [WhatsappInboxController::class, 'send'])->name('wa.chat.send');
    Route::get ('/whatsapp/chat/{msisdn}/fetch', [WhatsappInboxController::class, 'fetch'])->name('wa.chat.fetch');
    Route::get('/whatsapp/media/{mediaId}', [WhatsappMediaController::class, 'show'])
        ->name('wa.media');
        // routes/web.php (acciones desde tu panel)
    Route::post('/whatsapp/claim/{msisdn}', [WhatsappWebhookController::class, 'claimByAgent'])
        ->name('wa.claim');
    Route::post('/whatsapp/close/{msisdn}', [WhatsappWebhookController::class, 'closeByAgent'])
        ->name('wa.close');
});

Route::middleware(['auth'])->group(function () { 
    Route::get('/transactions',        [CashTransactionController::class,'index'])->name('transactions.index');
    Route::get('/transactions/create', [CashTransactionController::class,'create'])->name('transactions.create');

    // Tabs (AJAX)
    Route::post('/transactions/allocation',            [CashTransactionController::class,'storeAllocation'])->name('transactions.allocation.store');
    Route::post('/transactions/disbursement/direct',   [CashTransactionController::class,'storeDisbursementDirect'])->name('transactions.disbursement.direct');
    Route::post('/transactions/disbursement/qr/start', [CashTransactionController::class,'startDisbursementWithQr'])->name('transactions.disbursement.qr.start');
    Route::post('/transactions/return',                [CashTransactionController::class,'storeReturn'])->name('transactions.return.store');

    // Dashboard AJAX
    Route::get('/transactions/data/chart',   [CashTransactionController::class,'apiChart'])->name('transactions.chart');
    Route::get('/transactions/data/metrics', [CashTransactionController::class,'apiMetrics'])->name('transactions.metrics');
    Route::get('/transactions/data/list',    [CashTransactionController::class,'apiTransactions'])->name('transactions.list');

    // Poll QR
    Route::get('/transactions/qr/status/{token}', [CashTransactionController::class,'qrStatus'])->name('transactions.qr.status');

    // >>> Recibo PDF (lo genera si no existe y lo muestra inline)
    Route::get('/transactions/{transaction}/receipt', [CashTransactionController::class,'receipt'])
        ->name('transactions.receipt');
});

// Rutas públicas para el celular del usuario (sin login)
Route::get('/qr/{token}',  [CashTransactionController::class, 'showQrForm'])->name('transactions.qr.show');
Route::post('/qr/{token}', [CashTransactionController::class, 'ackDisbursementWithQr'])->name('transactions.qr.ack');

Route::middleware(['auth']) // opcional
    ->prefix('promos')
    ->group(function () {
        Route::get('/promo-todo', [PromoController::class,'create'])->name('promos.promo_todo.form');
        Route::post('/promo-todo', [PromoController::class,'send'])->name('promos.promo_todo.send');
    });
 Route::resource('paquetes', PaqueteController::class);

Route::middleware(['auth'])->group(function () {

    /* =========================
     * GUIAS (SPA + JSON AJAX)
     * ========================= */
    Route::prefix('guias')->name('guias.')->controller(GuiaController::class)->group(function () {
        Route::get('/',            'create')->name('create');         // Vista SPA
        Route::post('/',           'store')->name('store');           // Crear guía (AJAX o normal)
        Route::get('/disponibles', 'disponibles')->name('disponibles'); // JSON: guías sin entrega (cards)
        Route::get('/search',      'getGuias')->name('search');       // JSON: buscador (q/suggest/per_page/solo_disponibles)
    });

    /* ==================================
     * ENTREGAS (vista + JSON para tabla)
     * ================================== */
    // Formulario de captura (mantienes /entrega como en tus vistas)
    Route::get('/entrega', [EntregaGuiaController::class, 'create'])->name('entrega.create');

    // Listado + acciones
    Route::prefix('entregas')->name('entregas.')->controller(EntregaGuiaController::class)->group(function () {
        Route::get('/',     'index')->name('index');     // Vista listado (tabla custom)
        Route::post('/',    'store')->name('store');     // Registrar entrega (AJAX o normal)

        // JSON para tabla custom (la que usas en la vista con fetch a route('entregas.list'))
        Route::get('/list', 'list')->name('list');       // q/page/per_page

        // (Opcional) Compatibilidad con tu DataTable anterior
        Route::get('/data', 'data')->name('data');
    });
});
// routes/web.php
Route::get('/guias/resumen', [\App\Http\Controllers\GuiaController::class, 'resumen'])
    ->name('guias.resumen');   // <-- NUEVO
Route::middleware(['auth'])->group(function () {

    // YA EXISTENTES
    Route::get('/registros/create', [RegistroController::class, 'create'])->name('registros.create');
    Route::post('/registros/guardar', [RegistroController::class, 'guardarRegistro'])->name('registros.guardar');

    Route::get('/registros', [RegistroController::class, 'index'])->name('registros.index');
    Route::get('/registros/{id}/detalles', [RegistroController::class, 'obtenerDetalles'])->name('registros.detalles');
    Route::get('/registros/{id}/barcode', [RegistroController::class, 'imprimirBarcode'])->name('registros.imprimir-barcode');

    // Opcionales
    Route::get('/inventario/buscar', [RegistroController::class, 'vistaBuscar'])->name('inventario.buscar');
    Route::post('/inventario/buscar', [RegistroController::class, 'buscarSubmit'])->name('inventario.buscar.submit');
    Route::get('/inventario/detalle/{id}', [RegistroController::class, 'mostrarProductoDetalle'])->name('inventario.detalle');

    /* ====== NUEVAS RUTAS: EDITAR / ACTUALIZAR / ELIMINAR ====== */

    // Editar (formulario)
    Route::get('/registros/{id}/edit', [RegistroController::class, 'edit'])->name('registros.edit');

    // Actualizar (PUT/PATCH desde el form de edit)
    Route::match(['put', 'patch'], '/registros/{id}', [RegistroController::class, 'update'])->name('registros.update');

    // Eliminar (usa @method('DELETE') en el form)
    Route::delete('/registros/{id}', [RegistroController::class, 'eliminarRegistro'])->name('registros.destroy');

    /* ====== ALIAS OPCIONALES (por compatibilidad con lo que ya tenías en las vistas) ====== */

    // Alias para editar (si en alguna vista usas route('inventario.editar', $id))
    Route::get('/inventario/editar/{id}', [RegistroController::class, 'edit'])->name('inventario.editar');

    // Alias para eliminar (si en alguna vista usas route('registros.eliminar', $id))
    Route::delete('/registros/eliminar/{id}', [RegistroController::class, 'eliminarRegistro'])->name('registros.eliminar');

    // Alias antiguo para actualizar vía POST (si lo usabas)
    Route::post('/registros/{id}/actualizar', [RegistroController::class, 'actualizarRegistro'])->name('registros.actualizar');
});
Route::get('/inv-kits', [InvKitController::class,'index']);
Route::get('/inv-kits/{slug}', [InvKitController::class,'show']);

Route::get('/procesos/{id}/stock', function ($id) {
    return view('procesos.stock', ['id' => $id]);
})->name('proceso.stock');

Route::middleware(['auth'])->group(function () {
    Route::post('/registros/{id}/validar-pin-edicion', 
        [RegistroController::class, 'validarPinEdicion'])
        ->name('registros.validar-pin-edicion');
});


Route::get('/envios-gastos',        [EnvioGastoController::class, 'index'])->name('envios-gastos.index');
Route::get('/envios-gastos/create', [EnvioGastoController::class, 'create'])->name('envios-gastos.create');
Route::post('/envios-gastos',       [EnvioGastoController::class, 'store'])->name('envios-gastos.store');
Route::post('/clientes/check-telefono', [ClienteController::class, 'checkTelefono'])
    ->name('clientes.checkTelefono');
        Route::delete('/ventas/{venta}', [VentaController::class, 'destroy'])->name('ventas.destroy');
        
Route::get('/ventas/{venta}/pdf-alt', [VentaController::class, 'pdfAlt'])
     ->middleware('auth')
     ->name('ventas.pdf.alt');

Route::get('/ventas/{venta}/pdf-alt/preview', [VentaController::class, 'previewPdfAlt'])
     ->middleware('auth')
     ->name('ventas.pdf.alt.preview');
     
     Route::get('/inventarioservicio', [InventarioServicioController::class, 'index'])->name('inventarioservicio');



Route::middleware('auth')->group(function () {
    // CRUD
    Route::get('/tickets',                 [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create',          [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets',                [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}',        [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/edit',   [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}',        [TicketController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/{ticket}',     [TicketController::class, 'destroy'])->name('tickets.destroy');

    // Acciones
    Route::post('/tickets/{ticket}/status',    [TicketController::class, 'changeStatus'])->name('tickets.status');

    // Watchers (dos rutas porque tus blades llaman a .sync y a .update)
    Route::post('/tickets/{ticket}/watchers',  [TicketController::class, 'syncWatchers'])->name('tickets.watchers.sync');
    Route::put('/tickets/{ticket}/watchers',   [TicketController::class, 'updateWatchers'])->name('tickets.watchers.update');

    // Comentarios
    Route::post('/tickets/{ticket}/comments',  [TicketController::class, 'storeComment'])->name('tickets.comments.store');
});

Route::post('/ai/checklist', [AiChecklistController::class, 'suggest'])->name('ai.checklist');

// IA – buscador de clientes
Route::get('/clientes/search', [ClienteController::class, 'search'])
    ->name('clientes.search')
    ->middleware('auth'); // opcional según tu proyecto
   Route::resource('ordenes', OrdenController::class)->names('ordenes');
Route::get('ordenes/{orden}/pdf', [OrdenController::class, 'pdf'])->name('ordenes.pdf');
Route::post('/ordenes/ai-checklist', [OrdenController::class, 'aiChecklist'])->name('ordenes.aiChecklist');
Route::get('/ordenes/{orden}/remision-pdf', [OrdenController::class, 'remisionPdf'])
    ->name('ordenes.remision.pdf');
    

Route::middleware(['web','auth'])->group(function () {
    // Eliminar pagos PENDIENTES y SIN financiamiento
    Route::delete('/pagos/{pago}', [PagoController::class, 'destroy'])
        ->name('pagos.destroy');
});
    
Route::get('/venta/{venta}/pagos-global-pdf', [VentaController::class, 'pagosGlobalPdf'])
    ->name('venta.pagos-global.pdf');

// Crear
// Crear (RUTA FIJA primero)
Route::get('/paquetes/crear', [PaqueteController::class, 'create'])
    ->name('paquetes.create');

Route::post('/paquetes', [PaqueteController::class, 'store'])
    ->name('paquetes.store');

// Ver detalle de un paquete (show) – SOLO números
Route::get('/paquetes/{paquete}', [PaqueteController::class, 'show'])
    ->whereNumber('paquete')
    ->name('paquetes.show');

// Editar
Route::get('/paquetes/{paquete}/editar', [PaqueteController::class, 'edit'])
    ->whereNumber('paquete')
    ->name('paquetes.edit');

Route::put('/paquetes/{paquete}', [PaqueteController::class, 'update'])
    ->whereNumber('paquete')
    ->name('paquetes.update');

// Eliminar
Route::delete('/paquetes/{paquete}', [PaqueteController::class, 'destroy'])
    ->whereNumber('paquete')
    ->name('paquetes.destroy');
    
    // Mostrar la ficha completa del equipo
Route::get('/servicios/{servicio}', [App\Http\Controllers\ServicioController::class, 'show'])
    ->name('servicios.show');
// Listado (Mantenimiento interno)
Route::get('/mantenimiento-interno', [App\Http\Controllers\ServicioController::class, 'index'])
    ->name('servicios.index');
    
    
   Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])
        ->name('perfil.updatePassword');


        Route::middleware('auth')->group(function () {
    Route::post('/notificaciones/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::post('/notificaciones/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');
});

Route::get('/cron/eventos/reminders', function (Request $request) {
    // 1) Seguridad por token
    if ($request->query('token') !== config('services.agenda_cron.token')) {
        abort(403, 'Token inválido');
    }

    // 2) Ejecutamos el comando agenda:run
    //    Puedes ajustar limit y window si quieres
    Artisan::call('agenda:run', [
        '--limit'  => 200,
        '--window' => 5,    // minutos hacia atrás para considerar next_reminder_at
    ]);

    return response()->json([
        'ok'   => true,
        'cmd'  => 'agenda:run',
        'out'  => Artisan::output(), // lo que imprime el comando (debug)
    ]);
});

Route::middleware('auth')->group(function () {

    // ✅ Página de agenda (la vista)
    Route::view('/agenda', 'agenda')->name('agenda');

    // ✅ Feed JSON para FullCalendar
    Route::get('/agenda/events', [EventoController::class, 'index'])->name('agenda.events');

    // 🔔 Notificaciones
    Route::get('/notifications/poll', [NotificationsController::class, 'poll'])->name('notifications.poll');
    Route::post('/notifications/read', [NotificationsController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationsController::class, 'readAll'])->name('notifications.readAll');
});


    Route::post('/financiamientos/notificar/{pagoId}', [\App\Http\Controllers\VentaController::class, 'notificarPagoFinanciamiento'])
    ->middleware('auth');
    Route::post('/financiamientos/notificar/{pagoId}', [VentaController::class, 'notificarPagoFinanciamiento'])
    ->middleware('auth');

Route::post('/financiamientos/notificar-pendientes', [VentaController::class, 'notificarPagosPendientesHoyYAtrasados'])
    ->middleware('auth');

Route::get('/cron/financiamientos/notificar', [CronPublicController::class, 'financiamientosNotificar'])
    ->middleware('throttle:10,1'); // ✅ anti-spam

    
    
     // ✅ Importar Excel (1 o 2 archivos)
    Route::post('/asistencias/importar-excel', [AsistenciaController::class, 'importarExcel'])
        ->name('asistencias.importarExcel');

    // Verificar si ya tiene entrada (para cambiar a salida)
    Route::get('/asistencia/verificar', [AsistenciaController::class, 'verificarAsistencia'])
        ->name('asistencias.verificar');

Route::get('/asistencias/historial', [AsistenciaController::class, 'historialHorizontal'])
    ->name('asistencias.horizontal');
    
Route::get('/remisions/{remision}/ticket', [RemisionController::class, 'ticketMantenimiento'])
  ->name('remisions.ticketMantenimiento');

Route::get('/remisions/{remision}/ticket-pdf', [RemisionController::class, 'ticketMantenimientoPdf'])
  ->name('remisions.ticketMantenimientoPdf');
Route::get('/public/remision/{remision}/ticket-mantenimiento', [PublicRemisionPdfController::class, 'ticketMantenimiento'])
    ->name('public.remision.ticket_mantenimiento')
    ->middleware('signed'); // ✅ opcional si ya validas hasValidSignature() dentro
    
    
Route::post('/registros/check-series', [RegistroController::class, 'checkSeries'])
  ->name('registros.check-series');

Route::get('/registros/export/pdf', [RegistroController::class, 'exportPdf'])
  ->name('registros.export.pdf');

  // EXCEL (CSV) – mismos filtros que PDF
Route::get('/inventario/export/excel', [RegistroController::class, 'exportExcel'])
    ->name('registros.exportExcel');
    
    
  // EXCEL (CSV) – mismos filtros que PDF
Route::get('/inventario/export/excel', [RegistroController::class, 'exportExcel'])
    ->name('registros.exportExcel');

    Route::get('/catalogo/export/pdf', [ProductoController::class, 'exportPdf'])->name('catalogo.export.pdf');
Route::get('/catalogo/export/xlsx', [ProductoController::class, 'exportXlsx'])->name('catalogo.export.xlsx');
Route::middleware(['web','auth'])->group(function () {
    // Eliminar pagos PENDIENTES y SIN financiamiento
    Route::delete('/pagos/{pago}', [PagoController::class, 'destroy'])
        ->name('pagos.destroy');
        
});

Route::put('/pagos/{id}/revertir', [\App\Http\Controllers\PagoController::class, 'revertirAPendiente'])
  ->name('pagos.revertir');

  
Route::get('/ordenes/{orden}/pagos', [PagoController::class, 'indexPorOrden'])
    ->name('ordenes.pagos.index');

Route::post('/ordenes/{orden}/pagos', [PagoController::class, 'storePorOrden'])
    ->name('ordenes.pagos.store');

Route::post('/ordenes/pagos/{pago}/aprobar', [PagoController::class, 'aprobarPagoOrden'])
    ->name('ordenes.pagos.aprobar');

Route::post('/ordenes/pagos/{pago}/revertir', [PagoController::class, 'revertirPagoOrden'])
    ->name('ordenes.pagos.revertir');

Route::delete('/ordenes/pagos/{pago}', [PagoController::class, 'destroyPagoOrden'])
    ->name('ordenes.pagos.destroy');


Route::prefix('registros/{registro}')->group(function () {
    // Borrar un proceso específico
    Route::delete('procesos/{proceso}', [ProcesoEquipoController::class, 'eliminarProceso'])
        ->name('procesos.eliminar');

    // Regresar el flujo a un estado (borra los procesos posteriores)
    Route::post('procesos/rollback', [ProcesoEquipoController::class, 'rollbackEstado'])
        ->name('procesos.rollback');

    // (Opcional) Borrar TODOS los procesos del registro (reset completo)
    Route::delete('procesos', [ProcesoEquipoController::class, 'resetProcesos'])
        ->name('procesos.reset');
});

Route::delete('/registros/{registro}/procesos/{proceso}', [ProcesoEquipoController::class, 'eliminarProceso'])
    ->name('procesos.eliminar');
    
    
Route::middleware(['auth'])->group(function () {

  // =========================
  // Internal Assets / Inventory (EN)
  // =========================
  Route::get('/internal-assets', [InventoryController::class, 'index'])->name('assets.index');

  Route::get('/internal-assets/create', [InventoryController::class, 'create'])->name('assets.create');
  Route::post('/internal-assets', [InventoryController::class, 'store'])->name('assets.store');

  Route::get('/internal-assets/{item}/edit', [InventoryController::class, 'edit'])->name('assets.edit');
  Route::put('/internal-assets/{item}', [InventoryController::class, 'update'])->name('assets.update');

  Route::delete('/internal-assets/{item}', [InventoryController::class, 'destroy'])->name('assets.destroy');

  // Assign to user + signature + timestamp
  Route::post('/internal-assets/assign', [InventoryController::class, 'assign'])->name('assets.assign');

  // PDF report per user
  Route::get('/internal-assets/users/{user}/pdf', [InventoryController::class, 'userPdf'])->name('assets.userPdf');

});

Route::post('/internal-assets/categories', [InventoryCategoryController::class, 'store'])
  ->name('assets.categories.store');
  
  
  Route::middleware('auth')->group(function () {
    Route::post('/uploads', [UploadController::class, 'store'])->name('uploads.store');
});
Route::middleware(['auth'])->group(function () {
    Route::post('/ai/tickets/checklist', [AiTicketController::class, 'checklist'])
        ->name('ai.tickets.checklist');
});
Route::middleware('auth')->group(function () {
    Route::post('/tickets/{ticket}/checklist/{item}/update', [TicketChecklistController::class, 'updateItem'])
        ->name('tickets.checklist.updateItem');
});



Route::middleware(['auth'])->group(function () {

    Route::resource('tickets', TicketController::class);

    // NUEVAS VISTAS:
    // - El creador ve avance / progreso
    Route::get('/tickets/{ticket}/progress', [TicketController::class, 'progress'])
        ->name('tickets.progress');

    // - El asignado / watchers trabajan el checklist
    Route::get('/tickets/{ticket}/work', [TicketController::class, 'work'])
        ->name('tickets.work');

    // - Guardar avance del checklist (trabajo)
    Route::post('/tickets/{ticket}/work', [TicketController::class, 'storeWork'])
        ->name('tickets.work.store');
});
Route::delete('/clientes/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');



Route::get('/assets/board', [App\Http\Controllers\InventoryController::class, 'board'])->name('assets.board');
use App\Http\Controllers\InventoryAssignmentController;

Route::get('/internal-assets/assignments', [InventoryAssignmentController::class, 'index'])->name('assets.assignments.index');
Route::post('/internal-assets/assignments', [InventoryAssignmentController::class, 'store'])->name('assets.assignments.store');
Route::post('/internal-assets/assignments/{assignment}/return', [InventoryAssignmentController::class, 'returnAsset'])->name('assets.assignments.return');
Route::get('/internal-assets/assignments/{assignment}/pdf', [InventoryAssignmentController::class, 'pdf'])->name('assets.assignments.pdf');


Route::get('/servicio/{id}/proceso', [ServicioController::class, 'proceso'])
    ->name('servicio.proceso');

Route::post('/servicio/{id}/proceso', [ServicioController::class, 'avanzarProceso'])
    ->name('servicio.proceso.avanzar');

Route::get('/servicio/{id}/orden-servicio', [ServicioController::class, 'ordenServicioForm'])
    ->name('servicio.os.form');

Route::post('/servicio/{id}/orden-servicio', [ServicioController::class, 'ordenServicioValidar'])
    ->name('servicio.os.validar');

    Route::post('/ordenes/{orden}/vincular-servicio', [OrdenController::class, 'vincularServicio'])
    ->name('ordenes.vincular_servicio');


    Route::get('/productos/exportar/woocommerce', [ProductoController::class, 'exportWooCommerceXlsx'])
    ->name('productos.export.woocommerce');


    Route::middleware(['auth'])->group(function () {
    Route::get('/inventario/servicio/{id}/salida-externa/qr', [ServicioController::class, 'qrSalidaExterna'])
        ->name('servicio.externo.salida.qr');
});



Route::middleware(['auth'])->group(function () {
    Route::get('/inventario/servicio/{id}/salida-externa/qr', [ServicioController::class, 'qrSalidaExterna'])
        ->name('servicio.externo.salida.qr');
});

Route::get('/servicios/externo/salida/acceso/{id}', [ServicioController::class, 'accesoSalidaExterna'])
    ->name('servicio.externo.salida.access');

Route::get('/servicios/externo/salida/{token}', [ServicioController::class, 'formSalidaExterna'])
    ->name('servicio.externo.salida.form');

Route::post('/servicios/externo/salida/{token}', [ServicioController::class, 'storeSalidaExterna'])
    ->name('servicio.externo.salida.store');

Route::get('/financiamientos/auditoria', [VentaController::class, 'auditoriaFinanciamientos'])
    ->name('financiamientos.auditoria');

Route::post('/financiamientos/auditoria/chat', [VentaController::class, 'auditoriaFinanciamientosChat'])
    ->name('financiamientos.auditoria.chat');

Route::post('/financiamientos/auditoria/pdf', [VentaController::class, 'auditoriaFinanciamientosPdf'])
    ->name('financiamientos.auditoria.pdf');

    Route::get('/inventario/servicio/{servicio}/externo/salida/qr/status', [ServicioController::class, 'qrSalidaExternaStatus'])
    ->name('servicio.externo.salida.qr.status');

Route::post('/financiamientos/auditoria/chat/stream', [VentaController::class, 'auditoriaFinanciamientosChatStream'])
    ->name('financiamientos.auditoria.chat.stream');

    Route::delete('/users/{id}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
    
// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


// Equipos
Route::resource('equipments', EquipmentController::class)->except(['create', 'edit', 'show']);

// Componentes
Route::resource('components', ComponentController::class);

// Rentas
Route::resource('rentals', RentalController::class);
Route::post('/rentals/{rental}/status', [RentalController::class, 'changeStatus'])->name('rentals.status');
Route::post('/rentals/{rental}/items', [RentalController::class, 'storeItem'])->name('rentals.items.store');
Route::delete('/rentals/{rental}/items/{item}', [RentalController::class, 'destroyItem'])->name('rentals.items.destroy');
Route::post('/rentals/{rental}/invoice/mark-paid', [RentalController::class, 'markInvoicePaid'])->name('rentals.invoice.markPaid');
Route::post('/logistics/{logistic}/status', [RentalController::class, 'updateLogisticsStatus'])->name('rentals.logistics.status');

// Logística
Route::resource('logistics', LogisticsController::class);

// Facturas
Route::resource('invoices', InvoiceController::class);

// Mantenimientos
Route::resource('maintenances', MaintenanceController::class);

// Compliance
Route::resource('compliance_logs', ComplianceLogController::class);

    
    Route::get('/inventario/servicio/{id}/edit', [ServicioController::class, 'edit'])->name('servicio.edit');
Route::put('/inventario/servicio/{id}', [ServicioController::class, 'update'])->name('servicio.update');


Route::get('/bento', [BentoController::class, 'index'])->name('bento.index');

Route::get('/servicios/{id}/edit', [ServicioController::class, 'edit'])->name('servicios.edit');
Route::put('/servicios/{id}', [ServicioController::class, 'update'])->name('servicios.update');
Route::delete('/servicios/{id}', [ServicioController::class, 'destroy'])->name('servicios.destroy');


Route::post('/registros/{registro}/cambiar-estado', [RegistroController::class, 'cambiarEstado'])
    ->name('registros.cambiar-estado');
    
Route::delete('/pedidos/{pedido}', [PedidoController::class, 'destroy'])->name('pedidos.destroy');
 /*
|--------------------------------------------------------------------------
| AGENDA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Vista calendario
    Route::get('/agenda', [AgendaEventController::class,'calendar'])->name('agenda.calendar');
   Route::get('/agenda/users', [AgendaEventController::class, 'users'])->name('agenda.users');
    // Feed JSON
    Route::get('/agenda/feed', [AgendaEventController::class,'feed'])->name('agenda.feed');
    Route::get('/agenda/resumen', [AgendaEventController::class, 'summary'])->name('agenda.summary');

    // CRUD AJAX
    Route::post('/agenda',             [AgendaEventController::class,'store'])->name('agenda.store');
    Route::get('/agenda/{agenda}',     [AgendaEventController::class,'show'])->name('agenda.show');
    Route::put('/agenda/{agenda}',     [AgendaEventController::class,'update'])->name('agenda.update');
    Route::delete('/agenda/{agenda}',  [AgendaEventController::class,'destroy'])->name('agenda.destroy');

    // Drag/resize
    Route::put('/agenda/{agenda}/move', [AgendaEventController::class,'move'])->name('agenda.move');
});

/*
|--------------------------------------------------------------------------
| CRON AGENDA
|--------------------------------------------------------------------------
*/
Route::get('/cron/agenda-run', function (Request $request) {
    if (! $request->hasValidSignature()) {
        return response()->json([
            'ok' => false,
            'message' => 'Firma no válida.',
        ], 401);
    }

    try {
        Artisan::call('agenda:run', [
            '--limit'  => 200,
            '--window' => 5,
        ]);

        return response()->json([
            'ok'     => true,
            'output' => Artisan::output(),
            'time'   => now('America/Mexico_City')->format('Y-m-d H:i:s'),
        ]);
    } catch (\Throwable $e) {
        \Log::error('cron.agenda-run.error', [
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);

        return response()->json([
            'ok'      => false,
            'message' => $e->getMessage(),
        ], 500);
    }
})->name('cron.agenda.run');

Route::get('/cron/agenda-url-test', function () {
    return URL::temporarySignedRoute(
        'cron.agenda.run',
        now()->addYears(5)
    );
});

Route::get('/clientes/exportar', [WhatsappPromotionControllerController::class, 'exportClientes'])->name('clientes.exportar');
Route::get('/cron/eventos/reminders', function () {
    $token = request()->query('token');

    if (!$token || $token !== env('AGENDA_CRON_TOKEN')) {
        Log::warning('CRON agenda rechazado por token inválido', [
            'ip' => request()->ip(),
            'token' => $token,
        ]);

        abort(403, 'Token inválido');
    }

    try {
        Artisan::call('agenda:run', [
            '--limit' => 200,
            '--window' => 10,
        ]);

        $output = Artisan::output();

        Log::info('CRON agenda ejecutado correctamente desde URL', [
            'ip' => request()->ip(),
            'output' => $output,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Recordatorios de agenda ejecutados correctamente.',
            'output' => $output,
            'time' => now()->toDateTimeString(),
        ]);
    } catch (\Throwable $e) {
        Log::error('CRON agenda error ejecutando agenda:run desde URL', [
            'ip' => request()->ip(),
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'ok' => false,
            'message' => 'Error ejecutando recordatorios.',
            'error' => $e->getMessage(),
            'time' => now()->toDateTimeString(),
        ], 500);
    }
});
Route::put('/publicaciones/{id}/subir-archivo', [PublicacionController::class, 'subirArchivo'])->name('publicaciones.subirArchivo');
Route::delete('/publicaciones/{id}', [PublicacionController::class, 'destroy'])->name('publicaciones.destroy');

Route::post('/agenda/ai/parse', [\App\Http\Controllers\AgendaEventController::class, 'aiParse'])
    ->name('agenda.ai.parse');
    Route::patch('/agenda/{agenda}/toggle-completed', [\App\Http\Controllers\AgendaEventController::class, 'toggleCompleted'])
    ->name('agenda.toggleCompleted');


Route::post('/ia/generar-promocion', function (Request $request) {

    $client = \OpenAI::client(env('OPENAI_API_KEY'));

    $keywords = $request->keywords;

    $response = $client->chat()->create([
    'model' => 'gpt-4.1-mini',
    'messages' => [
        [
            'role' => 'system',
            'content' => 'Eres un especialista en marketing médico, ventas consultivas y equipamiento hospitalario para Medibuy. Redactas mensajes comerciales profesionales para WhatsApp orientados a generar interés y credibilidad.'
        ],
        [
            'role' => 'user',
            'content' => "
Genera un mensaje promocional para WhatsApp utilizando EXCLUSIVAMENTE la información proporcionada.

INFORMACIÓN DISPONIBLE:
{$keywords}

OBJETIVO:
Promocionar el producto o servicio destacando sus beneficios clínicos, operativos o técnicos de forma profesional y persuasiva.

REGLAS OBLIGATORIAS:
- Utiliza únicamente la información proporcionada.
- No inventes productos, marcas, especificaciones, certificaciones, precios o características.
- Emplea terminología técnica relacionada con equipo médico cuando la información lo permita.
- Enfócate en beneficios, aplicaciones y valor para hospitales, clínicas, consultorios o profesionales de la salud.
- Redacta un único mensaje listo para enviar por WhatsApp.
- Mantén un tono profesional, corporativo y persuasivo.
- Evita frases genéricas de vendedor.
- No uses hashtags.
- No incluyas saludos ni despedidas.
- No menciones nombres de grupos.
- No utilices frases como: 'Contáctenos', 'Llámanos', 'Escríbenos', 'Comunícate con nosotros'.
- No inventes llamadas a la acción agresivas.
- El texto debe sonar natural y especializado.
- Longitud mínima: 800 caracteres.
- Longitud máxima: 1500 caracteres.
EMOJIS:
- Utiliza entre 2 y 5 emojis.
- Los emojis deben ser coherentes con el contenido.
- Colócalos únicamente donde aporten claridad o destaquen información.
- Nunca pongas muchos emojis seguidos.
- No coloques un emoji en cada línea.
- Ejemplos:
  🩺 para equipos médicos.
  🏥 para hospitales.
  ❤️ para salud.
  ⚕️ para atención médica.
  🔬 para laboratorio.
  💉 para insumos.
  🦴 para traumatología.
  🫀 para cardiología.
  👩‍⚕️ para personal médico.
  📈 para eficiencia.
  ✅ para beneficios.
  ⭐ para destacar una característica.

RESULTADO:
Devuelve únicamente el mensaje final con los emojis integrados de forma natural, sin títulos, sin comillas y sin explicaciones.
"
        ]
    ]
]);

    return response()->json([
        'texto' => $response->choices[0]->message->content
    ]);
});


Route::get('/ia-prueba', function () {

    $client = OpenAI::client(env('OPENAI_API_KEY'));

    $response = $client->chat()->create([
        'model' => 'gpt-4.1-mini',
        'messages' => [
            [
                'role' => 'user',
                'content' => 'Hola'
            ]
        ]
    ]);

    return $response->choices[0]->message->content;
});