@extends('layouts.app')

@section('content')

    @csrf
    <style>
    body{
        background: #F5FAFF;
    }
</style>
<body>
<div class="container mt-4">
    <h2>.</h2>
    <div class="container">
    <div class="row">
        <!-- Columna de productos (lado izquierdo) -->
         <!-- Columna de cliente y detalles (lado derecho) -->
<div class="col-md-3 mt-3">
    <!-- Tarjeta de Cliente -->
    <div class="card modern-card">
        <div class="card-header modern-heade">Cliente</div>
        <div class="card-body">
            <div class="dropdown">
                <input 
                    type="text" 
                    id="search-client" 
                    class="form-control modern-input dropdown-toggle" 
                    data-bs-toggle="dropdown" 
                    placeholder="Buscar cliente..."
                    autocomplete="off"
                >
                <ul class="dropdown-menu modern-dropdown w-100" id="client-list">
                    <li>
                        <button class="dropdown-item modern-dropdown-item" onclick="selectClient({
                            nombre: 'Público en General',
                            apellido: '',
                            telefono: '',
                            email: '',
                            comentarios: ''
                        })">
                            Público en General
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item modern-dropdown-item" onclick="openCreateClientModal()">
                            + Crear nuevo cliente
                        </button>
                    </li>
                    <!-- Aquí se insertarán dinámicamente los clientes -->
                </ul>
            </div>
        </div>
        
        <!-- Sección para mostrar los detalles del cliente -->
        <div id="client-details" class="mt-3"></div>
    </div>

    

    <hr>



<!-- Selección de Lugar -->
<div class="card modern-card mb-3">
    <div class="card-header modern-heade">Lugar de la Cotización</div>
    <div class="card-body">
        <select id="lugarCotizacion" class="form-control modern-select">
            <option value="">Selecciona un lugar...</option>
            <option value="AMCG ECOS INTERNACIONAL DE CIRUGIA GENERAL">AMCG ECOS INTERNACIONAL DE CIRUGIA GENERAL</option>
            <option value="AMCG CONGRESO INTERNACIONAL DE CIRUGIA GENERA">AMCG CONGRESO INTERNACIONAL DE CIRUGIA GENERAL</option>
            <option value="AMCE CONGRESO INTERNACIONAL DE CIRUGIA ENDOSCOPICA">AMCE CONGRESO INTERNACIONAL DE CIRUGIA ENDOSCOPICA</option>
            <option value="AMECRA XXIX CONGRESO INTERNACIONAL DE ASOCIACIÓN MEX DE CIRUGIA RECONS, ARTICULAR Y ARTROSCOPICA">AMECRA XXIX CONGRESO INTERNACIONAL DE ASOCIACIÓN MEX DE CIRUGIA RECONS, ARTICULAR Y ARTROSCOPICA</option>
            <option value="CVDL CONGRESO DE VETERINARIA">CVDL CONGRESO DE VETERINARIA</option>
            <option value="AMG ECOS INTERNACIONALES DE GASTROENTEROLOGIA">AMG ECOS INTERNACIONALES DE GASTROENTEROLOGIA</option>
            <option value="AMG SEMANA NACIONAL GASTRO">AMG SEMANA NACIONAL GASTRO</option>
            <option value="otro">Otro</option>
        </select>
    </div>
</div>

<hr>

<!-- Tarjeta de Nota al Cliente -->
<div class="card modern-card">
    <div class="card-header modern-heade">Nota al Cliente</div>
    <div class="card-body">
        <textarea id="notaCliente" class="form-control modern-textarea" rows="4" placeholder="Escribe una nota..."></textarea>
    </div>
</div>
<hr>
<!-- Tarjeta de Registro de Usuario -->
<div class="card modern-card">
    <div class="card-header modern-heade">Registrado por</div>
    <div class="card-body">
        @auth
        <input type="text" id="registrado_por" name="registrado_por" class="form-control modern-textarea" 
               value="{{ Auth::user()->name }}" readonly />
        @else
        <input type="text" id="registrado_por" name="registrado_por" class="form-control modern-textarea" 
               value="Desconocido" readonly />
        @endauth
    </div>
</div>


</div>
        <div class="col-md-9">
            <!-- Contenedor de búsqueda de registros -->
            <div class="card modern-card mt-3">
    <div class="card-header modern-header">Productos</div>
    <div class="card-body">
        <div class="dropdown">
            <input 
                type="text" 
                id="buscarRegistro" 
                class="form-control modern-input dropdown-toggle" 
                data-bs-toggle="dropdown" 
                placeholder="Buscar producto..."
            >
            <ul class="dropdown-menu modern-dropdown w-100" id="sugerencias">
    <li>
        <button class="dropdown-item modern-dropdown-item" data-bs-toggle="modal" data-bs-target="#modal1">
            + Crear Producto
        </button>
    </li>

    <!-- Mostrar paquetes primero -->
    @foreach($paquetes as $paquete)
        <li>
            <button class="dropdown-item modern-dropdown-item" 
                    data-id="{{ $paquete->id }}" 
                    data-productos='@json($paquete->productos)'
                    onclick="agregarPaqueteDesdeData(this)">
                📦 {{ strtoupper($paquete->nombre) }} - Paquete
            </button>
        </li>
    @endforeach

    <!-- Mostrar productos ordenados alfabéticamente -->
    @foreach($productos->sortBy('tipo_equipo') as $producto)
        <li>
            <button class="dropdown-item modern-dropdown-item d-flex align-items-center" 
                    onclick="agregarProductoSeleccionado({{ $producto->id }}, '{{ $producto->tipo_equipo }}', '{{ $producto->modelo }}', '{{ $producto->marca }}', {{ $producto->precio }}, '{{ $producto->imagen }}')">
                
                <img src="/storage/{{ $producto->imagen }}" alt="{{ $producto->tipo_equipo }}" class="modern-product-img me-2">
                
                <div class="flex-grow-1 modern-product-info">
                    <strong>{{ strtoupper($producto->tipo_equipo) }}</strong> - {{ strtoupper($producto->modelo) }} {{ strtoupper($producto->marca) }}
                    <br>
                    <span class="text-muted modern-product-price">${{ $producto->precio }}</span>
                </div>
                
                <span class="badge modern-badge">{{ $producto->stock }} unidades</span>
            </button>
        </li>
    @endforeach
