<?php $pageTitle = 'Cursos Admin — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="flex items-center justify-between mb-8">
  <h1 class="text-2xl font-bold text-gray-800">Cursos</h1>
  <button onclick="abrirModalNovoCurso()" class="bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-900 transition-colors">
    + Novo Curso
  </button>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 text-left">
      <tr>
        <th class="px-5 py-3 font-medium text-gray-600">Título</th>
        <th class="px-5 py-3 font-medium text-gray-600">Categoria</th>
        <th class="px-5 py-3 font-medium text-gray-600">Status</th>
        <th class="px-5 py-3 font-medium text-gray-600">Ações</th>
      </tr>
    </thead>
    <tbody id="cursos-tbody" class="divide-y divide-gray-100">
      <tr><td colspan="4" class="text-center py-8 text-gray-400">Carregando...</td></tr>
    </tbody>
  </table>
</div>

<!-- Modal Novo/Editar Curso -->
<div id="modal-curso" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
    <h2 id="modal-titulo" class="text-lg font-bold text-gray-800 mb-5">Novo Curso</h2>
    <form id="form-curso" class="space-y-4">
      <input type="hidden" id="curso-id">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
        <input type="text" id="curso-titulo" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
        <textarea id="curso-descricao" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">URL da Thumbnail</label>
        <input type="url" id="curso-thumb" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Carga Horária (h)</label>
          <input type="number" id="curso-carga" min="0" value="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Preço (R$)</label>
          <input type="number" id="curso-preco" min="0" step="0.01" value="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
        <select id="curso-categoria" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
          <option value="">Sem categoria</option>
        </select>
      </div>
      <div class="flex gap-4">
        <label class="flex items-center gap-2 text-sm">
          <input type="checkbox" id="curso-ativo" checked class="rounded"> Ativo
        </label>
        <label class="flex items-center gap-2 text-sm">
          <input type="checkbox" id="curso-publico" class="rounded"> Público
        </label>
      </div>
      <div id="form-erro" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-2"></div>
      <div class="flex gap-3 pt-2">
        <button type="submit" id="btn-salvar-curso" class="flex-1 bg-primary text-white font-medium py-2.5 rounded-lg hover:bg-blue-900 transition-colors disabled:opacity-60">Salvar</button>
        <button type="button" onclick="fecharModalCurso()" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=2"></script>
<script>
  document.addEventListener('DOMContentLoaded', carregarCursosAdmin);
</script>

    </div></main></div></body></html>
