<?php $pageTitle = 'LGPD — ActShare'; ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="max-w-4xl mx-auto px-4 py-12">
  <h1 class="text-3xl font-bold text-gray-800 mb-3">Treinamento LGPD</h1>
  <p class="text-gray-500 mb-10">Capacite sua equipe sobre a Lei Geral de Proteção de Dados Pessoais.</p>

  <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
    <h2 class="font-semibold text-primary text-lg mb-2">Por que investir em LGPD?</h2>
    <p class="text-gray-600 text-sm">
      Desde agosto de 2021, a ANPD pode aplicar sanções às empresas que descumprirem a lei.
      Treine sua equipe e evite multas de até 2% do faturamento.
    </p>
  </div>

  <div class="grid md:grid-cols-2 gap-6 mb-10">
    <?php
    $modulos = [
      ['Fundamentos da LGPD', 'Bases legais, princípios e direitos dos titulares.'],
      ['Tratamento de dados pessoais', 'Coleta, armazenamento, uso e compartilhamento.'],
      ['Obrigações das empresas', 'DPO, RIPD, políticas internas e contratos.'],
      ['Boas práticas e compliance', 'Implementação prática da lei no dia a dia.'],
    ];
    foreach ($modulos as [$titulo, $desc]):
    ?>
    <div class="bg-white border border-gray-200 rounded-xl p-5">
      <h3 class="font-semibold text-gray-800 mb-2"><?= htmlspecialchars($titulo) ?></h3>
      <p class="text-sm text-gray-500"><?= htmlspecialchars($desc) ?></p>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="text-center">
    <a href="<?= BASE_PATH ?>/registro" class="bg-primary text-white font-semibold px-8 py-3 rounded-lg hover:bg-blue-900 transition-colors inline-block">
      Matricular minha equipe
    </a>
  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