</ul>

        </div>
    </div>
</div>

   


<!-- Modal -->
<form id="formProducto" method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="modal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-heade">
                    <h5 class="modal-title" id="exampleModalLabel">Registrar Equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Tipo de equipo -->
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="tipo_equipo" class="form-control" placeholder="Ej: Monitor" required>
                    </div>

                    <!-- Modelo y Marca -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Modelo</label>
                            <input type="text" name="modelo" class="form-control" placeholder="Ej: Vision Pro" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" name="marca" class="form-control" placeholder="Ej: Stryker" required>
                        </div>
                    </div>

                    <!-- Existencias y Precio -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Existencias</label>
                            <input type="number" name="stock" class="form-control" value="1" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio</label>
                            <input type="number" name="precio" class="form-control" value="0.00" required min="0">
                        </div>
                    </div>

                    <!-- Imagen -->
                    <div class="mb-3 text-center">
                        <label class="form-label d-block">Imagen</label>
                        <div class="image-container">
                            <label for="image-upload" class="image-preview">
                                <img id="preview-icon" src="https://cdn-icons-png.flaticon.com/512/1829/1829586.png" 
                                     alt="Añadir imagen">
                                <span id="preview-text">Añadir imagen</span>
                            </label>
                            <input type="file" id="image-upload" name="imagen" accept="image/*" hidden>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Contenedor de registros seleccionados -->
<div class="card modern-card mt-3">
    <div class="card-header modern-header">Productos Seleccionados</div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="registrosSeleccionados" class="table modern-table">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Equipo</th>
                        <th>Modelo</th>
                        <th>Marca</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Sobreprecio</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí se agregarán los registros seleccionados -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-flex flex-column flex-md-row">
    <!-- Contenedor de totales -->
    <div class="card modern-card mt-3 w-100 w-md-50">
        <div class="card-header modern-header">Resumen</div>
        <div class="card-body">
        <p>Subtotal: <span id="subtotal" class="modern-value">0.00</span></p>
        <p>Descuento:
            <input type="number" id="descuento" class="form-control modern-input w-25 d-inline-block" value="0">
        </p>
        <p>Envío:
            <input type="number" id="envio" class="form-control modern-input w-25 d-inline-block" value="0">
        </p>
        <p>IVA (16%): <span id="iva" class="modern-value">0.00</span></p>
        <p>
            <label class="modern-checkbox">
                <input type="checkbox" id="aplicarIva"> Aplicar IVA
            </label>
        </p>
        <p><strong>Total: <span id="total" class="modern-value">0.00</span></strong></p>

            <div>
    <label>Selecciona Plan:</label>
    <select id="tipoPago" class="form-control modern-input w-50">
        <option value="contado">Pago de Contado</option>
        <option value="personalizado">Plan Personalizado</option>
        <option value="estatico">Plan Fijo</option>
        <option value="dinamico">Plan Flexible</option>
        <option value="credito">Plan a Crédito</option>
    </select>
</div>

<div id="opcionesDinamicas" style="display: none;">
    <label>Pago Inicial:</label>
    <input type="number" id="pagoInicial" class="form-control modern-input w-50">
</div>

<div id="opcionesCredito" style="display: none;">
    <label>Pago Inicial:</label>
    <input type="number" id="pagoCreditoInicial" class="form-control modern-input w-50">
    <label>Plazo (meses):</label>
    <input type="number" id="plazoCredito" class="form-control modern-input w-50" value="6">
</div>

<!-- Nueva sección para el plan personalizado -->
<div id="opcionesPersonalizado" style="display: none;">
    <label>Selecciona el número de meses:</label>
    <input type="number" id="mesesPersonalizado" class="form-control modern-input w-50" min="1">
   
    <div id="listaPagosPersonalizados" class="mt-3"></div>
</div>

<label for="ficha_tecnica_id">Ficha Técnica:</label>
<select name="ficha_tecnica_id" id="ficha_tecnica_id" required>
    <option value="">Seleccionar ficha técnica</option>
    @foreach ($fichas as $ficha)
        <option value="{{ $ficha->id }}">{{ $ficha->nombre }}</option>
    @endforeach
</select>

<div class="d-flex gap-2 mt-3">
    <button id="generate-pdf" class="btn btn-primary">Generar PDF</button>
    <button class="btn btn-secondary">Cancelar</button>
</div>
        </div>
    </div>
    <!-- Contenedor de Plan de Pagos -->
<div class="card modern-card mt-3 w-100 w-md-50 ms-md-3">
    <div class="card-header modern-header">Detalles del Financiamiento</div>
    <div class="card-body" id="plan-pagos"></div>
</div>
    </div>
</div>


<form id="form-cliente" method="POST" action="{{ route('clientes.store') }}">
  <div class="modal fade" id="modal_formulario" tabindex="-1" role="dialog" aria-labelledby="FormularioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-heade">
          <h5 class="modal-title" id="createClientModalLabel">Registrar Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control text-uppercase" id="nombre" name="nombre" placeholder="Ingresar nombre" required>
            </div>
            <div class="col-md-6">
              <label for="apellido" class="form-label">Apellido</label>
              <input type="text" class="form-control text-uppercase" id="apellido" name="apellido" placeholder="Ingresar apellido" required>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="tel" class="form-control text-uppercase" id="telefono" name="telefono" placeholder="Ingresar teléfono" required>
              <span id="error-telefono" class="text-danger" style="display: none;">El teléfono ya está registrado.</span>
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Ingresar email">
              <span id="error-email" class="text-danger" style="display: none;">El correo ya está registrado.</span>
            </div>
          </div>

          <div class="mb-3">
            <label for="comentarios" class="form-label">Dirección</label>
            <textarea id="comentarios" name="comentarios" class="form-control text-uppercase" placeholder="Agrega información de tu cliente"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Agregar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  // Función para convertir texto en mayúsculas automáticamente
  document.querySelectorAll('.text-uppercase').forEach(input => {
      input.addEventListener('input', function () {
          this.value = this.value.toUpperCase();
      });
  });

  // Formateo del teléfono
  document.getElementById("telefono").addEventListener("input", function (event) {
      let valor = this.value.replace(/\D/g, ""); // Elimina caracteres no numéricos

      if (valor.length <= 10) {
          valor = valor.replace(/(\d{2})(\d{4})(\d{4})/, "$1 $2 $3");
      } else if (valor.length > 10) {
          valor = valor.replace(/(\d{2})(\d{2})(\d{4})(\d{4})/, "+$1 $2 $3 $4");
      }

      this.value = valor;
  });
