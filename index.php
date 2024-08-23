<?php
// Ruta del archivo donde se almacenan los productos
$archivoProductos = 'productos.json';

// Cargar productos desde el archivo si existe, o inicializar como un array vacío
if (file_exists($archivoProductos)) {
    $productos = json_decode(file_get_contents($archivoProductos), true);
} else {
    $productos = [];
}

// Procesar el formulario si se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            // Recoge los datos del formulario
            $cantidad = $_POST['cantidad'];
            $clave = $_POST['clave'];
            $claveUnidad = $_POST['claveUnidad'];
            $descripcion = $_POST['descripcion'];
            $unidad = $_POST['unidad'];
            $valorUnitario = $_POST['valorUnitario'];
            $importe = $cantidad * $valorUnitario;

            // Agrega el nuevo producto al array
            $productos[] = [
                'cantidad' => $cantidad,
                'clave' => $clave,
                'claveUnidad' => $claveUnidad,
                'descripcion' => $descripcion,
                'unidad' => $unidad,
                'valorUnitario' => $valorUnitario,
                'importe' => $importe
            ];
        } elseif ($_POST['action'] == 'delete') {
            // Elimina el producto seleccionado
            $claveEliminar = $_POST['claveEliminar'];
            $productos = array_filter($productos, function($producto) use ($claveEliminar) {
                return $producto['clave'] !== $claveEliminar;
            });
        }

        // Guarda los productos en el archivo
        file_put_contents($archivoProductos, json_encode($productos));

        // Redirige a la misma página para evitar reenvío del formulario
        header('Location: index.php');
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario PDF y Productos</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

    <!-- Formulario para detalles del PDF -->
    <form id="exportForm" action="" method="post">
        <fieldset class="border p-2">
            <legend class="float-none w-auto p-2">Cliente</legend>
            <div class="form-row">
                <!-- campo RFC -->
                <div class="col-md-3 mb-3">
                    <label for="RFC">RFC</label>
                    <div class="input-group">
                        <input type="text" id="RFC" class="form-control" placeholder="RFC" name="RFC" value="SAEM941218DWA" required>
                    </div>
                </div>
                <!-- campo razón social -->
                <div class="col-md-9 mb-3">
                    <label for="Rsocial">Nombre o Razón Social</label>
                    <input type="text" class="form-control" placeholder="Razón social" name="Rsocial" id="Rsocial"  value="manuel nahum santillan" required>
                </div>
                <!-- Uso CFDI -->
                <div class="col-md-12 mb-3">
                    <label>Uso CFDI</label>
                    <select class="form-select" id="Uso" name="Uso">
                        <option selected disabled value="">Elegir...</option>
                        <option value="Adquisición de mercancías">Adquisición de mercancías</option>
                        <option value="Devoluciones, descuentos o bonificaciones">Devoluciones, descuentos o bonificaciones</option>
                        <option value="Gastos en general">Gastos en general</option>
                        <option value="Construcciones">Construcciones</option>
                        <option value="Mobiliario y equipo de oficina por inversiones">Mobiliario y equipo de oficina por inversiones</option>
                        <option value="Equipo de transporte">Equipo de transporte</option>
                        <option value="Equipo de cómputo y accesorio">Equipo de cómputo y accesorios</option>
                        <option value="Dados, troqueles, moldes, matrices y herramentaL">Dados, troqueles, moldes, matrices y herramental</option>
                        <option value="Comunicaciones telefónicas">Comunicaciones telefónicas</option>
                        <option value="Comunicaciones satelitales">Comunicaciones satelitales</option>
                        <option value="Otra maquinaria y equipo">Otra maquinaria y equipo</option>
                    </select>
                </div>
            </div>
        </fieldset>
        <fieldset class="border p-2">
            <legend class="float-none w-auto p-2">Comprobante</legend>
            <div class="form-row">
                <!-- Fecha y hora de expedición -->
                <div class="col-md-4 mb-3">
                    <label>Fecha y hora de expedición se generará automáticamente</label>
                </div>
                <!-- Código postal -->
                <div class="col-md-4 mb-3">
                    <label for="codigoPostal">Código Postal</label>
                    <input type="text" class="form-control" id="codigoPostal" name="codigoPostal">
                </div>
                <!-- Moneda -->
                <div class="col-md-4 mb-3">
                    <label>Moneda</label>
                    <input type="text" class="form-control" id="moneda" name="moneda" value="MXN">
                </div>
                <!-- Forma de pago -->
                <div class="col-md-5 mb-3">
                    <label>Forma de Pago</label>
                    <select class="form-select" id="formaPago" name="formaPago">
                        <option selected disabled value="">Elegir...</option>
                        <option value="01 Efectivo">01 Efectivo</option>
                        <option value="02 Cheque nominativo">02 Cheque nominativo</option>
                        <option value="03 Transferencia electrónica de fondos">03 Transferencia electrónica de fondos</option>
                        <option value="4">04 Tarjeta de crédito</option>
                        <option value="5">05 Monedero electrónico</option>
                        <option value="6">06 Dinero electrónico</option>
                        <option value="7">08 Vales de despensa</option>
                        <option value="8">12 Dación en pago</option>
                        <option value="9">13 Pago por subrogación</option>
                        <option value="10">14 Pago por consignación</option>
                        <option value="11">15 Condonación</option>
                        <option value="12">17 Compensación</option>
                        <option value="13">23 Novación</option>
                        <option value="14">24 Confusión</option>
                        <option value="15">25 Remisión de deuda</option>
                        <option value="16">26 Prescripción o caducidad</option>
                        <option value="17">27 A satisfacción del acreedor</option>
                        <option value="18">28 Tarjeta de débito</option>
                        <option value="19">29 Tarjeta de servicios</option>
                        <option value="20">30 Aplicación de anticipos</option>
                        <option value="21">31 Intermediario pagos</option>
                        <option value="22">99 Por definir</option>
                    </select>
                </div>
                <!-- Método de pago -->
                <div class="col-md-5 mb-3">
                    <label>Método de Pago</label>
                    <select class="form-select" id="fpago" name="fpago">
                        <option selected disabled value="">Elegir...</option>
                        <option value="PPD">PPD Pago en parcialidades o diferido</option>
                        <option value="PUE">PUE Pago en una sola exhibición</option>
                    </select>
                </div>
                <!-- Tipo de cambio -->
                <div class="col-md-2 mb-3">
                    <label>Tipo de Cambio</label>
                    <input type="text" class="form-control" id="tipoCambio" name="tipoCambio" value="17.0">
                </div>
            </div>
        </fieldset>
        <!-- Botón para agregar productos -->
            <a href="add_product.php" class="btn btn-primary btn-add">
                Agregar/Eliminar Producto
            </a>
            <!-- Tabla para mostrar los productos -->
            <table>
                <thead>
                    <tr>
                        <th>Cantidad</th>
                        <th>Clave</th>
                        <th>Clave de Unidad</th>
                        <th>Descripción</th>
                        <th>Unidad</th>
                        <th>Valor Unitario</th>
                        <th>Importe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Muestra los productos en la tabla
                    foreach ($productos as $producto) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($producto['cantidad']) . "</td>";
                        echo "<td>" . htmlspecialchars($producto['clave']) . "</td>";
                        echo "<td>" . htmlspecialchars($producto['claveUnidad']) . "</td>";
                        echo "<td>" . htmlspecialchars($producto['descripcion']) . "</td>";
                        echo "<td>" . htmlspecialchars($producto['unidad']) . "</td>";
                        echo "<td>" . htmlspecialchars(number_format($producto['valorUnitario'], 2)) . "</td>";
                        echo "<td>" . htmlspecialchars(number_format($producto['importe'], 2)) . "</td>";
                        echo "<td>
                           
                        </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
  
        </table>

        <!-- Botón de envío del formulario -->
          <!-- <button type="submit" class="btn btn-success" name="action" value="save">Generar PDF</button>
          <button type="submit" class="btn btn-success" name="action" value="xml">Generar XML</button> -->
        <!-- Botones de envío del formulario -->
        <input type="hidden" id="action" name="action" value="">
        <button type="button" class="btn btn-primary" onclick="submitForm('generar_pdf3.php')">Generar PDF</button>
        <button type="button" class="btn btn-primary" onclick="submitForm('generar_xml.php')">Generar XML</button>
    </form>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function submitForm(action) {
            var form = document.getElementById('exportForm');
            form.action = action;
            form.submit();
        }
    </script>
</body>
</html>
