<?php $pageTitle = 'Editar Curso — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="mb-6">
  <a href="<?= BASE_PATH ?>/admin/cursos" class="text-sm text-primary hover:underline">← Voltar aos cursos</a>
</div>

<div id="curso-admin-loading" class="text-gray-400 py-12 text-center">Carregando...</div>

<div id="curso-admin-content" class="hidden space-y-8">
  <!-- Info do curso -->
  <div class="bg-white rounded-xl border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 id="ca-titulo" class="text-xl font-bold text-gray-800"></h1>
      <div class="flex gap-2">
        <button onclick="editarCursoInfo()" class="text-sm border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-50">Editar</button>
        <button onclick="excluirCurso()" class="text-sm bg-red-50 border border-red-200 text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-100">Excluir</button>
      </div>
    </div>
    <p id="ca-descricao" class="text-gray-500 text-sm"></p>
  </div>

  <!-- Módulos e aulas -->
  <div class="bg-white rounded-xl border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-5">
      <h2 class="font-semibold text-gray-700">Módulos</h2>
      <button onclick="abrirModalModulo()" class="text-sm bg-primary text-white px-3 py-1.5 rounded-lg hover:bg-blue-900">+ Módulo</button>
    </div>
    <div id="modulos-admin-list" class="space-y-4"></div>
  </div>
</div>

<!-- Modal módulo -->
<div id="modal-modulo" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-md p-6">
    <h2 id="mod-modal-titulo" class="text-lg font-bold text-gray-800 mb-4">Novo Módulo</h2>
    <form id="form-modulo" class="space-y-4">
      <input type="hidden" id="mod-id">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
        <input type="text" id="mod-titulo" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
        <input type="number" id="mod-ordem" value="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div class="flex gap-3">
        <button type="submit" class="flex-1 bg-primary text-white font-medium py-2.5 rounded-lg">Salvar</button>
        <button type="button" onclick="fecharModalModulo()" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal aula -->
<div id="modal-aula" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-md p-6">
    <h2 id="aula-modal-titulo" class="text-lg font-bold text-gray-800 mb-4">Nova Aula</h2>
    <form id="form-aula" class="space-y-4">
      <input type="hidden" id="aula-id">
      <input type="hidden" id="aula-modulo-id">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
        <input type="text" id="aula-titulo" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">URL do Vídeo</label>
        <input type="url" id="aula-url" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
        <textarea id="aula-descricao" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
          <input type="number" id="aula-ordem" value="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Duração (min)</label>
          <input type="number" id="aula-duracao" value="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
      </div>
      <div class="flex gap-3">
        <button type="submit" class="flex-1 bg-primary text-white font-medium py-2.5 rounded-lg">Salvar</button>
        <button type="button" onclick="fecharModalAula()" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=2"></script>
<script>
  const cursoAdminId = <?= (int)($_GET['id'] ?? 0) ?>;
  document.addEventListener('DOMContentLoaded', () => carregarCursoAdmin(cursoAdminId));
</script>

    </div></main></div></body></html>
