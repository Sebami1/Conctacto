<?php

// Configuración del servidor SMTP
ini_set("SMTP", "mail.wolfstore.shop");
ini_set("smtp_port", "5847");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se ha proporcionado un archivo adjunto
    if (!empty($_FILES['archivo']['name'])) {
        // Datos del formulario
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $mensaje = $_POST['mensaje'];

        // Nombre del archivo adjunto y su ubicación temporal
        $nombreArchivo = $_FILES['archivo']['name'];
        $archivoTemporal = $_FILES['archivo']['tmp_name'];

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
            $headers .= "X-Mailer: PHP/" . phpversion();
            $headers .= "Content-Type: multipart/mixed; boundary=\"boundary\"\r\n";

            // Adjuntar el archivo al correo
            $mensaje = "--boundary\r\n";
            $mensaje .= "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n";
            $mensaje .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $mensaje .= $cuerpoMensaje . "\r\n\r\n";
            $mensaje .= "--boundary\r\n";
            $mensaje .= "Content-Type: application/octet-stream; name=\"" . $nombreArchivo . "\"\r\n";
            $mensaje .= "Content-Transfer-Encoding: base64\r\n";
            $mensaje .= "Content-Disposition: attachment; filename=\"" . $nombreArchivo . "\"\r\n\r\n";
            $mensaje .= chunk_split(base64_encode(file_get_contents($directorioDestino . $nombreArchivo))) . "\r\n\r\n";
            $mensaje .= "--boundary--";

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
}




