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
$cuerpoMensaje .= "Mensaje:\n$mensaje\n";

// Cabeceras del correo
$headers = "From: $nombre <$email>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".md5(time())."\"\r\n";

// Carpeta para almacenar archivos adjuntos
$carpetaAdjuntos = 'archivos_adjuntos/';

// Archivo adjunto
$nombreArchivo = $_FILES['archivo']['name'];
$archivoTemporal = $_FILES['archivo']['tmp_name'];

// Verificar si se ha proporcionado un archivo adjunto
if (!empty($nombreArchivo) && !empty($archivoTemporal)) {
    // Mover el archivo adjunto a la carpeta de archivos adjuntos
    $rutaArchivo = $carpetaAdjuntos . $nombreArchivo;
    if (move_uploaded_file($archivoTemporal, $rutaArchivo)) {
        // Leer el contenido del archivo
        $contenidoArchivo = file_get_contents($rutaArchivo);
        // Codificar el archivo adjunto en base64
        $archivoAdjunto = chunk_split(base64_encode($contenidoArchivo));

        // Definir el límite del mensaje multipart/mixed
        $boundary = "--PHP-mixed-".md5(time());

        // Construir el cuerpo del mensaje
        $mensajeAdjunto = "\n\n--$boundary\n";
        $mensajeAdjunto .= "Content-Type: application/octet-stream; name=\"$nombreArchivo\"\n";
        $mensajeAdjunto .= "Content-Transfer-Encoding: base64\n";
        $mensajeAdjunto .= "Content-Disposition: attachment\n\n";
        $mensajeAdjunto .= "$archivoAdjunto\n";
        $mensajeAdjunto .= "--$boundary--\n";

        // Agregar el mensaje adjunto al cuerpo del mensaje principal
        $cuerpoMensaje .= $mensajeAdjunto;
    } else {
        echo '<p>Hubo un error al subir el archivo adjunto. Por favor, inténtelo de nuevo más tarde.</p>';
    }
}

// Envío del correo al correo corporativo
if (mail($correoCorporativo, $asunto, $cuerpoMensaje, $headers)) {
    header('Location: index.html');
} else {
    echo '<p>Hubo un error al enviar el mensaje al correo corporativo. Por favor, inténtelo de nuevo más tarde.</p>';
}

