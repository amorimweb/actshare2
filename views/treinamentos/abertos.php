<?php $pageTitle = 'Treinamentos Abertos — ActShare'; ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="max-w-5xl mx-auto px-4 py-12">
  <h1 class="text-3xl font-bold text-gray-800 mb-3">Treinamentos Abertos</h1>
  <p class="text-gray-500 mb-10">Participe de nossas turmas abertas presenciais e online.</p>

  <div class="grid md:grid-cols-2 gap-6">
    <?php
    $treinamentos = [
      ['titulo' => 'Gestão de Projetos na Prática', 'data' => '15/06/2026', 'modalidade' => 'Online ao vivo', 'duracao' => '8h', 'cor' => 'bg-blue-50 border-blue-200'],
      ['titulo' => 'Liderança e Comunicação', 'data' => '22/06/2026', 'modalidade' => 'Presencial — SP', 'duracao' => '16h', 'cor' => 'bg-green-50 border-green-200'],
      ['titulo' => 'Excel Avançado para Gestores', 'data' => '29/06/2026', 'modalidade' => 'Online ao vivo', 'duracao' => '12h', 'cor' => 'bg-purple-50 border-purple-200'],
      ['titulo' => 'Compliance e LGPD', 'data' => '06/07/2026', 'modalidade' => 'Online ao vivo', 'duracao' => '4h', 'cor' => 'bg-orange-50 border-orange-200'],
    ];
    foreach ($treinamentos as $t):
    ?>
    <div class="border rounded-xl p-6 <?= $t['cor'] ?>">
      <h2 class="font-semibold text-gray-800 text-lg mb-3"><?= htmlspecialchars($t['titulo']) ?></h2>
      <div class="space-y-1 text-sm text-gray-600 mb-5">
        <p>📅 <?= $t['data'] ?></p>
        <p>🖥 <?= $t['modalidade'] ?></p>
        <p>⏱ <?= $t['duracao'] ?></p>
      </div>
      <a href="<?= BASE_PATH ?>/registro" class="inline-block bg-primary text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-blue-900 transition-colors">
        Inscrever-se
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
