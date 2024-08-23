<?php
require_once __DIR__ . '/vendor/autoload.php'; // Incluye el autoload de mPDF

// // Crear instancia de mPDF
$mpdf = new Mpdf\Mpdf();
$qrCode = new Mpdf\QrCode\QrCode('Información del CFDI');

// Ruta del archivo donde se almacenan los productos
$archivoProductos = 'productos.json';

// Desactivar la visualización de errores para evitar salidas inesperadas
ini_set('display_errors', '0');
error_reporting(0);

// Iniciar el buffer de salida
ob_start();


// Recoger los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['Rsocial'];
    $rfc = $_POST['RFC'];
    $uso_cfdi = $_POST['Uso'];
    // $fpago  = $_POST['fpago'];
    
    if ( $_POST['fpago'] = "PPD" ){
         $fpago = 'Pago en parcialidades o diferido';
         $mpago = 'PPD';
     }else{
         $fpago = 'Pago en una sola exhibiciones';
         $mpago = 'PUE';
     }

     $productos = json_decode(file_get_contents($archivoProductos), true);
   
    // Generate QR code
    $output = new Mpdf\QrCode\Output\Png();
    $qrImage = $output->output($qrCode, 100, [255, 255, 255], [0, 0, 0]);
    $qrImagePath = 'qr-code.png';
    file_put_contents($qrImagePath, $qrImage);

    // fecha y hora
    date_default_timezone_set('America/Mexico_City');
    $fecha = date('d-m-Y H:i:s');


// Add the CSS for the table layout and styling
$stylesheet = "
    body {font-family: Arial, sans-serif;}
    table {width: 100%; border-collapse: collapse;}
    .header-table, .content-table, .footer-table {border: 1px solid #000; margin-bottom: 10px;}
    .header-table th, .header-table td, .content-table th, .content-table td, .footer-table th, .footer-table td {border: 1px solid #000; padding: 5px;}
    .header-left, .header-right {width: 50%;}
    .header-left {vertical-align: top;}
    .header-right {text-align: right;}
    .content-table th, .content-table td {text-align: center;}
    .footer-table td {padding: 10px;}
    .logo {width: 100px; vertical-align: middle;}
    .sub-total, .iva, .total {text-align: right;}
";
$mpdf->WriteHTML($stylesheet, 1);

// Create the header table
    $contenido = '
<table class="header-table">
    <tr>
        <td colspan="2" class="header-left">
            <img src="path/to/elektra-logo.jpg" class="logo" alt="Elektra Logo"><br>
            <b>Nueva Elektra del Milenio SA de CV</b><br>
            Subsidiaria de Grupo Elektra<br>
            AVENIDA FFCC DE RIO FRIO 419 BW Col.CUCHILLA DEL MORAL, IZTAPALAPA, CIUDAD DE MEXICO, MEXICO CP: 09319<br>
            <b>RFC: ECE9610253TA</b>
        </td>
        <td colspan="2" class="header-right">
            <b>FACTURA NO.</b><br>
            <b>CFDI - 18657790</b><br>
            <b>REGIMEN FISCAL</b><br>
            623<br>
            <b>PEDIDO</b><br>
            1168353<br>
            <b>FECHA DE EMISION</b>'. $fecha .'<br>
            <b>TIPO DE MONEDA</b> MXN <br>
            MXN
        </td>
    </tr>
    <tr>
        <td class="header-left" colspan="2">
            <b>Nombre Fiscal:</b> ' . $nombre . '<br>
            <b>RFC:</b> ' . $rfc . '<br>
            <b>USO CFDI:</b> ' . $uso_cfdi . '<br>
            <b>LUGAR DE EXPEDICION:</b> 09010
        </td>
    </tr>
</table>
';

// Add the content table for product details
  
            // Construir el HTML para la tabla
            $tabla = '
            <table class="content-table">
                <thead>
                    <tr>
                        <th width="10%">CANTIDAD</th>
                        <th width="20%">CLAVE DE PROD.</th>
                        <th width="40%">DESCRIPCION</th>
                        <th width="10%">U DE M</th>
                        <th width="10%">P. UNITARIO</th>
                        <th width="10%">IMPORTE</th>
                    </tr>
                </thead>
                <tbody>
            ';
            $subtotales = 0;
            // Agregar filas a la tabla
            foreach ($productos as $producto) {
                $subtotales += $producto['importe'];
                $tabla .= "<tr>";
                $tabla .= "<td>" . htmlspecialchars($producto['cantidad']) . "</td>";
                $tabla .= "<td>" . htmlspecialchars($producto['clave']) . "</td>";
                $tabla .= "<td>" . htmlspecialchars($producto['descripcion']) . "</td>";
                $tabla .= "<td>" . htmlspecialchars($producto['udem']) . "</td>";
                $tabla .= "<td>" . htmlspecialchars(number_format($producto['preciounitario'], 2)) . "</td>";
                $tabla .= "<td>" . htmlspecialchars(number_format($producto['importe'], 2)) . "</td>";
                $tabla .= "</tr>";
            }

            // Cerrar la tabla
            $tabla .= '
                </tbody>
            </table>
            ';

            // Agregar la tabla al contenido
            $contenido .= $tabla;


        // Add the footer table
        $contenido .= '
        <table class="footer-table">
            <tr>
                <td width="30%" rowspan="3">
                    <b>MÉTODO DE PAGO</b>
                    <br><br>
                    <b>FORMA DE PAGO</b>
                </td>
                <td width="40%" rowspan="3">
                    <br>'. $mpago .'<br>
                    <br>'. $fpago .'<br>
                </td>
                <td width="30%" rowspan="3">
                    <b>SUBTOTAL </b> '. $subtotales.'<br>
                    <b>I.V.A. 16% </b> '. $subtotales*.16.'<br>
                    <b>TOTAL </b> '. $subtotales*1.16.'<br>
                </td>

            </tr>
        </table>
        ';


// Add the digital seal section
$contenido.= '
    <div>
        <img src="' . $qrImagePath . '" alt="QR Code" style="width:130px; float: left;">
        <b>FOLIO FISCAL:</b> B3CF3DA4-0BC3-4FDA-8F72-0C762FB2491<br>
        <b>FECHA Y HORA DE CERTIFICACION:</b> '. $fecha .'<br>
        <b>NO. CERTIFICADO DIGITAL:</b> 00001000000404345624996<br>                                                  <b>SELLO DIGITAL DEL EMISOR:</b> XXXXXXXXXXXXXXXXXXXXXXXXXXXXX<br>
        <b>SELLO DIGITAL DEL SAT:</b> XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX<br>
        <b>CADENA ORIGINAL DE CERTIFICACION DEL SAT:</b> XXXXXXXXXXXXXX<br>
    </div>'
    ;


    // Captura el contenido del buffer
    $html = ob_get_clean();

    // // Cargar mPDF
    // require_once __DIR__ . '/vendor/autoload.php';

    $mpdf = new \Mpdf\Mpdf();

    // Agregar el contenido al PDF
    $mpdf->WriteHTML($contenido);
    $mpdf->WriteHTML($html);

    // Limpiar cualquier salida previa para evitar conflictos
    ob_end_clean();


// Generar el PDF
$mpdf->Output('formulario.pdf', 'D');
    }
?>
