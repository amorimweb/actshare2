<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') methodNotAllowed();

requireAdmin();
$id = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

$db = getDB();
$stmt = $db->prepare('SELECT caminho FROM aula_materiais WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$material = $stmt->fetch();
if (!$material) jsonError('Material não encontrado.', 404);

$caminhoAbsoluto = __DIR__ . '/../../assets/uploads/' . $material['caminho'];
if (is_file($caminhoAbsoluto)) unlink($caminhoAbsoluto);

$db->prepare('DELETE FROM aula_materiais WHERE id = ?')->execute([$id]);
jsonOk(['success' => true]);