</script>



<!-- Modal -->
<div class="modal fade" id="cliente_creado" tabindex="-1" role="dialog" aria-labelledby="ClienteCreadoLabel" 
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header encabezado_modal text-center">
                <h5 class="modal-title titulo_modal">¡Cliente guardado exitosamente!</h5>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img src="{{ asset('images/confirmar.jpeg') }}" alt="Logo de encabezado" class="logo-modal">
                </div>
                <p class="text-center mensaje-modal">
                    El cliente se ha registrado correctamente en el sistema.  
                    Puedes proceder a cerrar este mensaje.  
                    <b>Grupo MediBuy</b>.
                </p>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button class="btn btn-listo" onclick="cerrarModal()">Listo</button>
            </div>
        </div>
    </div>
</div>



<!-- Modal de Confirmación -->
<div class="modal fade" id="modalPdf" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header encabezado_modal text-center">
                <h5 class="modal-title titulo_modal">¡Cotización Generada Exitosamente!</h5>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img src="{{ asset('images/confirmar.jpeg') }}" alt="Logo de encabezado" class="logo-modal">
                </div>
                <p class="text-center mensaje-modal">
                Su cotización ha sido registrada correctamente en el sistema.  
                La descarga comenzará en breve.  
                    <b>Grupo MediBuy</b>.
                </p>
            </div>
                <button id="btnDescargarPDF" class="btn btn-danger" style="display: none;">Descargar PDF</button>
            </div>
        </div>
    </div>
</div>
</body>
@endsection

@section('scripts')

<script>
function cerrarModal() {
    var modal = document.getElementById("cliente_creado");
    var modalInstance = bootstrap.Modal.getInstance(modal);

    if (modalInstance) {
        modalInstance.hide();
    } else {
        new bootstrap.Modal(modal).hide();
    }
}


</script>




<script>
document.addEventListener("DOMContentLoaded", function () {
  const createClientButton = document.querySelector('.dropdown-item[onclick="openCreateClientModal()"]');
  const modalFormularioElement = document.getElementById("modal_formulario");
  const modalExitoElement = document.getElementById("cliente_creado");

  // Inicializar modales
  const modalFormulario = new bootstrap.Modal(modalFormularioElement);
  const modalExito = new bootstrap.Modal(modalExitoElement);

  createClientButton.addEventListener("click", function () {
    modalFormulario.show();
  });

  // Manejar el envío del formulario
  const form = document.getElementById("form-cliente");

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    // Limpiar mensajes de error previos
    const errorTelefono = document.getElementById("error-telefono");
    const errorEmail = document.getElementById("error-email");
    errorTelefono.style.display = "none";
    errorEmail.style.display = "none";
    errorTelefono.textContent = "";
    errorEmail.textContent = "";

    // Obtener datos del formulario
    const nombre = document.getElementById("nombre").value.trim();
    const apellido = document.getElementById("apellido").value.trim();
    const telefono = document.getElementById("telefono").value.trim();
    const email = document.getElementById("email").value.trim();
    const comentarios = document.getElementById("comentarios").value.trim();

    // Validar campos requeridos
    if (!nombre || !apellido || !telefono) {
      alert("Todos los campos son obligatorios.");
      return;
    }

    // Verificar si el teléfono o el correo ya existen
    fetch("{{ route('clientes.check_unique') }}", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ telefono, email }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Si no hay problemas de duplicado, enviar los datos al servidor
          fetch("{{ route('clientes.store') }}", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "Accept": "application/json",  // 👈 importante
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ nombre, apellido, telefono, email, comentarios }),
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                // Cerrar el primer modal
                const bsModalFormulario = bootstrap.Modal.getInstance(modalFormularioElement);
                bsModalFormulario.hide();

                // Esperar a que el primer modal se cierre completamente antes de abrir el segundo
                modalFormularioElement.addEventListener("hidden.bs.modal", function () {
                  modalExito.show();
                }, { once: true });

                // Restablecer formulario
                form.reset();
              } else {
                alert(data.message || "Ocurrió un error al guardar el cliente.");
              }
            })
            .catch((error) => {
              console.error("Error:", error);
              alert("Error al guardar el cliente.");
            });
        } else {
          // Mostrar mensajes de error en el formulario
          if (data.error_telefono) {
            errorTelefono.textContent = data.error_telefono;
            errorTelefono.style.display = "block";
          }

          if (data.error_email) {
            errorEmail.textContent = data.error_email;
            errorEmail.style.display = "block";
          }
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Error al verificar la existencia del teléfono o correo.");
      });
  });

  // Detectar cuando el modal de éxito se cierre para recargar la página
  modalExitoElement.addEventListener("hidden.bs.modal", function () {
    location.reload();
  });
});


