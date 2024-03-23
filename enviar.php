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

// Nombre del archivo adjunto
$nombreArchivo = $_FILES['archivo']['name'];
$archivoTemporal = $_FILES['archivo']['tmp_name'];

// Verificar si se ha proporcionado un archivo adjunto
if (!empty($nombreArchivo) && !empty($archivoTemporal)) {
    // Directorio de destino para el archivo adjunto
    $directorioDestino = 'archivos_adjuntos/';
    // Mover el archivo adjunto al directorio de destino
    if (move_uploaded_file($archivoTemporal, $directorioDestino . $nombreArchivo)) {
        // Adjuntar el archivo al correo
        $archivoAdjunto = $directorioDestino . $nombreArchivo;

        // Envío del correo al correo corporativo
        if (mail($correoCorporativo, $asunto, $cuerpoMensaje, $headers, "-f$correoCorporativo")) {
            header('Location: index.html');
        } else {
            echo '<p>Hubo un error al enviar el mensaje al correo corporativo. Por favor, inténtelo de nuevo más tarde.</p>';
        }

        // Envío del correo al correo ingresado en el formulario (si se proporcionó)
        if (!empty($correoRemitente)) {
            if (mail($correoRemitente, $asunto, $cuerpoMensaje, $headers, "-f$correoCorporativo")) {
                header('Location: index.html');
            } else {
                echo '<p>Hubo un error al enviar una copia del mensaje a tu correo electrónico. Por favor, inténtelo de nuevo más tarde.</p>';
            }
        }
    } else {
        echo '<p>Hubo un error al subir el archivo adjunto. Por favor, inténtelo de nuevo más tarde.</p>';
    }
} else {
    echo '<p>Debe seleccionar un archivo adjunto.</p>';
}


