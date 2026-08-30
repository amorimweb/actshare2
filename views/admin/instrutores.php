<?php $pageTitle = 'Instrutores Admin — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="flex items-center justify-between mb-8">
  <h1 class="text-2xl font-bold text-gray-800">Instrutores</h1>
  <button onclick="abrirModalInstrutor()" class="bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-900 transition-colors">
    + Novo Instrutor
  </button>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 text-left">
      <tr>
        <th class="px-5 py-3 font-medium text-gray-600">Foto</th>
        <th class="px-5 py-3 font-medium text-gray-600">Nome</th>
        <th class="px-5 py-3 font-medium text-gray-600">Qualificações</th>
        <th class="px-5 py-3 font-medium text-gray-600">Ações</th>
      </tr>
    </thead>
    <tbody id="inst-tbody" class="divide-y divide-gray-100">
      <tr><td colspan="4" class="text-center py-8 text-gray-400">Carregando...</td></tr>
    </tbody>
  </table>
</div>

<!-- Modal -->
<div id="modal-inst" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
    <h2 id="modal-inst-titulo" class="text-lg font-bold text-gray-800 mb-5">Novo Instrutor</h2>
    <form id="form-inst" class="space-y-4">
      <input type="hidden" id="inst-id">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
        <input type="text" id="inst-nome" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Qualificação 1</label>
          <input type="text" id="inst-qualificacao1" placeholder="Ex: MBA em Gestão" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Qualificação 2</label>
          <input type="text" id="inst-qualificacao2" placeholder="Ex: Auditor Líder ISO" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Breve currículo (aparece na ficha do curso)</label>
        <textarea id="inst-descricao" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">URL da Foto</label>
        <input type="url" id="inst-avatar" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">URL da Assinatura (para o certificado)</label>
        <input type="url" id="inst-assinatura" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div id="inst-erro" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-2"></div>
      <div class="flex gap-3">
        <button type="submit" id="btn-salvar-inst" class="flex-1 bg-primary text-white font-medium py-2.5 rounded-lg hover:bg-blue-900 transition-colors disabled:opacity-60">Salvar</button>
        <button type="button" onclick="document.getElementById('modal-inst').classList.add('hidden')" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=10"></script>
<script>
  document.addEventListener('DOMContentLoaded', carregarInstrutoresAdmin);
</script>

    </div></main></div></body></html>
