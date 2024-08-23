<?php
// Ruta del archivo donde se almacenan los productos
$archivoProductos = 'productos.json';

// Procesar datos del formulario al recibir una solicitud POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $nombre = $_POST['Rsocial'];
    $rfc = $_POST['RFC'];
    $uso_cfdi = $_POST['Uso'];
    $fpago = $_POST['formaPago'];

    // Determinar el método de pago en función de la forma de pago
    $mpago = ($fpago == "PPD") ? 'PPD Pago en parcialidades o diferido' : 'PUE Pago en una sola exhibición';

    // Leer y decodificar los datos JSON de productos
    $productos = json_decode(file_get_contents($archivoProductos), true);

    // Establecer la zona horaria y obtener la fecha y hora actual
    date_default_timezone_set('America/Mexico_City');
    $fecha = date('d-m-Y H:i:s');

    // Crear el objeto SimpleXMLElement
    $xml = new SimpleXMLElement('<formulario></formulario>');

    // Agregar elementos al XML
    $xml->addChild('Fecha', htmlspecialchars($fecha));
    $xml->addChild('Nombre', htmlspecialchars($nombre));
    $xml->addChild('RFC', htmlspecialchars($rfc));
    $xml->addChild('UsoCFDI', htmlspecialchars($uso_cfdi));
    $xml->addChild('FormaDePago', htmlspecialchars($fpago));
    $xml->addChild('MetodoDePago', htmlspecialchars($mpago));

    // Inicializar el subtotal
    $subtotales = 0;

    // Agregar cada producto al XML
    foreach ($productos as $producto) {
        $subtotales += $producto['importe'];

        $productoElement = $xml->addChild('Producto');
        $productoElement->addChild('Cantidad', htmlspecialchars($producto['cantidad']));
        $productoElement->addChild('ClaveDelProducto', htmlspecialchars($producto['clave']));
        $productoElement->addChild('ClaveDeLaUnidad', htmlspecialchars($producto['claveUnidad']));
        $productoElement->addChild('DescripcionDelProducto', htmlspecialchars($producto['descripcion']));
        $productoElement->addChild('Unidad', htmlspecialchars($producto['unidad']));
        $productoElement->addChild('ValorUnitario', htmlspecialchars(number_format($producto['valorUnitario'], 2)));
        $productoElement->addChild('Importe', htmlspecialchars(number_format($producto['importe'], 2)));
    }

    // Agregar subtotal, IVA y total al XML
    $xml->addChild('Subtotal', htmlspecialchars(number_format($subtotales, 2)));
    $xml->addChild('IVA', htmlspecialchars(number_format($subtotales * 0.16, 2)));
    $xml->addChild('Total', htmlspecialchars(number_format($subtotales * 1.16, 2)));

    // Función para formatear el XML con indentación
    function formatXML($xmlString) {
        $xml = new DOMDocument();
        $xml->loadXML($xmlString);
        $xml->formatOutput = true;
        return $xml->saveXML();
    }

    // Obtener el XML formateado
    $xmlString = $xml->asXML();
    $formattedXml = formatXML($xmlString);

    // Nombre del archivo XML
    $filename = 'formulario.xml';

    // Guardar el XML en un archivo
    file_put_contents($filename, $formattedXml);

    // Configurar los encabezados para la descarga del archivo
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Length: ' . filesize($filename));

    // Leer y enviar el contenido del archivo XML
    readfile($filename);

    // Eliminar el archivo después de la descarga (opcional)
    unlink($filename);

    // Terminar el script después de la descarga
    exit;
}
?>
