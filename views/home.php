<?php
$pageTitle = 'ActShare — Treinamentos e Educação Corporativa';
$showCourseNav = true;
?>
<?php require __DIR__ . '/layout/header.php'; ?>

<!-- Hero -->
<section class="relative min-h-[520px] overflow-hidden text-white">
  <video class="absolute inset-0 h-full w-full object-cover" autoplay muted loop playsinline preload="metadata">
    <source src="<?= BASE_PATH ?>/assets/img/herovideo.mp4" type="video/mp4">
  </video>
  <div class="absolute inset-0 bg-gradient-to-r from-primary/85 via-primary/45 to-black/55"></div>

  <div class="relative z-10 mx-auto flex min-h-[520px] max-w-7xl items-center justify-start px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl text-left">
      <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
        Capacite sua equipe com<br>os melhores treinamentos
      </h1>
      <p class="max-w-xl text-lg md:text-xl text-blue-50 mb-10">
        Cursos online, treinamentos ao vivo e conteúdo especializado para o mercado corporativo.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-start">
        <a href="<?= BASE_PATH ?>/cursos" class="bg-secondary text-white font-semibold px-8 py-3 rounded-lg hover:bg-green-600 transition-colors">
          Ver Cursos
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Catálogo em destaque -->
<section class="py-16 px-4 bg-white">
  <div class="max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold text-gray-800 mb-2">Treinamentos em Destaque</h2>
    <p class="text-gray-500 mb-10">Explore nosso catálogo de treinamentos profissionais.</p>

    <div id="cursos-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <!-- preenchido via JS -->
      <div class="col-span-full text-center py-12 text-gray-400">
        <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
        <p>Carregando cursos...</p>
      </div>
    </div>

    <div class="text-center mt-10">
      <a href="<?= BASE_PATH ?>/cursos" class="inline-block bg-primary text-white font-semibold px-8 py-3 rounded-lg hover:bg-primary-dark transition-colors">
        Ver todos os cursos
      </a>
    </div>
  </div>
</section>

<!-- Sobre -->
<section class="py-16 px-4 bg-gray-50">
  <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
    <div>
      <h2 class="text-3xl font-bold text-gray-800 mb-4">Por que escolher a ActShare?</h2>
      <p class="text-gray-600 mb-6">
        Somos especialistas em educação corporativa, com uma plataforma robusta para gestão de treinamentos, acompanhamento de progresso e emissão de certificados.
      </p>
      <ul class="space-y-3 text-gray-600">
        <?php foreach(['Cursos online com certificação','Gestão completa de equipes','Progresso em tempo real','Suporte especializado'] as $item): ?>
        <li class="flex items-center gap-3">
          <svg class="w-5 h-5 text-secondary flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
          <?= $item ?>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="bg-primary rounded-2xl p-8 text-white">
      <div class="grid grid-cols-2 gap-6">
        <?php foreach([['500+','Alunos ativos'],['50+','Cursos disponíveis'],['95%','Satisfação'],['24/7','Suporte']] as [$n,$l]): ?>
        <div class="text-center">
          <div class="text-4xl font-bold text-secondary"><?= $n ?></div>
          <div class="text-sm text-blue-200 mt-1"><?= $l ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<script src="<?= BASE_PATH ?>/assets/js/cursos.js?v=2"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    carregarCursosDestaque('cursos-grid', 4);
  });
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
