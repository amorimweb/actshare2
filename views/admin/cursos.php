<?php $pageTitle = 'Cursos Admin — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="flex items-center justify-between mb-8 gap-4 flex-wrap">
  <h1 class="text-2xl font-bold text-gray-800">Cursos</h1>
  <div class="flex items-center gap-3">
    <div class="relative">
      <input type="text" id="cursos-busca" oninput="filtrarCursosAdmin()" placeholder="Buscar por nome do curso..."
        class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
      <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    <button onclick="abrirModalNovoCurso()" class="bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-900 transition-colors shrink-0">
      + Novo Curso
    </button>
  </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 text-left">
      <tr>
        <th class="px-5 py-3 font-medium text-gray-600">Título <button type="button" onclick="ordenarCursosAdmin('titulo')" class="inline-flex items-center ml-1 align-middle text-gray-400 hover:text-primary transition-colors" title="Ordenar"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3l4 5H6l4-5zM10 17l-4-5h8l-4 5z"/></svg></button></th>
        <th class="px-5 py-3 font-medium text-gray-600">Categoria <button type="button" onclick="ordenarCursosAdmin('categoria.nome')" class="inline-flex items-center ml-1 align-middle text-gray-400 hover:text-primary transition-colors" title="Ordenar"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3l4 5H6l4-5zM10 17l-4-5h8l-4 5z"/></svg></button></th>
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
      <div class="grid grid-cols-3 gap-3">
        <div class="col-span-2">
          <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Título do Curso (Loja) *</label>
          <input type="text" id="curso-titulo" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Código *</label>
          <input type="text" id="curso-codigo" required maxlength="10" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary uppercase" placeholder="ISO9001">
        </div>
      </div>
      <div class="grid grid-cols-3 gap-3">
        <div class="col-span-2">
          <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nome para o Certificado</label>
          <input type="text" id="curso-nome-certificado" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Se vazio, usa o título">
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Prazo (Dias)</label>
          <input type="number" id="curso-prazo-acesso" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Ilimitado">
        </div>
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Descrição</label>
        <textarea id="curso-descricao" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Carga Horária (h)</label>
          <input type="number" id="curso-carga" min="0" value="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Preço (R$)</label>
          <input type="number" id="curso-preco" min="0" step="0.01" value="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Categoria</label>
          <select id="curso-categoria" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white">
            <option value="">Sem categoria</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Instrutor</label>
          <select id="curso-instrutor" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white">
            <option value="">Sem instrutor</option>
          </select>
        </div>
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">URL da Imagem de Capa (Thumbnail)</label>
        <input type="url" id="curso-thumb" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div class="grid grid-cols-2 gap-3 bg-slate-50 border border-slate-100 p-3 rounded-lg">
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-750">
          <input type="checkbox" id="curso-ativo" checked class="rounded accent-primary"> Ativo
        </label>
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-750">
          <input type="checkbox" id="curso-publico" class="rounded accent-primary"> Público (Vendas)
        </label>
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-750">
          <input type="checkbox" id="curso-disponivel-loja" checked class="rounded accent-primary"> Disponível na Loja
        </label>
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-750">
          <input type="checkbox" id="curso-exibir-instrutor" class="rounded accent-primary"> Exibir Instrutor
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

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=11"></script>
<script>
  document.addEventListener('DOMContentLoaded', carregarCursosAdmin);
</script>

    </div></main></div></body></html>
