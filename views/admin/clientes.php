<?php $pageTitle = 'Clientes Admin — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="flex items-center justify-between mb-8 gap-4 flex-wrap">
  <h1 class="text-2xl font-bold text-gray-800">Clientes</h1>
  <div class="flex items-center gap-3">
    <div class="relative">
      <input type="text" id="clientes-busca" oninput="filtrarClientesAdmin()" placeholder="Buscar por nome, e-mail ou cidade..."
        class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-72 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
      <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    <button onclick="abrirNovoClienteAdmin()" class="bg-primary text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-blue-900 transition-colors shadow-sm whitespace-nowrap">
      + Novo Cliente
    </button>
  </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-left">
        <?php
        $seta = function ($campo) {
            return '<button type="button" onclick="ordenarClientesAdmin(\'' . $campo . '\')" class="inline-flex items-center ml-1 align-middle text-gray-400 hover:text-primary transition-colors" title="Ordenar"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3l4 5H6l4-5zM10 17l-4-5h8l-4 5z"/></svg></button>';
        };
        ?>
        <tr>
          <th class="px-4 py-3 font-medium text-gray-600">ID</th>
          <th class="px-4 py-3 font-medium text-gray-600">DtCadastro <?= $seta('created_at') ?></th>
          <th class="px-4 py-3 font-medium text-gray-600">Nome <?= $seta('nome') ?></th>
          <th class="px-4 py-3 font-medium text-gray-600">PF/PJ</th>
          <th class="px-4 py-3 font-medium text-gray-600">Razão Social <?= $seta('razao_social') ?></th>
          <th class="px-4 py-3 font-medium text-gray-600">E-mail <?= $seta('email') ?></th>
          <th class="px-4 py-3 font-medium text-gray-600">Contato</th>
          <th class="px-4 py-3 font-medium text-gray-600">Cidade <?= $seta('cidade') ?></th>
          <th class="px-4 py-3 font-medium text-gray-600">UF</th>
          <th class="px-4 py-3 font-medium text-gray-600">Ações</th>
        </tr>
      </thead>
      <tbody id="clientes-tbody" class="divide-y divide-gray-100">
        <tr><td colspan="10" class="text-center py-8 text-gray-400">Carregando...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Ficha do Cliente -->
<div id="modal-cliente" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
    <h2 id="cli-modal-titulo" class="text-lg font-bold text-gray-800 mb-5">Ficha do Cliente</h2>
    <form id="form-cliente" class="space-y-4">
      <input type="hidden" id="cli-id">

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
          <input type="text" id="cli-nome" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Pessoa</label>
          <select id="cli-tipo-pessoa" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white">
            <option value="">Não informado</option>
            <option value="fisica">Física</option>
            <option value="juridica">Jurídica</option>
          </select>
        </div>
      </div>

      <div id="cli-email-bloco">
        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail *</label>
        <input type="email" id="cli-email" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <p class="text-[11px] text-gray-400 mt-1">Login do cliente. Senha temporária padrão: <code class="bg-gray-100 px-1 rounded">actshare123</code>.</p>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">CPF / CNPJ</label>
          <input type="text" id="cli-documento" data-mask="documento" maxlength="18" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Razão Social</label>
          <input type="text" id="cli-razao-social" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Inscrição Estadual</label>
          <input type="text" id="cli-inscricao-estadual" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Telefone / Contato</label>
          <input type="text" id="cli-telefone" data-mask="telefone" maxlength="15" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
      </div>

      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
          <input type="text" id="cli-cep" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
          <input type="text" id="cli-endereco" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
      </div>

      <div class="grid grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
          <input type="text" id="cli-numero" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
          <input type="text" id="cli-bairro" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
          <input type="text" id="cli-cidade" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
          <input type="text" id="cli-estado" maxlength="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase">
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Forma de Acesso ao Certificado</label>
        <div class="flex gap-4 text-sm text-gray-700">
          <label class="flex items-center gap-1.5"><input type="radio" name="cli-certificado-acesso" value="empresa"> Somente a Empresa</label>
          <label class="flex items-center gap-1.5"><input type="radio" name="cli-certificado-acesso" value="aluno"> Somente o Aluno</label>
          <label class="flex items-center gap-1.5"><input type="radio" name="cli-certificado-acesso" value="ambos" checked> Empresa e Aluno</label>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Observação (interna, só o Admin vê)</label>
        <textarea id="cli-observacao" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
      </div>

      <div id="cli-erro" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-2"></div>
      <div class="flex gap-3 pt-2">
        <button type="submit" id="btn-salvar-cliente" class="flex-1 bg-primary text-white font-medium py-2.5 rounded-lg">Salvar</button>
        <button type="button" onclick="document.getElementById('modal-cliente').classList.add('hidden')" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg">Fechar</button>
      </div>
    </form>
  </div>
</div>

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=12"></script>
<script>
  document.addEventListener('DOMContentLoaded', carregarClientesAdmin);
</script>

    </div></main></div></body></html>
