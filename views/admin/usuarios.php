<?php $pageTitle = 'Usuários Admin — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<h1 class="text-2xl font-bold text-gray-800 mb-8">Usuários</h1>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 text-left">
      <tr>
        <th class="px-5 py-3 font-medium text-gray-600">Nome</th>
        <th class="px-5 py-3 font-medium text-gray-600">E-mail</th>
        <th class="px-5 py-3 font-medium text-gray-600">Role</th>
        <th class="px-5 py-3 font-medium text-gray-600">Cadastro</th>
        <th class="px-5 py-3 font-medium text-gray-600">Ações</th>
      </tr>
    </thead>
    <tbody id="users-tbody" class="divide-y divide-gray-100">
      <tr><td colspan="5" class="text-center py-8 text-gray-400">Carregando...</td></tr>
    </tbody>
  </table>
</div>

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=2"></script>
<script>
  document.addEventListener('DOMContentLoaded', carregarUsuariosAdmin);
</script>

    </div></main></div></body></html>
