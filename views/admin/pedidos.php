<?php $pageTitle = 'Pedidos Admin — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="flex items-center justify-between mb-8 gap-4 flex-wrap">
  <h1 class="text-2xl font-bold text-gray-800">Pedidos</h1>
  <button onclick="abrirNovoPedidoAdmin()" class="bg-primary text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-blue-900 transition-colors shadow-sm">
    + Novo Pedido
  </button>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-left">
        <?php
        $seta = function ($campo) {
            return '<button type="button" onclick="ordenarPedidosAdmin(\'' . $campo . '\')" class="inline-flex items-center ml-1 align-middle text-gray-400 hover:text-primary transition-colors" title="Ordenar"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3l4 5H6l4-5zM10 17l-4-5h8l-4 5z"/></svg></button>';
        };
        ?>
        <tr>
          <th class="px-4 py-3 font-medium text-gray-600">ID</th>
          <th class="px-4 py-3 font-medium text-gray-600">Data Pedido <?= $seta('created_at') ?></th>
          <th class="px-4 py-3 font-medium text-gray-600">Nome Cliente <?= $seta('nome_cliente') ?></th>
          <th class="px-4 py-3 font-medium text-gray-600">Status <?= $seta('situacao') ?></th>
          <th class="px-4 py-3 font-medium text-gray-600">Forma Pgto</th>
          <th class="px-4 py-3 font-medium text-gray-600">Cód Cupom</th>
          <th class="px-4 py-3 font-medium text-gray-600 text-right">Desc. R$</th>
          <th class="px-4 py-3 font-medium text-gray-600 text-right">Total Pago <?= $seta('total_liquido') ?></th>
          <th class="px-4 py-3 font-medium text-gray-600">Transação Asaas</th>
        </tr>
      </thead>
      <tbody id="pedidos-tbody" class="divide-y divide-gray-100">
        <tr><td colspan="9" class="text-center py-8 text-gray-400">Carregando...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Detalhe do Pedido -->
<div id="modal-pedido" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-3xl p-6 max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-lg font-bold text-gray-800">Detalhe do Pedido <span id="ped-id-titulo"></span></h2>
      <button onclick="document.getElementById('modal-pedido').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
    </div>
    <div id="pedido-detalhe-body" class="text-sm text-gray-600">Carregando...</div>
  </div>
</div>

<!-- Modal Novo Pedido (manual, item 7) -->
<div id="modal-novo-pedido" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-3xl p-6 max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-lg font-bold text-gray-800">Novo Pedido Manual</h2>
      <button onclick="document.getElementById('modal-novo-pedido').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
    </div>

    <div class="mb-4">
      <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cliente *</label>
      <input type="text" id="np-cliente-busca" placeholder="Buscar por nome ou e-mail..." autocomplete="off"
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20">
      <div id="np-cliente-resultados" class="hidden mt-1 border border-gray-200 rounded-lg shadow-sm max-h-48 overflow-y-auto bg-white"></div>
      <input type="hidden" id="np-cliente-id">
      <p id="np-cliente-selecionado" class="text-xs text-secondary font-semibold mt-1 hidden"></p>
    </div>

    <div class="flex items-center justify-between mb-2">
      <label class="block text-xs font-bold text-gray-500 uppercase">Produtos</label>
      <button type="button" onclick="adicionarItemNovoPedido()" class="text-xs text-secondary font-bold hover:underline">+ Adicionar Produto</button>
    </div>
    <table class="w-full text-xs border border-gray-100 rounded-lg overflow-hidden mb-2">
      <thead class="bg-gray-50 text-gray-500">
        <tr>
          <th class="px-2 py-2 text-left">Produto</th>
          <th class="px-2 py-2 text-left">Avaliação/Exame</th>
          <th class="px-2 py-2 text-left">Preço Unit.</th>
          <th class="px-2 py-2 text-left">Qde</th>
          <th class="px-2 py-2"></th>
        </tr>
      </thead>
      <tbody id="np-itens" class="divide-y divide-gray-100"></tbody>
    </table>

    <div id="np-erro" class="hidden bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-4 py-2 mb-3"></div>

    <button id="btn-criar-pedido" onclick="salvarNovoPedidoAdmin()" class="w-full bg-primary text-white text-xs font-bold uppercase tracking-wider py-3 rounded-lg hover:bg-slate-800 transition-colors">
      Criar Pedido
    </button>
  </div>
</div>

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=12"></script>
<script>
  document.addEventListener('DOMContentLoaded', carregarPedidosAdmin);
</script>

    </div></main></div></body></html>
