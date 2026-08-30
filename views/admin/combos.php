<?php $pageTitle = 'Combos — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="flex items-center justify-between mb-8">
  <div>
    <h1 class="text-2xl font-bold text-gray-800">Combos</h1>
    <p class="text-xs text-gray-400 mt-1">Produtos compostos por 2 ou mais cursos, com preço próprio (diferente da soma das partes).</p>
  </div>
  <button onclick="abrirModalCombo()" class="bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-900 transition-colors">
    + Novo Combo
  </button>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 text-left">
      <tr>
        <th class="px-5 py-3 font-medium text-gray-600">Título</th>
        <th class="px-5 py-3 font-medium text-gray-600">Cursos Inclusos</th>
        <th class="px-5 py-3 font-medium text-gray-600">Preço</th>
        <th class="px-5 py-3 font-medium text-gray-600">Status</th>
        <th class="px-5 py-3 font-medium text-gray-600">Ações</th>
      </tr>
    </thead>
    <tbody id="combos-tbody" class="divide-y divide-gray-100">
      <tr><td colspan="5" class="text-center py-8 text-gray-400">Carregando...</td></tr>
    </tbody>
  </table>
</div>

<!-- Modal -->
<div id="modal-combo" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
    <h2 id="modal-combo-titulo" class="text-lg font-bold text-gray-800 mb-5">Novo Combo</h2>
    <form id="form-combo" class="space-y-4">
      <input type="hidden" id="combo-id">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
        <input type="text" id="combo-titulo" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
        <textarea id="combo-descricao" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Preço do Combo (R$) *</label>
          <input type="number" id="combo-preco" required min="0" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
          <p class="text-[10px] text-slate-400 mt-0.5">Não precisa ser a soma dos cursos — defina o preço promocional do combo.</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Prazo de Validade (dias)</label>
          <input type="number" id="combo-prazo" min="0" placeholder="Ilimitado" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">URL da Imagem de Capa</label>
        <input type="url" id="combo-thumb" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Cursos Inclusos * (selecione 2 ou mais)</label>
        <select id="combo-cursos" multiple size="6" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></select>
        <p class="text-[10px] text-slate-400 mt-0.5">Ctrl/Cmd + clique para selecionar mais de um.</p>
      </div>
      <div class="grid grid-cols-3 gap-3 bg-slate-50 border border-slate-100 p-3 rounded-lg">
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-750">
          <input type="checkbox" id="combo-ativo" checked class="rounded accent-primary"> Ativo
        </label>
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-750">
          <input type="checkbox" id="combo-publico" class="rounded accent-primary"> Público (Vendas)
        </label>
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-750">
          <input type="checkbox" id="combo-disponivel-loja" checked class="rounded accent-primary"> Disponível na Loja
        </label>
      </div>
      <div id="combo-erro" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-2"></div>
      <div class="flex gap-3 pt-2">
        <button type="submit" id="btn-salvar-combo" class="flex-1 bg-primary text-white font-medium py-2.5 rounded-lg hover:bg-blue-900 transition-colors disabled:opacity-60">Salvar</button>
        <button type="button" onclick="document.getElementById('modal-combo').classList.add('hidden')" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=7"></script>
<script>
  document.addEventListener('DOMContentLoaded', carregarCombosAdmin);
</script>

    </div></main></div></body></html>
