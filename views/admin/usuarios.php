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
        <th class="px-5 py-3 font-medium text-gray-600">Status</th>
        <th class="px-5 py-3 font-medium text-gray-600">Ações</th>
      </tr>
    </thead>
    <tbody id="users-tbody" class="divide-y divide-gray-100">
      <tr><td colspan="5" class="text-center py-8 text-gray-400">Carregando...</td></tr>
    </tbody>
  </table>
</div>

<!-- Modal Ficha do Cliente -->
<div id="modal-ficha" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
    <h2 class="text-lg font-bold text-gray-800 mb-5">Ficha do Cliente</h2>
    <form id="form-ficha" class="space-y-4">
      <input type="hidden" id="ficha-id">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
          <input type="text" id="ficha-nome" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">E-mail *</label>
          <input type="email" id="ficha-email" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">CPF / CNPJ</label>
          <input type="text" id="ficha-documento" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Telefone / WhatsApp</label>
          <input type="text" id="ficha-telefone" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Pessoa</label>
          <select id="ficha-tipo-pessoa" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white">
            <option value="">Não informado</option>
            <option value="fisica">Física</option>
            <option value="juridica">Jurídica</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Razão Social</label>
          <input type="text" id="ficha-razao-social" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Inscrição Estadual</label>
          <input type="text" id="ficha-inscricao-estadual" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
          <input type="text" id="ficha-cep" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
      </div>
      <div class="grid grid-cols-3 gap-4">
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
          <input type="text" id="ficha-endereco" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
          <input type="text" id="ficha-numero" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
      </div>
      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
          <input type="text" id="ficha-bairro" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
          <input type="text" id="ficha-cidade" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
          <input type="text" id="ficha-estado" maxlength="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase">
        </div>
      </div>
      <div id="ficha-erro" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-2"></div>
      <div class="flex gap-3 pt-2">
        <button type="submit" id="btn-salvar-ficha" class="flex-1 bg-primary text-white font-medium py-2.5 rounded-lg">Salvar Ficha</button>
        <button type="button" onclick="document.getElementById('modal-ficha').classList.add('hidden')" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg">Fechar</button>
      </div>
    </form>
  </div>
</div>

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=5"></script>
<script>
  document.addEventListener('DOMContentLoaded', carregarUsuariosAdmin);
</script>

    </div></main></div></body></html>
