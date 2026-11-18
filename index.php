<?php
session_start();
require_once 'config.php';

if (isset($_POST['entrar'])) {
    $code = trim($_POST['codigo'] ?? '');
    if (hash('sha256', $code) === SHARED_CODE_HASH) {
        $_SESSION['acceso_recuerdos'] = true;
        header('Location: index.php'); exit;
    }
    $loginError = 'El código no es correcto. Inténtalo de nuevo.';
}
if (isset($_GET['salir'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: index.php'); exit;
}

$autorizado = !empty($_SESSION['acceso_recuerdos']);
$recuerdos = [];
if ($autorizado) {
    $stmt = $pdo->query('SELECT * FROM recuerdos ORDER BY fecha_subida DESC');
    $recuerdos = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nuestros pequeños momentos</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="ambient ambient-one"></div><div class="ambient ambient-two"></div>
<header class="topbar">
  <a class="brand" href="index.php"><span class="brand-icon">♡</span> entre nosotros</a>
  <?php if ($autorizado): ?><a class="logout" href="?salir=1">Cerrar sesión ↗</a><?php endif; ?>
</header>

<?php if (!$autorizado): ?>
<main class="login-wrap">
  <section class="login-card">
    <div class="tiny-heart">✦ &nbsp; UN ESPACIO SOLO NUESTRO &nbsp; ✦</div>
    <div class="big-heart">♡</div>
    <h1>Una historia,<br><em>mil recuerdos.</em></h1>
    <p class="intro">Un rinconcito para guardar nuestras fotos, videos y esos momentos que siempre queremos volver a ver.</p>
    <form method="POST" class="login-form">
      <label for="codigo">Nuestro código privado</label>
      <input id="codigo" name="codigo" type="password" placeholder="Escribe el código compartido" required>
      <?php if (!empty($loginError)): ?><p class="error"><?= htmlspecialchars($loginError) ?></p><?php endif; ?>
      <button type="submit" name="entrar">Entrar a nuestros recuerdos <span>→</span></button>
    </form>
    <p class="footnote">Hecho de momentos, cariño y pequeñas historias.</p>
  </section>
</main>
<?php else: ?>
<main class="main-content">
  <section class="hero">
    <div>
      <div class="eyebrow">NUESTRO ÁLBUM DIGITAL</div>
      <h1>Lo bonito de <em>nosotros.</em></h1>
      <p>Cada foto cuenta algo. Cada video nos devuelve a ese instante.</p>
    </div>
    <div class="hero-stamp"><span>♡</span><small>tú + yo<br>siempre aquí</small></div>
  </section>

  <section class="upload-panel">
    <div class="upload-copy">
      <span class="upload-symbol">＋</span>
      <div><h2>Guardar un momento</h2><p>Los dos pueden agregar recuerdos desde su celular o computadora.</p></div>
    </div>
    <form action="upload.php" method="POST" enctype="multipart/form-data" class="upload-form">
      <label class="file-picker" for="archivos">＋ Elegir fotos o videos</label>
      <input id="archivos" type="file" name="archivos[]" accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime" multiple required>
      <input type="text" name="descripcion" maxlength="500" placeholder="Escribe algo sobre este recuerdo (opcional)">
      <button type="submit">Subir recuerdos <span>↑</span></button>
      <small>Máximo 100 MB por archivo. Formatos: JPG, PNG, WEBP, GIF, MP4, WEBM y MOV.</small>
    </form>
  </section>

  <section class="gallery-heading">
    <div><div class="eyebrow">ARCHIVO DE MOMENTOS</div><h2>Nuestra colección</h2></div>
    <span class="count"><?= count($recuerdos) ?> recuerdos</span>
  </section>

  <?php if (!$recuerdos): ?>
    <div class="empty-state"><div>♡</div><h3>El primer recuerdo está por llegar</h3><p>Suban una foto o un video para comenzar su colección.</p></div>
  <?php else: ?>
    <section class="gallery">
      <?php foreach ($recuerdos as $r): ?>
        <article class="memory-card">
          <div class="media">
          <?php if ($r['tipo'] === 'imagen'): ?>
            <a href="uploads/<?= rawurlencode($r['nombre_archivo']) ?>" target="_blank">
              <img src="uploads/<?= htmlspecialchars(rawurlencode($r['nombre_archivo'])) ?>" alt="<?= htmlspecialchars($r['nombre_original']) ?>" loading="lazy">
            </a>
          <?php else: ?>
            <video controls preload="metadata">
              <source src="uploads/<?= htmlspecialchars(rawurlencode($r['nombre_archivo'])) ?>">
              Tu navegador no puede reproducir este video.
            </video>
          <?php endif; ?>
          </div>
          <div class="memory-info">
            <div><span class="memory-type"><?= $r['tipo'] === 'imagen' ? 'FOTOGRAFÍA' : 'VIDEO' ?></span>
            <h3><?= htmlspecialchars($r['descripcion'] ?: $r['nombre_original']) ?></h3></div>
            <time><?= date('d/m/Y · H:i', strtotime($r['fecha_subida'])) ?></time>
          </div>
        </article>
      <?php endforeach; ?>
    </section>
  <?php endif; ?>
  <footer>Un lugar para volver a lo que nos hace felices. <span>♡</span></footer>
</main>
<?php endif; ?>
</body>
</html>
