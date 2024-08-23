<?php
require_once __DIR__ . '/vendor/autoload.php'; // Incluye el autoload de mPDF

// Crear instancia de mPDF
$mpdf = new Mpdf\Mpdf();
$qrCode = new Mpdf\QrCode\QrCode('Información del CFDI');

// Ruta del archivo donde se almacenan los productos
$archivoProductos = 'productos.json';
// Recoger los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['Rsocial'];
    $rfc = $_POST['RFC'];
    $uso_cfdi = $_POST['Uso'];

    // Generate QR code
    $output = new Mpdf\QrCode\Output\Png();
    $qrImage = $output->output($qrCode, 100, [255, 255, 255], [0, 0, 0]);
    $qrImagePath = 'qr-code.png';
    file_put_contents($qrImagePath, $qrImage);
    
    // fecha y hora
    date_default_timezone_set('America/Mexico_City');
    $fecha = date('d-m-Y H:i:s');
    
      $productos = json_decode(file_get_contents($archivoProductos), true);
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
                <b>Datos de la empresa que expide:</b><br>
                Nombre o Razón Social<br>
                Dirección<br>
                Teléfono(s)<br>
                Página Web
            </td>
            <td colspan="2" class="header-right">
                <img src="path/to/logo.jpg" class="logo" alt="Logo de la empresa"><br>
                <b>FACTURA</b><br>
                <b>FA012016</b><br>
                <b>FOLIO FISCAL</b><br>
                XXXXXXXXXXXXXXXXXXXXXXX<br>
                <b>N° SERIE DEL CERTIFICADO</b><br>
                XXXXXXXXXXXXXXXXXXXXXXX<br>
                <b>FECHA Y HORA DE EMISIÓN</b><br>  
                '. $fecha .'
        
            </td>
        </tr>
        <tr>
            <td class="header-left" colspan="2">
                <b>Datos Emisor:</b><br>
                Nombre Fiscal<br>
                RFC, Dirección, etc.<br>
                Régimen Fiscal
            </td>
            <td class="header-left" colspan="2">
                <b>Datos Cliente Receptor:</b><br>
                Nombre Fiscal: ' . $nombre . '<br>
                RFC: ' . $rfc . '<br>
                Uso CFDI: ' . $uso_cfdi . '
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
            <td width="50%">
                <b>MÉTODO DE PAGO</b><br>
                CUENTA BANCARIA<br>
                PAGO EN UNA SOLA EXHIBICIÓN
            </td>
            <td width="50%" rowspan="3">
                <b>SUBTOTAL</b><br>
                <b>DESCUENTO</b><br>
                <b>I.V.A. 16%</b><br>
                <b>TOTAL</b>
            </td>
        </tr>
        <tr>
             <td><img src="' . $qrImagePath . '" alt="QR Code" style="width:100px;"></td>
        </tr>
        <tr>
            <td>
                <b>SELLO DIGITAL</b><br>
                XXXXXXXXXXXXXXXX<br>
                <b>SELLO DEL SAT</b><br>
                XXXXXXXXXXXXXXXX<br>
                <b>CADENA ORIGINAL DE CERTIFICACIÓN DEL SAT</b><br>
                XXXXXXXXXXXXXXXX
            </td>
        </tr>
    </table>
  
    ';
    // Agregar el contenido al PDF
    $mpdf->WriteHTML($contenido);

    // Generar el PDF
    $mpdf->Output('formulario.pdf', 'D');
}
?>
