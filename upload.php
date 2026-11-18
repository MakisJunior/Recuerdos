<?php
session_start();
if (empty($_SESSION['acceso_recuerdos'])) {
    http_response_code(403);
    exit('Necesitas entrar al álbum antes de subir archivos.');
}
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['archivos'])) {
    header('Location: index.php'); exit;
}

$allowed = [
    'image/jpeg' => ['imagen', 'jpg'],
    'image/png' => ['imagen', 'png'],
    'image/webp' => ['imagen', 'webp'],
    'image/gif' => ['imagen', 'gif'],
    'video/mp4' => ['video', 'mp4'],
    'video/webm' => ['video', 'webm'],
    'video/quicktime' => ['video', 'mov']
];
$description = trim($_POST['descripcion'] ?? '');
$description = mb_substr($description, 0, 500);
$finfo = new finfo(FILEINFO_MIME_TYPE);
$files = $_FILES['archivos'];
$success = 0;
$errors = [];

for ($i = 0; $i < count($files['name']); $i++) {
    if ($files['error'][$i] !== UPLOAD_ERR_OK) {
        $errors[] = 'No se pudo subir ' . $files['name'][$i]; continue;
    }
    if ($files['size'][$i] > MAX_FILE_SIZE) {
        $errors[] = $files['name'][$i] . ' supera el límite de 100 MB.'; continue;
    }
    $mime = $finfo->file($files['tmp_name'][$i]);
    if (!isset($allowed[$mime])) {
        $errors[] = $files['name'][$i] . ' tiene un formato no permitido.'; continue;
    }
    [$type, $extension] = $allowed[$mime];
    $safeName = bin2hex(random_bytes(18)) . '.' . $extension;
    if (!move_uploaded_file($files['tmp_name'][$i], UPLOAD_DIR . $safeName)) {
        $errors[] = 'No se pudo guardar ' . $files['name'][$i]; continue;
    }
    $stmt = $pdo->prepare('INSERT INTO recuerdos (nombre_original, nombre_archivo, tipo, descripcion) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        mb_substr(basename($files['name'][$i]), 0, 255),
        $safeName,
        $type,
        $description
    ]);
    $success++;
}
$_SESSION['upload_message'] = $success . ' archivo(s) subido(s). ' . implode(' ', $errors);
header('Location: index.php');
exit;
