<?php $pageTitle = 'Pedidos Admin — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<h1 class="text-2xl font-bold text-gray-800 mb-8">Pedidos</h1>

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

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=10"></script>
<script>
  document.addEventListener('DOMContentLoaded', carregarPedidosAdmin);
</script>

    </div></main></div></body></html>
