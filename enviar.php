<?php

$random_hash = md5(date('r', time()));


ini_set("SMTP", "wolfsotre.shop");
ini_set("smtp_port", "5847"); 

// Datos del formulario
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$mensaje = $_POST['mensaje'];

// Correo corporativo al que se enviará el mensaje
$correoCorporativo = 'sebami@wolfstore.shop';

// Correo del remitente para enviar una copia
$correoRemitente = $_POST['correo_remitente'];

// Asunto del correo
$asunto = 'Nuevo mensaje desde formulario de contacto';

// Construir el cuerpo del mensaje
$cuerpoMensaje = "Nombre: $nombre\n";
$cuerpoMensaje .= "Email: $email\n";
$cuerpoMensaje .= "Mensaje:\n$mensaje";

// Cabeceras del correo
$headers = "From: $nombre <$email>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Carpeta para almacenar archivos adjuntos
$carpetaAdjuntos = 'archivos_adjuntos/';

// Archivo adjunto
$nombreArchivo = $_FILES['archivo']['name'];
$archivoTemporal = $_FILES['archivo']['tmp_name'];

// Mover el archivo adjunto a la carpeta de archivos adjuntos
if (!empty($nombreArchivo) && !empty($archivoTemporal)) {
    $rutaArchivo = $carpetaAdjuntos . $nombreArchivo;
    if (move_uploaded_file($archivoTemporal, $rutaArchivo)) {
        // Adjuntar el archivo al correo
        $adjunto = chunk_split(base64_encode(file_get_contents($rutaArchivo)));
        $headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-$random_hash\"\r\n";
        $mensaje = "--PHP-mixed-$random_hash\r\n";
        $mensaje .= "Content-Type: multipart/alternative; boundary=\"PHP-alt-$random_hash\"\r\n\r\n";
        $mensaje .= "--PHP-alt-$random_hash\r\n";
        $mensaje .= "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n";
        $mensaje .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $mensaje .= $cuerpoMensaje . "\r\n\r\n";
        $mensaje .= "--PHP-alt-$random_hash--\r\n\r\n";
        $mensaje .= "--PHP-mixed-$random_hash\r\n";
        $mensaje .= "Content-Type: application/octet-stream; name=\"$nombreArchivo\"\r\n";
        $mensaje .= "Content-Transfer-Encoding: base64\r\n";
        $mensaje .= "Content-Disposition: attachment\r\n\r\n";
        $mensaje .= $adjunto . "\r\n\r\n";
        $mensaje .= "--PHP-mixed-$random_hash--";
    } else {
        echo '<p>Hubo un error al subir el archivo adjunto. Por favor, inténtelo de nuevo más tarde.</p>';
    }
}

// Envío del correo al correo corporativo
if (mail($correoCorporativo, $asunto, $mensaje, $headers)) {
    header('Location: index.html');
} else {
    echo '<p>Hubo un error al enviar el mensaje al correo corporativo. Por favor, inténtelo de nuevo más tarde.</p>';
}

// Envío del correo al correo ingresado en el formulario (si se proporcionó)
if (!empty($correoRemitente)) {
    if (mail($correoRemitente, $asunto, $cuerpoMensaje, $headers)) {
        header('Location: index.html');
    } else {
        echo '<p>Hubo un error al enviar una copia del mensaje a tu correo electrónico. Por favor, inténtelo de nuevo más tarde.</p>';
    }
}


