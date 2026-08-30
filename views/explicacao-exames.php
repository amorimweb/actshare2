<?php
$pageTitle = 'Avaliação e Exames — ActShare';
require __DIR__ . '/layout/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/configuracoes.php';

$config = getConfiguracoes(getDB());
$texto = $config['texto_explicacao_exames'] ?? '';
?>

<div class="max-w-3xl mx-auto px-4 py-16">
  <h1 class="text-3xl font-bold text-gray-800 mb-6">Avaliação e Exames</h1>
  <div class="prose prose-sm text-gray-600 whitespace-pre-line leading-relaxed">
    <?= nl2br(htmlspecialchars($texto)) ?>
  </div>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
