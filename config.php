<?php
// Cambia estos datos según tu instalación de MySQL.
define('DB_HOST', 'localhost');
define('DB_NAME', 'recuerdos_pareja');
define('DB_USER', 'root');
define('DB_PASS', '');

// Cambia esta clave y compártela solamente con tu pareja.
// Para cambiarla, sustituye el texto por otra clave y vuelve a generar su SHA-256.
define('SHARED_CODE_HASH', '795971b66d9e348eb9c54ace51ec4fd08ad2a4530208021e4c9c698738c96edc');

define('MAX_FILE_SIZE', 100 * 1024 * 1024); // 100 MB
define('UPLOAD_DIR', __DIR__ . '/uploads/');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('No se pudo conectar con la base de datos. Revisa config.php y crea la base de datos.');
}

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
