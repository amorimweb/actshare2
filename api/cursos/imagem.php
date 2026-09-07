<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();
$file = $_FILES['arquivo'] ?? null;
if (!$file || !is_scalar($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
    jsonError('Envie uma imagem válida (até 5 MB).', 400);
}
if ($file['size'] > 5 * 1024 * 1024) jsonError('A imagem deve ter no máximo 5 MB.', 400);
$info = @getimagesize($file['tmp_name']);
$types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
if (!$info || !isset($types[$mime]) || $info['mime'] !== $mime) {
    jsonError('Use uma imagem JPG, PNG ou WebP.', 400);
}
$dir = __DIR__ . '/../../assets/uploads/cursos';
if (!is_dir($dir) && !mkdir($dir, 0755, true)) jsonError('Não foi possível criar a pasta de imagens.', 500);
$name = bin2hex(random_bytes(16)) . '.' . $types[$mime];
if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) jsonError('Não foi possível salvar a imagem.', 500);
jsonOk(['url' => BASE_PATH . '/assets/uploads/cursos/' . $name], 201);
