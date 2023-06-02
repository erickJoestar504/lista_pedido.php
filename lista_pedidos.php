<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
}
require_once 'config.php';

$sql = "SELECT pedidos.*, usuarios.username FROM pedidos JOIN usuarios ON pedidos.usuario_id = usuarios.id ORDER BY pedidos.fecha_creacion DESC";
$result = $conn->query($sql);
if(!$result) {
    die("Error en la consulta: " . mysqli_error($conn));
}
$pedidos = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de pedidos</title>
   
    <style>
    .modal-dialog {
        max-width: 400px;
    }
    .modal-header {
        background-color: #007bff;
        color: white;
    }
    .modal-title {
        font-weight: bold;
    }
    .modal-label {
        font-weight: bold;
        color: #007bff;
        margin-right: 5px;
        margin-top: px; /* Added to align the labels with the inputs */
    }
    .modal-input {
        margin-bottom: 10px !important; /* Reduced to bring the inputs closer together */
        background-color: white !important;
        margin-top: -10px !important; 
        height: 40px !important;
        padding: 5px !important;
    }
</style>

    <!-- Bootstrap CSS -->
    <!-- Agregar link a Bootstrap CSS aquí -->

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.4/datatables.min.css"/>

    <!-- DataTables JavaScript -->
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.4/datatables.min.js"></script>

    <!-- Responsive DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css"/>

    </head>
<body>
<?php include 'nav.php'; ?>

<div class="container my-4">
    <div class="row">
        <div class="col-md-8">
            <h1 class="header mb-4"><i class="fas fa-list-ul"></i> Lista Pedidos </h1>
        </div>
        <div class="col-md-4">
            <div class="input-group mb-4">
                <input type="text" id="search-input" class="form-control" placeholder="Buscar Pedido...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>
    </div>

    <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#newOrderModal">
        <i class="fas fa-plus"></i> Agregar nuevo pedido
    </button>

    <div class="table-responsive"> 
    <table id="tabla-pedidos" class="table table-hover table-sm">
        <thead class="table-light">
            <tr>
                <th class="d-none d-md-table-cell"><i class="fas fa-list-ol"></i> </th>
                <th><i class="fas fa-user-alt"></i> Cliente</th>
                <th class="d-none d-md-table-cell"><i class="fas fa-map-marker-alt"></i> Dirección</th>
                <th class="d-none d-md-table-cell"><i class="fas fa-clock"></i> Fecha</th>
                <th><i class="fas fa-truck-loading"></i> Estado</th>
                <th class="d-none d-md-table-cell"><i class="fas fa-user"></i> Creado Por:</th>
                <th><i class="fas fa-cog"></i> Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($pedidos as $row): ?>
            <tr class="context-menu-one" data-id="<?php echo $row['id']; ?>">
                <td class="d-none d-md-table-cell"><?php echo $row['id']; ?></td>
                <td><?php echo $row['nombre_cliente']; ?></td>
                <td class="d-none d-md-table-cell"><?php echo $row['direccion']; ?></td>
                <td class="d-none d-md-table-cell"><?php echo $row['fecha_creacion']; ?></td>
                <td>
                    <?php 
                        if ($row['estado'] == 'ENVIADO') {
                            echo "<i class='fas fa-truck-loading' style='color: orange;'></i> ENVIADO";
                        } elseif ($row['estado'] == 'PENDIENTE') {
                            echo "<i class='fas fa-hourglass-half' style='color: red;'></i> PENDIENTE";
                        } elseif ($row['estado'] == 'ENTREGADO') {
                            echo "<i class='fas fa-check-circle' style='color: green;'></i> ENTREGADO";
                        }
                    ?>
                </td>
                <td class="d-none d-md-table-cell"><?php echo $row['username']; ?></td>
                <td>
                    <!-- Aquí se elimina el contenido original de las acciones -->
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>


</div>


</div>


