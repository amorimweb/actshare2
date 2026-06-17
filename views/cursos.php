<?php $pageTitle = 'Cursos — ActShare'; ?>
<?php $showCourseNav = true; ?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 py-10">
  <h1 class="text-3xl font-bold text-gray-800 mb-2">Catálogo de Treinamentos</h1>
  <p class="text-gray-500 mb-8">Encontre o treinamento ideal para você e sua equipe.</p>

  <div id="cursos-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <div class="col-span-full text-center py-16 text-gray-400">
      <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
      <p>Carregando cursos...</p>
    </div>
  </div>
  <p id="cursos-empty" class="hidden text-center py-16 text-gray-400">Nenhum curso encontrado.</p>
</div>

<script src="<?= BASE_PATH ?>/assets/js/cursos.js?v=3"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    carregarPaginaCursos();
  });

  function filtrarCategoria(categoriaId) {
    const url = new URL(window.location.href);
    if (categoriaId) url.searchParams.set('categoria', categoriaId);
    else url.searchParams.delete('categoria');
    window.history.replaceState({}, '', url);

    atualizarCategoriaAtiva(categoriaId);
    renderizarCursos(categoriaId);
  }
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