</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search-client");
    const clientList = document.getElementById("client-list");

    // Función para cargar clientes dinámicamente desde el backend
    function loadClients(search = "") {
        fetch(`/buscar-clientes?search=${search}`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
        })
            .then((response) => response.json())
            .then((clients) => {
                // Limpiar la lista de clientes
                clientList.innerHTML = `
                    <li>
                        <button class="dropdown-item modern-dropdown-item" onclick="selectClient({
                            nombre: 'Público en General',
                            apellido: '',
                            telefono: '',
                            email: '',
                            comentarios: ''
                        })">
                            Público en General
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item modern-dropdown-item" onclick="openCreateClientModal()">
                            + Crear nuevo cliente
                        </button>
                    </li>
                `;

                // Agregar los clientes a la lista
                if (clients.length === 0 && search !== "") {
                    // Si no hay resultados, mostrar un mensaje
                    clientList.innerHTML += `
                        <li><button class="dropdown-item disabled">No se encontraron resultados</button></li>
                    `;
                } else {
                    clients.forEach((client) => {
                        const clientFullName = `${client.nombre.toUpperCase()} ${client.apellido.toUpperCase()}`;
                        const clientItem = document.createElement("li");

                        // Guardamos los datos en un objeto JSON como string en el atributo data-client
                        clientItem.innerHTML = `
                            <button class="dropdown-item modern-dropdown-item" onclick='selectClient(${JSON.stringify(client)})'>
                                ${clientFullName}
                            </button>
                        `;
                        clientList.appendChild(clientItem);
                    });
                }
            })
            .catch((error) => console.error("Error al cargar clientes:", error));
    }

    // Cargar todos los clientes inicialmente
    loadClients();

    // Escuchar el evento de escritura en el campo de búsqueda
    searchInput.addEventListener("input", function () {
        const search = searchInput.value.trim();
        loadClients(search); // Buscar clientes con el término ingresado
        // Asegurar que el dropdown siempre esté abierto
        const dropdown = document.querySelector('.dropdown-toggle');
        const dropdownMenu = document.querySelector('.dropdown-menu');
        if (search !== "") {
            dropdown.setAttribute('aria-expanded', 'true');
            dropdownMenu.classList.add('show');
        }
    });

    // Función para seleccionar un cliente y mostrar todos sus datos
    window.selectClient = function (client) {
        searchInput.value = `${client.nombre.toUpperCase()} ${client.apellido.toUpperCase()}`;

        // Mostrar los detalles del cliente en la interfaz
        const clientDetails = document.getElementById("client-details");
        clientDetails.innerHTML = `
            <p><strong>Nombre:</strong> ${client.nombre.toUpperCase()} ${client.apellido.toUpperCase()}</p>
            <p><strong>Teléfono:</strong> ${client.telefono || "No registrado"}</p>
            <p><strong>Email:</strong> ${client.email || "No registrado"}</p>
            <p><strong>Dirección:</strong> ${client.comentarios || "No registrado"}</p>
        `;

        // Aplicar estilo directamente al contenedor
        clientDetails.style.padding = "15px";  // Espacio interno

    };

    // Función para abrir el modal de creación de cliente
    window.openCreateClientModal = function () {
        const modalFormulario = new bootstrap.Modal(document.getElementById("modal_formulario"));
        modalFormulario.show();
    };

    // Abrir el dropdown si el usuario presiona Enter
    searchInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            const search = searchInput.value.trim();
            loadClients(search);
            // Asegurar que el dropdown esté visible
            const dropdown = document.querySelector('.dropdown-toggle');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            dropdown.setAttribute('aria-expanded', 'true');
            dropdownMenu.classList.add('show');
        }
    });
});
</script>


  <script>
document.getElementById("formProducto").addEventListener("submit", function (event) {
    event.preventDefault(); // Evita la recarga de la página

    let formData = new FormData(this);

    fetch("{{ route('productos.store') }}", {
        method: "POST",
        body: formData,
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            "Accept": "application/json" // Importante para que Laravel devuelva JSON
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert("Producto creado con éxito");
            location.reload(); // Opcional: recargar para ver los cambios
        } else {
            alert("Error al crear el producto");
        }
    })
    .catch(error => console.error("Error:", error));
});

</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const buscarRegistro = document.getElementById("buscarRegistro");
    const sugerencias = document.getElementById("sugerencias");
    let controladorAbort = new AbortController();

    function realizarBusqueda(searchQuery) {
        controladorAbort.abort(); 
        controladorAbort = new AbortController();

        if (searchQuery.length > 1) {
            Promise.all([
                fetch("{{ route('productos.search') }}?search=" + encodeURIComponent(searchQuery), { signal: controladorAbort.signal }).then(res => res.json()),
                fetch("{{ route('paquetes.search') }}?search=" + encodeURIComponent(searchQuery), { signal: controladorAbort.signal }).then(res => res.json())
            ])
            .then(([productos, paquetes]) => {
                mostrarResultados(paquetes, productos);
            })
            .catch(error => {
                if (error.name !== "AbortError") {
                    console.error("Error en la búsqueda:", error);
                }
            });
        } else {
            restaurarListaOriginal();
        }
    }

    function mostrarResultados(paquetes, productos) {
        sugerencias.innerHTML = '';

        // Agregar paquetes primero (sin orden alfabético)
        if (paquetes.length > 0) {
            paquetes.forEach(paquete => {
                let li = document.createElement("li");
                li.innerHTML = `
                    <button class="dropdown-item modern-dropdown-item"
                            data-id="${paquete.id}"
                            data-productos='${JSON.stringify(paquete.productos)}'
                            onclick="agregarPaqueteDesdeData(this)">
                        📦 ${paquete.nombre.toUpperCase()} - Paquete
                    </button>
                `;
                sugerencias.appendChild(li);
            });
        }

        // Ordenar productos alfabéticamente por tipo_equipo antes de mostrarlos
        productos.sort((a, b) => a.tipo_equipo.localeCompare(b.tipo_equipo));

        if (productos.length > 0) {
            productos.forEach(producto => {
                let li = document.createElement("li");
                li.innerHTML = `
                    <button class="dropdown-item modern-dropdown-item d-flex align-items-center"
                            onclick="agregarProductoSeleccionado(${producto.id}, '${producto.tipo_equipo}', '${producto.modelo}', '${producto.marca}', ${producto.precio}, '${producto.imagen}')">
                        <img src="/storage/${producto.imagen}" alt="${producto.tipo_equipo}" class="modern-product-img me-2">
                        <div class="flex-grow-1 modern-product-info">
                            <strong>${producto.tipo_equipo}</strong> - ${producto.modelo} ${producto.marca} 
                            <br>
                            <span class="text-muted modern-product-price">$${producto.precio}</span>
                        </div>
                        <span class="badge modern-badge">${producto.stock} unidades</span>
                    </button>
                `;
                sugerencias.appendChild(li);
            });
        }

        if (paquetes.length === 0 && productos.length === 0) {
            sugerencias.innerHTML = '<li><button class="dropdown-item text-muted">No se encontraron resultados</button></li>';
        }
    }

    function restaurarListaOriginal() {
        Promise.all([
            fetch("{{ route('productos.search') }}?search=").then(res => res.json()),
            fetch("{{ route('paquetes.search') }}?search=").then(res => res.json())
        ])
        .then(([productos, paquetes]) => {
            mostrarResultados(paquetes, productos);
        })
        .catch(error => console.error("Error al restaurar lista:", error));
    }

    buscarRegistro.addEventListener("input", function () {
        realizarBusqueda(this.value.trim());
    });

    buscarRegistro.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            realizarBusqueda(this.value.trim());
        }
    });

    function agregarPaqueteDesdeData(element) {
        let id = element.getAttribute("data-id");
        let productos = JSON.parse(element.getAttribute("data-productos"));
        agregarPaquete(id, productos);
    }
});
</script>

