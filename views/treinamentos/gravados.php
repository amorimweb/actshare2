<?php $pageTitle = 'Treinamentos Gravados — ActShare'; ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-12">
  <h1 class="text-3xl font-bold text-gray-800 mb-3">Treinamentos Gravados</h1>
  <p class="text-gray-500 mb-10">Assista quando e onde quiser, no seu próprio ritmo.</p>

  <div id="gravados-grid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="col-span-full text-center py-12 text-gray-400">
      <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
      <p>Carregando...</p>
    </div>
  </div>
</div>

<script src="<?= BASE_PATH ?>/assets/js/cursos.js?v=7"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    carregarCursosDestaque('gravados-grid', 12);
  });
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
