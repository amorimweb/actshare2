<?php $pageTitle = 'Categorias Admin — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="flex items-center justify-between mb-8">
  <h1 class="text-2xl font-bold text-gray-800">Categorias</h1>
  <button onclick="abrirModalCategoria()" class="bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-900 transition-colors">
    + Nova Categoria
  </button>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 text-left">
      <tr>
        <th class="px-5 py-3 font-medium text-gray-600">Nome</th>
        <th class="px-5 py-3 font-medium text-gray-600">Slug</th>
        <th class="px-5 py-3 font-medium text-gray-600">Grupo (categoria pai)</th>
        <th class="px-5 py-3 font-medium text-gray-600">Ações</th>
      </tr>
    </thead>
    <tbody id="cats-tbody" class="divide-y divide-gray-100">
      <tr><td colspan="4" class="text-center py-8 text-gray-400">Carregando...</td></tr>
    </tbody>
  </table>
</div>

<!-- Modal -->
<div id="modal-cat" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-md p-6">
    <h2 id="modal-cat-titulo" class="text-lg font-bold text-gray-800 mb-5">Nova Categoria</h2>
    <form id="form-cat" class="space-y-4">
      <input type="hidden" id="cat-id">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
        <input type="text" id="cat-nome" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
        <input type="text" id="cat-slug" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Categoria Pai (opcional — para criar um subgrupo)</label>
        <select id="cat-parent" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white">
          <option value="">Nenhuma (categoria de nível principal)</option>
        </select>
      </div>
      <div id="cat-erro" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-2"></div>
      <div class="flex gap-3">
        <button type="submit" id="btn-salvar-cat" class="flex-1 bg-primary text-white font-medium py-2.5 rounded-lg hover:bg-blue-900 transition-colors disabled:opacity-60">Salvar</button>
        <button type="button" onclick="document.getElementById('modal-cat').classList.add('hidden')" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=12"></script>
<script>
  document.addEventListener('DOMContentLoaded', carregarCategoriasAdmin);
</script>

    </div></main></div></body></html>
