ALBUM DIGITAL PARA DOS - PHP + MYSQL

REQUISITOS
- PHP 8 o superior (con extensiones PDO, fileinfo y mbstring).
- MySQL/MariaDB.
- Para usarlo desde dos dispositivos en cualquier lugar, súbelo a un hosting que soporte PHP y MySQL y tenga espacio suficiente.

INSTALACIÓN EN XAMPP
1. Copia la carpeta "recuerdos_en_pareja" dentro de C:\\xampp\\htdocs\\
2. Abre http://localhost/phpmyadmin
3. Importa el archivo database.sql.
4. Revisa config.php. En XAMPP normalmente DB_USER=root y DB_PASS vacío.
5. Crea dentro del proyecto la carpeta "uploads" si no existe y asegúrate de que PHP pueda escribir en ella.
6. Abre http://localhost/recuerdos_en_pareja/

CÓDIGO PRIVADO INICIAL
cambia-esta-clave

Cambia esta clave antes de compartir el sitio. Para cambiarla, calcula el SHA-256 de tu nueva clave y reemplaza SHARED_CODE_HASH en config.php.
Puedes calcularlo en PHP con:
<?php echo hash('sha256', 'TU_NUEVA_CLAVE'); ?>

IMPORTANTE
- "localhost" solo funciona en tu propia computadora. Para que ambos entren desde redes diferentes, el sitio debe estar publicado en internet.
- No compartas la dirección del sitio ni el código privado públicamente.
- El límite configurado es 100 MB por archivo. El hosting también debe permitir ese tamaño en php.ini (upload_max_filesize y post_max_size).
- Este proyecto es una base inicial: no incluye recuperación de archivos ni copias de seguridad automáticas. Haz respaldos de la base de datos y de la carpeta uploads.