<script>
   let productosSeleccionados = [];

// Función para agregar un producto seleccionado
function agregarProductoSeleccionado(id, tipoEquipo, modelo, marca, precio, imagen) {
    let productoExistente = productosSeleccionados.find(p => p.id === id);

    if (productoExistente) {
        // Si el producto ya está en la lista, aumentar la cantidad
        productoExistente.cantidad++;
        productoExistente.subtotal = productoExistente.cantidad * productoExistente.precio;
    } else {
        // Si no existe, agregarlo con cantidad inicial 1
        productosSeleccionados.push({
            id: id,
            tipo_equipo: tipoEquipo.toUpperCase(), // Convertimos a mayúsculas
            modelo: modelo.toUpperCase(), // Convertimos a mayúsculas
            marca: marca.toUpperCase(), // Convertimos a mayúsculas
            precio: parseFloat(precio),
            precio_modificado: parseFloat(precio), // Nuevo campo para el precio modificado
            imagen: imagen,
            cantidad: 1,
            subtotal: parseFloat(precio),
        });
    }

    // Actualizar la tabla y los totales
    actualizarTablaProductos();
    actualizarTotales();
}

// Función para dar formato a los números con separador de miles
function formatoMoneda(valor) {
    return parseFloat(valor).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Función para actualizar la tabla con los productos seleccionados
function actualizarTablaProductos() {
    let tabla = document.getElementById('registrosSeleccionados').getElementsByTagName('tbody')[0];
    tabla.innerHTML = ''; // Limpiar la tabla antes de agregar nuevos datos

    productosSeleccionados.forEach(producto => {
        let fila = tabla.insertRow();

        // Columna para la imagen
        let celdaImagen = fila.insertCell();
        let img = document.createElement('img');
        img.src = '/storage/' + producto.imagen;
        img.alt = 'Imagen del producto';
        img.style.width = '50px';
        celdaImagen.appendChild(img);

        // Tipo de Equipo
        let celdaTipoEquipo = fila.insertCell();
        celdaTipoEquipo.textContent = producto.tipo_equipo.toUpperCase(); // Aseguramos que sea mayúscula

        // Modelo
        let celdaModelo = fila.insertCell();
        celdaModelo.textContent = producto.modelo.toUpperCase(); // Aseguramos que sea mayúscula

        // Marca
        let celdaMarca = fila.insertCell();
        celdaMarca.textContent = producto.marca.toUpperCase(); // Aseguramos que sea mayúscula

        // Cantidad con input para cambiarla
        let celdaCantidad = fila.insertCell();
        let inputCantidad = document.createElement('input');
        inputCantidad.type = 'number';
        inputCantidad.classList.add('form-control', 'cantidad-producto');
        inputCantidad.value = producto.cantidad;
        inputCantidad.min = 1;
        inputCantidad.dataset.id = producto.id; // Guardar el ID del producto para referencia
        inputCantidad.addEventListener('input', actualizarCantidad);
        celdaCantidad.appendChild(inputCantidad);

        // Subtotal
       
        let celdaSubtotal = fila.insertCell();
        celdaSubtotal.textContent = '$' + formatoMoneda(producto.subtotal);
        celdaSubtotal.classList.add('subtotal-producto');
        celdaSubtotal.dataset.id = producto.id;
       // **Nuevo: Input para precio modificado**
       let celdaPrecioModificado = fila.insertCell();
        let inputPrecio = document.createElement('input');
        inputPrecio.type = 'number';
        inputPrecio.classList.add('form-control', 'precio-modificado');
        inputPrecio.value = producto.precio_modificado;
        inputPrecio.dataset.id = producto.id;
        inputPrecio.addEventListener('input', actualizarPrecioModificado);
        celdaPrecioModificado.appendChild(inputPrecio);

        // Acción (Eliminar)
        let celdaAccion = fila.insertCell();
        let btnEliminar = document.createElement('button');
        btnEliminar.textContent = 'Eliminar';
        btnEliminar.classList.add('btn', 'btn-danger');
        btnEliminar.dataset.id = producto.id;
        btnEliminar.onclick = function () {
            eliminarProductoSeleccionado(producto.id);
        };
        celdaAccion.appendChild(btnEliminar);
    });
}

// Función para actualizar la cantidad del producto seleccionado
function actualizarCantidad(event) {
    let id = event.target.dataset.id;
    let nuevaCantidad = parseInt(event.target.value);

    if (nuevaCantidad < 1 || isNaN(nuevaCantidad)) {
        nuevaCantidad = 1; // No permitir valores menores a 1
        event.target.value = 1;
    }

    let producto = productosSeleccionados.find(p => p.id == id);
    if (producto) {
        producto.cantidad = nuevaCantidad;

        // **Si el usuario NO ha cambiado el precio manualmente, actualizar el precio modificado**
        if (!producto.precio_modificado_manual) {
            producto.precio_modificado = producto.precio * producto.cantidad;
            document.querySelector(`.precio-modificado[data-id="${id}"]`).value = producto.precio_modificado.toFixed(2);
        }

        // **Calcular el subtotal correctamente solo con el precio original**
        producto.subtotal = producto.precio * producto.cantidad;
        document.querySelector(`.subtotal-producto[data-id="${id}"]`).textContent = '$' + producto.subtotal.toFixed(2);
    }

    actualizarTotales();
}

// Función para actualizar el precio modificado sin afectar el subtotal
function actualizarPrecioModificado(event) {
    let id = event.target.dataset.id;
    let nuevoPrecio = event.target.value.trim(); // Obtener valor sin espacios

    let producto = productosSeleccionados.find(p => p.id == id);
    if (producto) {
        if (nuevoPrecio === "") {
            producto.precio_modificado = ""; // Permitir que el usuario borre el campo y escriba un nuevo valor
        } else {
            let precioFloat = parseFloat(nuevoPrecio);
            
            // Validar que el precio ingresado sea un número y no menor al precio original
            if (!isNaN(precioFloat) && precioFloat >= producto.precio) {
                producto.precio_modificado = precioFloat;
                producto.precio_modificado_manual = true; // Marcar que el usuario cambió el precio manualmente
            } else {
                return; // No actualizar si el valor es inválido
            }
        }
    }

    actualizarTotales();
}






// Función para eliminar un producto de la lista
function eliminarProductoSeleccionado(id) {
    productosSeleccionados = productosSeleccionados.filter(p => p.id !== id);

    // Actualizar la tabla y los totales
    actualizarTablaProductos();
    actualizarTotales();
}
// Función para actualizar los totales (Subtotal, IVA, Total) usando el precio modificado
function actualizarTotales() {
    let subtotal = productosSeleccionados.reduce((sum, p) => {
        // Usar el precio modificado tal cual para el subtotal, no multiplicar por la cantidad
        let precio = p.precio_modificado || p.precio; 
        return sum + precio;  // Sumar el precio sin multiplicar por la cantidad
    }, 0);

    let descuento = parseFloat(document.getElementById('descuento').value.toString().replace(',', '')) || 0;
    let envio = parseFloat(document.getElementById('envio').value.toString().replace(',', '')) || 0;
    let iva = 0;

    // Solo aplicar IVA si la casilla está marcada
    if (document.getElementById('aplicarIva').checked) {
        iva = (subtotal - descuento + envio) * 0.16;
    }
    let total = subtotal - descuento + envio + iva;

    // Mostrar el subtotal correcto
    document.getElementById('subtotal').textContent = '$' + formatoMoneda(subtotal);
    document.getElementById('iva').textContent = '$' + formatoMoneda(iva);
    document.getElementById('total').textContent = '$' + formatoMoneda(total);
    
    actualizarPlanPagos(total);
}


function actualizarPlanPagos(total) {
    let planPagosDiv = document.getElementById('plan-pagos');
    planPagosDiv.innerHTML = '';
    let tipoPago = document.getElementById('tipoPago').value;
    let pagos = [];

    function formatoMoneda(valor) {
        return parseFloat(valor).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    let fechaActual = new Date(); // Mes actual
    let fechaPago = new Date(fechaActual); // Se usará para los pagos posteriores
    let nombresPagos = ["Primer", "Segundo", "Tercer", "Cuarto", "Quinto", "Sexto", "Séptimo", "Octavo", "Noveno", "Décimo", "Undécimo", "Duodécimo"];

    if (tipoPago === 'estatico') {
        if (total < 500000) {
            pagos.push({ cuota: formatoMoneda(total * 0.5), mes: fechaActual, descripcion: 'Pago inicial' });

            fechaPago.setMonth(fechaPago.getMonth() + 1); // Siguiente mes
            pagos.push({ cuota: formatoMoneda(total * 0.25), mes: new Date(fechaPago), descripcion: 'Primer pago' });

            fechaPago.setMonth(fechaPago.getMonth() + 1);
            pagos.push({ cuota: formatoMoneda(total * 0.25), mes: new Date(fechaPago), descripcion: 'Segundo pago' });
        } else {
            let primerPago = total * 0.4;
            pagos.push({ cuota: formatoMoneda(primerPago), mes: fechaActual, descripcion: 'Pago inicial' });

            let restante = total - primerPago;
            let numPagos = Math.min(6, Math.max(4, Math.ceil(restante / 50000)));
            let cuotaRestante = restante / numPagos;

            for (let i = 0; i < numPagos; i++) {
                fechaPago.setMonth(fechaPago.getMonth() + 1);
                pagos.push({ cuota: formatoMoneda(cuotaRestante), mes: new Date(fechaPago), descripcion: `${nombresPagos[i] || (i + 1)} pago` });
            }
        }
    } else if (tipoPago === 'dinamico') {
        let pagoInicial = parseFloat(document.getElementById('pagoInicial').value) || 0;
        let restante = total - pagoInicial;
        let numPagos = (total < 150000) ? 2 : (total < 400000) ? 4 : 6;
        let cuotaRestante = restante / numPagos;

        pagos.push({ cuota: formatoMoneda(pagoInicial), mes: fechaActual, descripcion: 'Pago inicial' });

        for (let i = 0; i < numPagos; i++) {
            fechaPago.setMonth(fechaPago.getMonth() + 1);
            pagos.push({ cuota: formatoMoneda(cuotaRestante), mes: new Date(fechaPago), descripcion: `${nombresPagos[i] || (i + 1)} pago` });
        }
    } else if (tipoPago === 'credito') {
        let pagoInicial = parseFloat(document.getElementById('pagoCreditoInicial').value) || 0;
        let plazo = parseInt(document.getElementById('plazoCredito').value) || 6;
        let tasaInteres = 0.05; // 5% mensual
        let montoCredito = total - pagoInicial;
        let totalCredito = montoCredito + (montoCredito * tasaInteres * plazo);
        let cuotaMensual = totalCredito / plazo;

        pagos.push({ cuota: formatoMoneda(pagoInicial), mes: fechaActual, descripcion: 'Pago inicial' });
        pagos.push({ cuota: formatoMoneda(totalCredito), mes: null, descripcion: 'Total a pagar con crédito' }); // ✅ Sin mes

        for (let i = 0; i < plazo; i++) {
            fechaPago.setMonth(fechaPago.getMonth() + 1);
            pagos.push({ cuota: formatoMoneda(cuotaMensual), mes: new Date(fechaPago), descripcion: `${nombresPagos[i] || (i + 1)} pago` });
        }
    } else if (tipoPago === 'personalizado') {
        generarPagosPersonalizados(total);
        return;
    }

    // Renderizar los pagos con nombres y negritas
    planPagosDiv.innerHTML = '';
    pagos.forEach((pago) => {
        let p = document.createElement('p');
        
        if (pago.mes) { 
            let nombreMes = pago.mes.toLocaleString('es-MX', { month: 'long' });
            p.innerHTML = `<strong>${pago.descripcion} - ${nombreMes}:</strong> $${pago.cuota}`;
        } else { 
            p.innerHTML = `<strong>${pago.descripcion}:</strong> $${pago.cuota}`;
        }

        planPagosDiv.appendChild(p);
    });
}


function generarPagosPersonalizados(total) {
    let meses = parseInt(document.getElementById('mesesPersonalizado').value) || 1;
    let contenedorPagos = document.getElementById('listaPagosPersonalizados');
    let planPagosDiv = document.getElementById('plan-pagos');

    // Limpiar pagos anteriores
    contenedorPagos.innerHTML = "";
    planPagosDiv.innerHTML = "";

    let pagoSugerido = total / (meses + 1); // Incluimos pago inicial en el cálculo
    let fechaActual = new Date();
    let nombresPagos = ["Primer", "Segundo", "Tercer", "Cuarto", "Quinto", "Sexto", "Séptimo", "Octavo", "Noveno", "Décimo", "Undécimo", "Duodécimo"];

    for (let i = 0; i <= meses; i++) {  // Empezamos desde 0 para el pago inicial
        let div = document.createElement('div');
        div.classList.add('mb-2');

        let mesPago = new Date(fechaActual);
        mesPago.setMonth(fechaActual.getMonth() + i); // Calculamos el mes del pago
        let nombreMes = mesPago.toLocaleString('es-MX', { month: 'long' }); // Obtener el nombre del mes en español

        let label = document.createElement('label');

        if (i === 0) {
            label.innerHTML = `<strong>Pago inicial - ${nombreMes}:</strong>`;
        } else {
            let nombrePago = nombresPagos[i - 1] || `${i + 1}°`; // Usa nombre o número si excede la lista
            label.innerHTML = `<strong>${nombrePago} pago - ${nombreMes}:</strong>`;
        }

        let inputDiv = document.createElement('div');
        inputDiv.classList.add('d-flex', 'align-items-center');

        let input = document.createElement('input');
        input.type = 'number';
        input.classList.add('form-control', 'modern-input', 'w-50');
        input.setAttribute('data-mes', i);
        input.value = pagoSugerido.toFixed(2);

        input.addEventListener('input', recalcularPagosPersonalizados);

        inputDiv.appendChild(input);
        div.appendChild(label);
        div.appendChild(inputDiv);
        contenedorPagos.appendChild(div);

        // También mostrar en plan-pagos con formato correcto y negritas
        let p = document.createElement('p');
        p.innerHTML = `<strong>${label.innerHTML}</strong> 
            $${pagoSugerido.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;
        planPagosDiv.appendChild(p);
    }
}


function recalcularPagosPersonalizados() {
    let total = parseFloat(document.getElementById('total').textContent.replace(/[$,]/g, '')) || 0;
    let listaPagos = document.querySelectorAll('#listaPagosPersonalizados input');
    let planPagosDiv = document.getElementById('plan-pagos');

    planPagosDiv.innerHTML = ""; // Limpiar antes de actualizar

    let sumaPagos = 0;
    let pagosEditados = new Map();
    let pagosNoEditados = [];

    // Leer los valores actuales y detectar pagos modificados
    listaPagos.forEach((input, index) => {
        let monto = parseFloat(input.value) || 0;
        if (input.dataset.modificado === "true") {
            pagosEditados.set(index, monto);
        } else {
            pagosNoEditados.push(input);
        }
        sumaPagos += monto;
    });

    // Ajustar solo los pagos no editados si la suma no coincide
    if (sumaPagos !== total && pagosNoEditados.length > 0) {
        let diferencia = total - sumaPagos;
        let ajustePorPago = diferencia / pagosNoEditados.length;

        pagosNoEditados.forEach(input => {
            let nuevoValor = (parseFloat(input.value) || 0) + ajustePorPago;
            if (nuevoValor >= 0) {
                input.value = nuevoValor.toFixed(2);
            }
        });

        // Recalcular la suma después del ajuste
        sumaPagos = Array.from(listaPagos).reduce((acc, input) => acc + parseFloat(input.value), 0);
    }

    // Mostrar los pagos en plan-pagos con formato de miles
    listaPagos.forEach((input, index) => {
        let monto = parseFloat(input.value) || 0;
        let p = document.createElement('p');

        // Mantener el primer pago como "Pago inicial"
        p.textContent = index === 0 
            ? `Pago inicial: $${monto.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`
            : `Pago ${index}: $${monto.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;

        planPagosDiv.appendChild(p);
    });

    // Agregar mensaje de validación
    let totalPagosP = document.createElement('p');
    totalPagosP.classList.add('no-imprimir');
    totalPagosP.style.fontWeight = "bold";

    let totalFormateado = sumaPagos.toLocaleString('es-MX', { minimumFractionDigits: 2 });
    if (Math.abs(sumaPagos - total) < 0.01) {
        totalPagosP.textContent = `Total de pagos: $${totalFormateado} ✅ (Coincide)`;
        totalPagosP.style.color = "green";
    } else {
        let totalOriginal = total.toLocaleString('es-MX', { minimumFractionDigits: 2 });
        totalPagosP.textContent = `Total de pagos: $${totalFormateado} ❌ (No coincide con $${totalOriginal})`;
        totalPagosP.style.color = "red";
    }

    planPagosDiv.appendChild(totalPagosP);
}

// Evento para marcar pagos editados manualmente
document.getElementById('listaPagosPersonalizados').addEventListener('input', function(event) {
    if (event.target.tagName === "INPUT") {
        event.target.dataset.modificado = "true"; // Marcar el input como modificado por el usuario
        recalcularPagosPersonalizados();
    }
});


// Validar pagos antes de generar PDF
document.getElementById('generate-pdf').addEventListener('click', function () {
    let total = parseFloat(document.getElementById('total').textContent.replace('$', '')) || 0;
    let tipoPago = document.getElementById('tipoPago').value;

    if (tipoPago === 'personalizado') {
        let listaPagos = document.querySelectorAll('#listaPagosPersonalizados input');
        let sumaPagos = 0;

        listaPagos.forEach(input => {
            sumaPagos += parseFloat(input.value.replace(',', '')) || 0;

        });

       
    }

    // Aquí puedes continuar con la lógica para generar el PDF
    console.log('Generando PDF con pagos correctos...');
});

// Eventos
document.getElementById('tipoPago').addEventListener('change', function() {
    document.getElementById('opcionesDinamicas').style.display = this.value === 'dinamico' ? 'block' : 'none';
    document.getElementById('opcionesCredito').style.display = this.value === 'credito' ? 'block' : 'none';
    document.getElementById('opcionesPersonalizado').style.display = this.value === 'personalizado' ? 'block' : 'none';
    actualizarTotales();
});
document.getElementById('mesesPersonalizado').addEventListener('input', function() {
    let total = parseFloat(document.getElementById('total').textContent.replace(/[$,]/g, '')) || 0;

    generarPagosPersonalizados(total);
});
document.getElementById('envio').addEventListener('input', actualizarTotales);
document.getElementById('descuento').addEventListener('input', actualizarTotales);
document.getElementById('aplicarIva').addEventListener('change', actualizarTotales);
document.getElementById('pagoInicial').addEventListener('input', actualizarTotales);
document.getElementById('pagoCreditoInicial').addEventListener('input', actualizarTotales);
document.getElementById('plazoCredito').addEventListener('input', actualizarTotales);


</script>



<script>
document.getElementById('generate-pdf').addEventListener('click', function () {
    let searchClient = document.getElementById('search-client')?.value || '';
    
    // Obtener los valores de los campos de cliente de los datos mostrados dinámicamente
    let telefono = document.querySelector('#client-details p:nth-child(2)')?.textContent.replace('Teléfono: ', '') || '';
    let email = document.querySelector('#client-details p:nth-child(3)')?.textContent.replace('Email: ', '') || '';
    let direccion = document.querySelector('#client-details p:nth-child(4)')?.textContent.replace('Dirección: ', '') || '';

    let descuento = parseFloat(document.getElementById('descuento')?.value) || 0;
    let envio = parseFloat(document.getElementById('envio').value) || 0; 
    let aplicarIva = document.getElementById('aplicarIva')?.checked ? 1 : 0;
    let tipoPago = document.getElementById('tipoPago')?.value || 'estatico';
    let validoHasta = document.getElementById('validoHasta')?.value || '';
    let lugarCotizacion = document.getElementById('lugarCotizacion')?.value || '';
    let notaCliente = document.getElementById('notaCliente')?.value || '';
    let registradoPor = document.getElementById('registrado_por')?.value || 'Desconocido';

    let productos = productosSeleccionados.map(p => ({
        id: p.id,
        tipo_equipo: p.tipo_equipo,
        modelo: p.modelo,
        marca: p.marca,
        imagen: p.imagen,
        cantidad: p.cantidad,
        precio_modificado: p.precio_modificado
    }));

    let planPagos = Array.from(document.getElementById('plan-pagos')?.children || []).map(p => p.textContent);

    let data = {
        cliente: {
            nombre: searchClient,
            telefono: telefono,
            email: email,
            direccion: direccion
        },
        productos: productos,
        subtotal: parseFloat(document.getElementById('subtotal')?.textContent.replace(/[$,]/g, '')) || 0,
        descuento: descuento,
        envio: envio, 
        iva: parseFloat(document.getElementById('iva')?.textContent.replace(/[$,]/g, '')) || 0,
        total: parseFloat(document.getElementById('total')?.textContent.replace(/[$,]/g, '')) || 0,
        tipo_pago: tipoPago,
        plan_pagos: planPagos,
        nota: notaCliente,
        valido_hasta: validoHasta,
        lugar_cotizacion: lugarCotizacion,
        registrado_por: registradoPor, // Se agrega el nombre del usuario autenticado
        ficha_tecnica_id: document.getElementById('ficha_tecnica_id')?.value || null // <--- ESTA LÍNEA
    };

    console.log("Enviando datos:", data); // Verifica en la consola si los datos están correctos

    fetch('/guardar-cotizacion', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.id) {
            let modalPdf = new bootstrap.Modal(document.getElementById('modalPdf'));
            modalPdf.show();

            setTimeout(() => {
                descargarPDF(data.id);
            }, 1000);
        } else {
            alert('Error al guardar la cotización.');
        }
    })
    .catch(error => console.error('Error al guardar la cotización:', error));
});


// Función para descargar el PDF sin abrir otra página
function descargarPDF(id) {
    fetch(`/descargar-cotizacion/${id}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.blob())
    .then(blob => {
        let link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `cotizacion_${id}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    })
    .catch(error => console.error('Error al descargar el PDF:', error));
}
</script>

