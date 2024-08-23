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
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Productos</title>
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
    <h1>Lista de Productos</h1>

    <!-- Formulario para agregar productos -->
    <form id="productForm" method="post">
        <div class="form-group">
            <label for="clave">Clave de Producto o Servicio</label>
            <input type="text" id="clave" name="clave" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="claveUnidad">Clave de Unidad</label>
            <input type="text" id="claveUnidad" name="claveUnidad" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="cantidad">Cantidad</label>
            <input type="number" id="cantidad" name="cantidad" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="unidad">Unidad</label>
            <select id="unidad" name="unidad" class="form-control" required>
                <option value="">Seleccione</option>
                <option value="pieza">Pieza</option>
                <option value="kilogramo">Kilogramo</option>
                <option value="litro">Litro</option>
            </select>
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <input type="text" id="descripcion" name="descripcion" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="valorUnitario">Valor Unitario</label>
            <input type="number" step="0.01" id="valorUnitario" name="valorUnitario" class="form-control" required>
        </div>
        <input type="hidden" name="action" value="add">
        <button type="submit" class="btn btn-primary">Agregar Producto</button>
    </form>

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
                <th>Acciones</th>
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
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='claveEliminar' value='" . htmlspecialchars($producto['clave']) . "'>
                        <input type='hidden' name='action' value='delete'>
                        <button type='submit' class='btn-delete'>Eliminar</button>
                    </form>
                </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
