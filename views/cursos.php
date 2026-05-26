<?php $pageTitle = 'Cursos — ActShare'; ?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 py-10">
  <h1 class="text-3xl font-bold text-gray-800 mb-2">Catálogo de Cursos</h1>
  <p class="text-gray-500 mb-8">Encontre o treinamento ideal para você e sua equipe.</p>

  <div class="flex flex-col lg:flex-row gap-8">
    <!-- Sidebar filtros -->
    <aside class="w-full lg:w-64 flex-shrink-0">
      <div class="bg-white rounded-xl border border-gray-200 p-5 sticky top-24">
        <h2 class="font-semibold text-gray-700 mb-4">Categorias</h2>
        <ul id="categorias-list" class="space-y-2">
          <li>
            <button onclick="filtrarCategoria(null)" class="categoria-btn w-full text-left text-sm px-3 py-1.5 rounded-lg text-primary font-medium bg-blue-50" data-id="">
              Todas as categorias
            </button>
          </li>
        </ul>
      </div>
    </aside>

    <!-- Grid de cursos -->
    <div class="flex-1">
      <div id="cursos-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
        <div class="col-span-full text-center py-16 text-gray-400">
          <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
          <p>Carregando cursos...</p>
        </div>
      </div>
      <p id="cursos-empty" class="hidden text-center py-16 text-gray-400">Nenhum curso encontrado.</p>
    </div>
  </div>
</div>

<script src="<?= BASE_PATH ?>/assets/js/cursos.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    carregarPaginaCursos();
  });

  function filtrarCategoria(categoriaId) {
    document.querySelectorAll('.categoria-btn').forEach(b => {
      b.classList.toggle('bg-blue-50', b.dataset.id == (categoriaId ?? ''));
      b.classList.toggle('text-primary', b.dataset.id == (categoriaId ?? ''));
      b.classList.toggle('font-medium', b.dataset.id == (categoriaId ?? ''));
    });
    renderizarCursos(categoriaId);
  }
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