<script>
     document.getElementById('image-upload').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewIcon = document.getElementById('preview-icon');
            const previewText = document.getElementById('preview-text');

            previewIcon.src = e.target.result;
            previewIcon.style.width = '100%';
            previewIcon.style.height = '100%';
            previewText.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
});

</script>
<script>
    function descargarPDF(id) {
        window.location.href = `/descargar-cotizacion/${id}`;
    }
</script>
<script>
    function agregarPaquete(paqueteId, productos) {
    // Recorremos los productos del paquete
    productos.forEach(producto => {
        // Primero, verificamos si el producto ya existe en la lista
        let productoExistente = productosSeleccionados.find(p => p.id === producto.id);

        if (productoExistente) {
            // Si el producto ya está en la lista, aumentamos la cantidad
            productoExistente.cantidad++;
            productoExistente.subtotal = productoExistente.cantidad * productoExistente.precio;
        } else {
            // Si no existe, lo agregamos con cantidad inicial 1
            productosSeleccionados.push({
                id: producto.id,
                tipo_equipo: producto.tipo_equipo.toUpperCase(),
                modelo: producto.modelo.toUpperCase(),
                marca: producto.marca.toUpperCase(),
                precio: parseFloat(producto.precio),
                precio_modificado: parseFloat(producto.precio),
                imagen: producto.imagen,
                cantidad: 1,
                subtotal: parseFloat(producto.precio),
            });
        }
    });

    // Actualizar la tabla y los totales
    actualizarTablaProductos();
    actualizarTotales();
}

</script>
<script>
    function agregarPaqueteDesdeData(element) {
    let id = element.getAttribute("data-id");
    let productos = JSON.parse(element.getAttribute("data-productos"));
    agregarPaquete(id, productos);
}

</script>
@endsection
