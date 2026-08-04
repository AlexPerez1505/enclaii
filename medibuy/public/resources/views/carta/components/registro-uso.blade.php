@php
    $rutas = [
        '/inventario'           => ['Inventario', 'fas fa-box'],
        '/perfil'               => ['Perfil', 'fas fa-user-circle'],
        '/carta-garantia'       => ['Cartas de Garantía', 'fas fa-shield-alt'],
        '/prestamos'            => ['Préstamos', 'fas fa-money-check-alt'],
        '/publicaciones'        => ['Noticias', 'fas fa-newspaper'],
        '/ventas'               => ['Remisiones', 'fas fa-receipt'],
        '/propuestas'           => ['Cotizaciones', 'fas fa-file-contract'],
        '/remisions'            => ['Mantenimiento', 'fas fa-tools'],
        '/ventas/deudores'      => ['Financiamientos', 'fas fa-hand-holding-usd'],
        '/clientes'             => ['Clientes', 'fas fa-address-book'],
        '/agenda'               => ['Agenda', 'fas fa-calendar-alt'],
        '/camionetas/index'     => ['Camionetas', 'fas fa-truck'],
        '/fichas'               => ['Fichas Técnicas', 'fas fa-file-alt'],
        '/solicitudes/crear'    => ['Solicitar Material', 'fas fa-box-open'],
        '/cuentas'              => ['Viáticos', 'fas fa-wallet'],
        '/usuarios'             => ['Usuarios', 'fas fa-users-cog'],
        '/pedidos'              => ['Pedidos', 'fas fa-shopping-cart'],
        route('asistencias.index', [], false) => ['Asistencias', 'fas fa-user-check'],
    ];

    $currentPath = '/' . trim(request()->path(), '/');

    if (isset($rutas[$currentPath])) {
        [$nombre, $icono] = $rutas[$currentPath];
        registrarModuloUso($nombre, $currentPath, $icono);
    }
@endphp
