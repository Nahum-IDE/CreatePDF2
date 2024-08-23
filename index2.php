<?php
// Ruta del archivo donde se almacenan los datos del cliente
$archivoCliente = 'cliente.json';

// Cargar cliente desde el archivo si existe, o inicializar como un array vacío
if (file_exists($archivoCliente)) {
    $cliente = json_decode(file_get_contents($archivoCliente), true);
} else {
    $cliente = [];
}

// Procesar el formulario si se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'save') {
        // Recoge los datos del formulario
        $cliente = [
            'RFC' => $_POST['rfc'],
            'Rsocial' => $_POST['nombre'],
            'Uso' => $_POST['Uso'],
            'Codigo_Postal' => $_POST['codigoPostal'],
            'Moneda' => $_POST['moneda'],
            'Forma_de_Pago' => $_POST['formaPago'],
            'Metodo_de_Pago' => $_POST['metodoPago'],
            'Tipo_de_Cambio' => $_POST['tipoCambio']
        ];

        // Guarda los datos en el archivo JSON
        file_put_contents($archivoCliente, json_encode($cliente, JSON_PRETTY_PRINT));

        echo "Datos guardados exitosamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario PDF</title>
</head>
<body>
    <form action="generar_pdf2.php" method="post">
        <fieldset class="border p-2">
        <legend  class="float-none w-auto p-2">Cliente</legend>
        <div class="form-row">
        <!-- campo rfc  -->
        <div class="col-md-3 mb-3">
            <label for="validationCustomRFC">RFC</label>
            <div class="input-group">
                <input type="text" id="RFC" class="form-control" placeholder="RFC" name="RFC" value="SAEM941218DWA" required>
            </div>
        </div>
        <!-- campo razon social -->
        <div class="col-md-9 mb-3">
            <label for="validationCustomRS">Nombre o Razon social</label>
            <input type="text" class="form-control" placeholder="Razon social" name="Rsocial" id="Rsocial" 
            ondblclick="doble_click(this.value)" value="manuel nahum santillan" required><!-- carga el valor al hacer doble click -->
        </div>
        <!-- Uso CFDI -->
        <div class="col-md-12 mb-3">
            <label>Uso CFDI</label>
            <select class="form-select" id="Uso" name="Uso" required>
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
            <legend  class="float-none w-auto p-2">Comprobante</legend>
            <div class="form-row">
                <!-- Fecha y hora de expedicion  -->
                <div class="col-md-4 mb-3">
                    <label>Fecha y hora de expedicion se generara automaticamente</label>
                </div>
                <!-- Codigo postal -->
                <div class="col-md-4 mb-3">
                    <label for="validationCustomRS">Codigo postal</label>
                    <input type="text" class="form-control">
                </div>
                <!-- Moneda -->
                <div class="col-md-4 mb-3">
                    <label>Moneda</label>
                    <input type="text" class="form-control" value="MXN"required>
                </div>
                <!-- Forma de pago -->
                <div class="col-md-5 mb-3">
                    <label>forma de pago</label>
                    <select class="form-select" required>
                        <option selected disabled value="">Elegir...</option>
                        <option value="1">01 Efectivo</option>
                        <option value="2">02 Cheque nominativo</option>
                        <option value="3">03 Transferencia electrónica de fondos</option>
                        <option value="4">04 Tarjeta de crédito</option>
                        <option value="5">05 Monedero electrónico</option>
                        <option value="6">06 Dinero electrónico</option>
                        <option value="7">08 Vales de despensa</option>
                        <option value="8">12 Dación en pago</option>
                        <option value="9">13 Pago por subrogación</option>
                        <option value="">14 Pago por consignación</option>
                        <option value="">15 Condonación</option>
                        <option value="">17 Compensación</option>
                        <option value="">23 Novación</option>
                        <option value="">24 Confusión</option>
                        <option value="">25 Remisión de deuda</option>
                        <option value="">26 Prescripción o caducidad</option>
                        <option value="">27 A satisfacción del acreedor</option>
                        <option value="">28 Tarjeta de débito</option>
                        <option value="">29 Tarjeta de servicios</option>
                        <option value="">30 Aplicación de anticipos</option>
                        <option value="">31 Intermediario pagos</option>
                        <option value="">99 Por definir</option>
                    </select>
                </div>
                <!-- Método de pago: -->
                <div class="col-md-5 mb-3">
                    <label>Método de pago</label>
                    <select class="form-select" id="fpago" name="fpago" required>
                    
                        <option selected disabled value="">Elegir...</option>
                        <option value="PPD">PPD Pago en parcialidades o diferido</option>
                        <option value="PUE">PUE Pago en una sola exhibicion</option>
                    </select>
                </div>
                <!-- Tipo de cambio -->
                <div class="col-md-2 mb-3">
                    <label>Tipo de cambio</label>
                    <input type="text" class="form-control" value="17.0" required>
                </div>
            </div>
        </fieldset>
                
        <button type="submit" name="action" value="save" >Generar PDF</button>
    </form>
</body>
</html>