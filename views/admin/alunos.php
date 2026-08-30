<?php $pageTitle = 'Alunos Admin — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="flex items-center justify-between mb-8 gap-4 flex-wrap">
  <h1 class="text-2xl font-bold text-gray-800">Alunos</h1>
  <div class="relative">
    <input type="text" id="alunos-admin-busca" oninput="filtrarAlunosAdmin()" placeholder="Buscar por nome, e-mail ou curso..."
      class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-72 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
  </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-left">
        <?php
        $seta = function ($campo) {
            return '<button type="button" onclick="ordenarAlunosAdmin(\'' . $campo . '\')" class="inline-flex items-center ml-1 align-middle text-gray-400 hover:text-primary transition-colors" title="Ordenar"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3l4 5H6l4-5zM10 17l-4-5h8l-4 5z"/></svg></button>';
        };
        ?>
        <tr>
          <th class="px-4 py-3 font-medium text-gray-600">Nome <?= $seta('aluno_nome') ?></th>
          <th class="px-4 py-3 font-medium text-gray-600">Cliente <?= $seta('cliente') ?></th>
          <th class="px-4 py-3 font-medium text-gray-600">Login</th>
          <th class="px-4 py-3 font-medium text-gray-600">Treinamento <?= $seta('curso_titulo') ?></th>
          <th class="px-4 py-3 font-medium text-gray-600">Status</th>
          <th class="px-4 py-3 font-medium text-gray-600">Prazo <?= $seta('data_fim_acesso') ?></th>
          <th class="px-4 py-3 font-medium text-gray-600">Início</th>
          <th class="px-4 py-3 font-medium text-gray-600">Término</th>
          <th class="px-4 py-3 font-medium text-gray-600">Certificado</th>
          <th class="px-4 py-3 font-medium text-gray-600">Provas</th>
        </tr>
      </thead>
      <tbody id="alunos-admin-tbody" class="divide-y divide-gray-100">
        <tr><td colspan="9" class="text-center py-8 text-gray-400">Carregando...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Provas -->
<div id="modal-provas-aluno" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-xl p-6 max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-bold text-gray-800">Provas do Aluno</h2>
      <button onclick="document.getElementById('modal-provas-aluno').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
    </div>
    <div id="provas-aluno-lista" class="space-y-3 text-sm">Carregando...</div>
  </div>
</div>

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=10"></script>
<script>
  document.addEventListener('DOMContentLoaded', carregarAlunosAdmin);
</script>

    </div></main></div></body></html>
