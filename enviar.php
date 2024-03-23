<?php

// Configuración del servidor SMTP
ini_set("SMTP", "wolfstore.shop");
ini_set("smtp_port", "5847");

// Datos del formulario
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$mensaje = $_POST['mensaje'];

// Nombre del archivo adjunto
$nombreArchivo = $_FILES['archivo_adjunto']['name'];
$archivoTemporal = $_FILES['archivo_adjunto']['tmp_name'];

// Verificar si se ha proporcionado un archivo adjunto
if (!empty($nombreArchivo) && !empty($archivoTemporal)) {
    // Directorio de destino para el archivo adjunto
    $directorioDestino = 'archivos_adjuntos/';
    // Mover el archivo adjunto al directorio de destino
    if (move_uploaded_file($archivoTemporal, $directorioDestino . $nombreArchivo)) {
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
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "Content-Transfer-Encoding: 8bit\r\n";

        // Adjuntar el archivo al correo
        $adjunto = chunk_split(base64_encode(file_get_contents($directorioDestino . $nombreArchivo)));

        // Separador de mensaje
        $separator = md5(time());

        // Cabecera para el archivo adjunto
        $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"\r\n\r\n";
        $mensaje = "--" . $separator . "\r\n";
        $mensaje .= "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n";
        $mensaje .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $mensaje .= $cuerpoMensaje . "\r\n\r\n";
        $mensaje .= "--" . $separator . "\r\n";
        $mensaje .= "Content-Type: application/octet-stream; name=\"" . $nombreArchivo . "\"\r\n";
        $mensaje .= "Content-Transfer-Encoding: base64\r\n";
        $mensaje .= "Content-Disposition: attachment\r\n\r\n";
        $mensaje .= $adjunto . "\r\n\r\n";
        $mensaje .= "--" . $separator . "--";

        // Envío del correo al correo corporativo
        if (mail($correoCorporativo, $asunto, $mensaje, $headers)) {
            echo '<script>alert("Mensaje enviado correctamente");</script>';
        } else {
            echo '<script>alert("Hubo un error al enviar el mensaje al correo corporativo. Por favor, inténtelo de nuevo más tarde.");</script>';
        }

        // Envío del correo al correo ingresado en el formulario (si se proporcionó)
        if (!empty($correoRemitente)) {
            if (mail($correoRemitente, $asunto, $mensaje, $headers)) {
                echo '<script>alert("Copia del mensaje enviada correctamente");</script>';
            } else {
                echo '<script>alert("Hubo un error al enviar una copia del mensaje a tu correo electrónico. Por favor, inténtelo de nuevo más tarde.");</script>';
            }
        }

        // Eliminar el archivo adjunto después de enviar el correo
        unlink($directorioDestino . $nombreArchivo);
    } else {
        echo '<script>alert("Hubo un error al subir el archivo adjunto. Por favor, inténtelo de nuevo más tarde.");</script>';
    }
} else {
    echo '<script>alert("Debe seleccionar un archivo adjunto.");</script>';
}


