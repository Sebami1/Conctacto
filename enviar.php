<?php

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
        $headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"";
        $mensaje .= "--PHP-mixed-".$random_hash."";
        $mensaje .= "Content-Type: application/octet-stream; name=\"".$nombreArchivo."\"";
        $mensaje .= "Content-Transfer-Encoding: base64";
        $mensaje .= "Content-Disposition: attachment";
        $mensaje .= $adjunto;
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

// Envío del correo al correo ingresado en el formulario (si se