<!-- Modal para nuevo pedido ----------------------------------------------------------->
<div class="modal fade" id="newOrderModal" tabindex="-1" aria-labelledby="newOrderModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newOrderModalLabel">Nuevo Pedido</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-pedido" action="crear_pedido.php" method="POST" class="needs-validation" novalidate>
                            <div class="form-group">
                                <label for="nombre_cliente"><i class="fas fa-user"></i> Nombre del cliente:</label>
                                <input type="text" name="nombre_cliente" id="nombre_cliente" class="form-control" required>
                                <div class="invalid-feedback">
                                    Por favor, rellene este campo.
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="direccion"><i class="fas fa-map-marker-alt"></i> Dirección:</label>
                                <input type="text" name="direccion" id="direccion" class="form-control" required>
                                <div class="invalid-feedback">
                                    Por favor, rellene este campo.
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tipo_factura"><i class="fas fa-file-invoice"></i> Tipo de factura:</label>
                                <select name="tipo_factura" id="tipo_factura" class="form-control" required>
                                    <option value="FACTURA SISTEMA">FACTURA SISTEMA</option>
                                    <option value="FACTURA TALONARIO">FACTURA TALONARIO</option>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor, seleccione una opción.
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="numero_factura"><i class="fas fa-hashtag"></i> Número de factura:</label>
                                <input type="number" name="numero_factura" id="numero_factura" class="form-control" required>
                                <div class="invalid-feedback">
                                    Por favor, rellene este campo.
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="sucursal"><i class="fas fa-store"></i> Sucursal:</label>
                                <select name="sucursal" id="sucursal" class="form-control" required>
                                    <option value="SAN LORENZO">SAN LORENZO</option>
                                    <option value="NACAOME">NACAOME</option>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor, seleccione una opción.
                                </div>
                            </div>

                            <input type="hidden" name="estado" id="estado" value="pendiente">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" form="form-pedido" class="btn btn-primary">Registrar pedido</button>
                    </div>
                </div>
            </div>
        </div>
    </div>




<!-- Modal para editar pedidos ---------------------------------------------------------->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <!-- Campo oculto para el ID del pedido -->
                    <input type="hidden" id="edit-id" name="id">

                    <div class="form-group row">
                        <div class="col">
                            <label for="edit-nombre_cliente" class="modal-label"><i class="fas fa-user"></i> Nombre Cliente:</label>
                            <input type="text" id="edit-nombre_cliente" name="nombre_cliente" class="form-control modal-input">
                        </div>
                        <div class="col">
                            <label for="edit-direccion" class="modal-label"><i class="fas fa-map-marker-alt"></i> Dirección:</label>
                            <input type="text" id="edit-direccion" name="direccion" class="form-control modal-input">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col">
                            <label for="edit-tipo_factura" class="modal-label"><i class="fas fa-file-invoice"></i> Tipo de Factura:</label>
                            <select id="edit-tipo_factura" name="tipo_factura" class="form-control modal-input">
                                <option value="FACTURA SISTEMA">FACTURA SISTEMA</option>
                                <option value="FACTURA TALONARIO">FACTURA TALONARIO</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="edit-numero_factura" class="modal-label"><i class="fas fa-hashtag"></i> Factura:</label>
                            <input type="text" id="edit-numero_factura" name="numero_factura" class="form-control modal-input">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col">
                            <label for="edit-sucursal" class="modal-label"><i class="fas fa-store"></i> Sucursal:</label>
                            <select id="edit-sucursal" name="sucursal" class="form-control modal-input">
                                <option value="SAN LORENZO">SAN LORENZO</option>
                                <option value="NACAOME">NACAOME</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="edit-estado" class="modal-label"><i class="fas fa-sync-alt"></i> Estado:</label>
                            <select id="edit-estado" name="estado" class="form-control modal-input">
                                <option value="ENTREGADO">ENTREGADO</option>
                                <option value="ENVIADO">ENVIADO</option>
                                <option value="PENDIENTE">PENDIENTE</option>
                            </select>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle del Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-2">
                    <div class="col-6">
                        <p><i class="fas fa-list-ol"></i> <label class="modal-label">ID: </label></p>
                        <input id="detail-id" class="form-control modal-input" type="text" readonly>
                    </div>
                    <div class="col-6">
                        <p><i class="fas fa-user-alt"></i> <label class="modal-label">Cliente: </label></p>
                        <input id="detail-nombre_cliente" class="form-control modal-input" type="text" readonly>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-6">
                        <p><i class="fas fa-map-marker-alt"></i> <label class="modal-label">Dirección: </label></p>
                        <input id="detail-direccion" class="form-control modal-input" type="text" readonly>
                    </div>
                    <div class="col-6">
                        <p><i class="fas fa-clock"></i> <label class="modal-label">Fecha: </label></p>
                        <input id="detail-fecha_creacion" class="form-control modal-input" type="text" readonly>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-6">
                        <p><i class="fas fa-user"></i> <label class="modal-label">Creado por: </label></p>
                        <input id="detail-username" class="form-control modal-input" type="text" readonly>
                    </div>
                    <div class="col-6">
                        <p><i class="fas fa-receipt"></i> <label class="modal-label">Tipo de factura: </label></p>
                        <input id="detail-tipo_factura" class="form-control modal-input" type="text" readonly>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-6">
                        <p><i class="fas fa-hashtag"></i> <label class="modal-label">Número factura: </label></p>
                        <input id="detail-numero_factura" class="form-control modal-input" type="text" readonly>
                    </div>
                    <div class="col-6">
                        <p><i class="fas fa-store"></i> <label class="modal-label">Sucursal: </label></p>
                        <input id="detail-sucursal" class="form-control modal-input" type="text" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <p><i class="fas fa-map-pin"></i> <label class="modal-label">Localizador: </label><a id="detail-localizador" href="">Enlace</a></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>



<script>
$(document).ready(function() {
    var table = $('#tabla-pedidos').DataTable({
        "autoWidth": false,
        "columns": [
            { "width": '1%' },  // ID
            { "width": '15%' },  // Cliente
            { "width": '30%' },  // Dirección
            { "width": '15%' },  // Fecha
            { "width": '12%' },  // Estado
            { "width": '12%' },  // Creado Por
            { "width": '15%' }   // Acciones
        ],
        "dom": 'lrtip',
        "language": {
            "lengthMenu": "Mostrando _MENU_ entradas por página",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "paginate": {
                "first":      "Primero",
                "last":       "Último",
                "next":       "Siguiente",
                "previous":   "Anterior"
            }
        },
    });

    // Add the custom search functionality
    $('#search-input').on('keyup', function() {
        table.search(this.value).draw();
    });
});
</script>






<script>
$('#editForm').on('submit', function(e) {
    e.preventDefault();

    // Obtén todos los valores de los campos del formulario
    var formData = $(this).serializeArray();
    var data = {};
    $.each(formData, function(i, field) {
        data[field.name] = field.value;
    });

    console.log(data); 

    $.ajax({
        url: 'actualizar_pedido.php',
        method: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Cerrar el modal
                $('#editModal').modal('hide');

                // Muestra una notificación de éxito
                Swal.fire({
                    icon: 'success',
                    title: 'Pedido actualizado',
                    text: 'El pedido ha sido actualizado con éxito',
                });
                setTimeout(function(){ location.reload(); }, 1000);

                // Aquí podrías recargar la tabla o hacer lo que sea necesario para mostrar el pedido actualizado
            } else {
                // Muestra una notificación de error
                Swal.fire({
                    icon: 'error',
                    title: 'Error al actualizar',
                    text: 'Ocurrió un error al intentar actualizar el pedido',
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error(textStatus, errorThrown);
            Swal.fire({
                icon: 'error',
                title: 'Error al actualizar',
                text: 'Ocurrió un error al intentar actualizar el pedido',
            });
        }
    });
});
</script>


<div id="contextMenu" class="dropdown clearfix" style="display:none; position: fixed;">
    <ul class="dropdown-menu p-2" role="menu" aria-labelledby="dropdownMenu" style="display:block; position:static; margin-bottom:5px;">
        <li><a tabindex="-1" href="#" id="detailModal" data-bs-toggle="modal" data-bs-target="#detailModal" class="dropdown-item"><i class="fas fa-info-circle me-2"></i> Detalle</a></li>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <li><a tabindex="-1" href="#" id="btnEditar" class="btnEditar dropdown-item" data-bs-toggle="modal" data-bs-target="#editModal" ><i class="fas fa-edit me-2"></i> Editar</a></li>
            <li><a tabindex="-1" href="#" id="btnEliminar" class="btnEliminar dropdown-item"><i class="fas fa-trash-alt me-2"></i> Eliminar</a></li>
        <?php endif; ?>
    </ul>
</div>

<script>
    $(document).ready(function() {
        var table = $('#tabla-pedidos').DataTable();

        $('.context-menu-one').on('contextmenu', function(e) {
            e.preventDefault();

            let contextMenu = $('#contextMenu');
            contextMenu.css('display', 'block');
            let clickCoords = { x: e.clientX, y: e.clientY };
            let clickCoordsX = clickCoords.x;
            let clickCoordsY = clickCoords.y;

            let menuWidth = contextMenu.outerWidth() + 4;
            let menuHeight = contextMenu.outerHeight() + 4;

            let windowWidth = $(window).width();
            let windowHeight = $(window).height();

            if ( (windowWidth - clickCoordsX) < menuWidth ) {
                contextMenu.css('left', (windowWidth - menuWidth) + "px");
            } else {
                contextMenu.css('left', clickCoordsX + "px");
            }

            if ( (windowHeight - clickCoordsY) < menuHeight ) {
                contextMenu.css('top', (windowHeight - menuHeight) + "px");
            } else {
                contextMenu.css('top', clickCoordsY + "px");
            }

            let id = $(this).data('id');
            let detailModal = $('#detailModal');
            detailModal.data('id', id);
            $('#btnEditar').data('id', id);
            $('#btnEliminar').data('id', id);
        });

        $(window).on('click', function(e) {
            $('#contextMenu').css('display', 'none');
        });

         // Controlador del evento que se desencadena al mostrar el modal de edición.
         $('#editModal').on('show.bs.modal', function(e) {
            var id = $(e.relatedTarget).data('id');

            $.ajax({
                url: 'get_pedido.php',
                method: 'GET',
                data: {
                    id: id
                },
                success: function(response) {
                    var result = typeof response === 'string' ? JSON.parse(response) : response;

                    if (result.error) {
                        // Mostrar error
                    } else {
                        $('#edit-id').val(result.id);
                        $('#edit-nombre_cliente').val(result.nombre_cliente);
                        $('#edit-direccion').val(result.direccion);
                        $('#edit-fecha_creacion').val(result.fecha_creacion);
                        $('#edit-username').val(result.username);
                        $('#edit-tipo_factura').val(result.tipo_factura);
                        $('#edit-numero_factura').val(result.numero_factura);
                        $('#edit-sucursal').val(result.sucursal);
                        $('#edit-localizador').val(result.localizador);
                    }
                }
            });
        });

        // Controlador del evento que se desencadena al mostrar el modal de detalles.
        $('#detailModal').on('show.bs.modal', function(e) {
            var id = $('#detailModal').data('id');

            $.ajax({
                url: 'get_pedido.php',
                method: 'GET',
                data: {
                    id: id
                },
                success: function(response) {
                    var result = typeof response === 'string' ? JSON.parse(response) : response;
                    console.log(response);
                    if (result.error) {
                        // Mostrar error
                    } else {
                        $('#detail-id').val(result.id);
                        $('#detail-nombre_cliente').val(result.nombre_cliente);
                        $('#detail-direccion').val(result.direccion);
                        $('#detail-fecha_creacion').val(result.fecha_creacion);
                        $('#detail-username').val(result.username);
                        $('#detail-tipo_factura').val(result.tipo_factura);
                        $('#detail-numero_factura').val(result.numero_factura);
                        $('#detail-sucursal').val(result.sucursal);
                        $('#detail-localizador').val(result.localizador);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Manejar error
                    console.log(textStatus, errorThrown);
                }
            });
        });

       
        // Asegúrate de que los eventos se adjunten después de que se recargue la tabla.
table.on('draw', function(){
    $('.context-menu-one').on('contextmenu', function(e) {
        e.preventDefault();

        let contextMenu = $('#contextMenu');
        contextMenu.css('display', 'block');
        let clickCoords = { x: e.clientX, y: e.clientY };
        let clickCoordsX = clickCoords.x;
        let clickCoordsY = clickCoords.y;

        let menuWidth = contextMenu.outerWidth() + 4;
        let menuHeight = contextMenu.outerHeight() + 4;

        let windowWidth = $(window).width();
        let windowHeight = $(window).height();

        if ( (windowWidth - clickCoordsX) < menuWidth ) {
            contextMenu.css('left', (windowWidth - menuWidth) + "px");
        } else {
            contextMenu.css('left', clickCoordsX + "px");
        }

        if ( (windowHeight - clickCoordsY) < menuHeight ) {
            contextMenu.css('top', (windowHeight - menuHeight) + "px");
        } else {
            contextMenu.css('top', clickCoordsY + "px");
        }

        let id = $(this).data('id');
        let detailModal = $('#detailModal');
        detailModal.data('id', id);
        $('#btnEditar').data('id', id);
        $('#btnEliminar').data('id', id);
    });
});

    });
</script>








<script>
  $(document).ready(function() {
    $('.btnEliminar').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        if (id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '¡Sí, bórralo!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'eliminar_pedido.php',
                        method: 'GET',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            var result = typeof response === 'string' ? JSON.parse(response) : response;

                            if (result.exito) {
                                Swal.fire(
                                    '¡Eliminado!',
                                    'El producto ha sido eliminado.',
                                    'success'
                                )
                                setTimeout(function(){ location.reload(); }, 1000);
                            } else {
                                Swal.fire(
                                    'Error',
                                    'Ocurrió un error al eliminar el producto.',
                                    'error'
                                )
                            }
                        }
                    });
                }
            })
        } else {
            console.error('No se pudo obtener el ID del pedido.');
        }
    });
});

$('#editModal, #detailModal').on('hidden.bs.modal', function () {
    // Actualizar atributos al cerrar el modal
    $('#btnEditar, #btnEliminar, #detailModal').data('id', '');
    $('#detailModal').attr('data-bs-target', '');
});

</script>

<script>
    //scrip para un nuevo pedido -----------------------------------
document.getElementById('form-pedido').addEventListener('submit', function(event) {
    event.preventDefault();

    let inputs = document.querySelectorAll('#form-pedido input, #form-pedido select');
    let isValid = true;

    for(let i = 0; i < inputs.length; i++) {
        if(inputs[i].value.trim() === '') {
            isValid = false;
            break;
        }
    }

    if (!isValid) {
        Swal.fire({
            icon: 'error',
            title: 'Campos vacíos',
            text: 'Por favor complete todos los campos obligatorios',
        });
    } else {
        // Si todos los campos están llenos, envía el formulario usando AJAX
        let formData = new FormData(this);

        fetch(this.action, {
            method: this.method,
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Pedido realizado correctamente',
                    text: data.success,
                    willClose: () => {
                        window.location.href = 'lista_pedidos.php';
                    }
                });
            } else if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error,
                });
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }
});

</script>







<?php include 'footer.php'; ?>
</body>
</html>
