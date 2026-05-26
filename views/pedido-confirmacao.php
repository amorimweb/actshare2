<?php
$pageTitle = 'Confirmação do Pedido — ActShare';
require __DIR__ . '/layout/header.php';
?>

<div class="max-w-3xl mx-auto px-4 py-12">
  <div id="confirmacao-vazia" class="hidden text-center py-16 bg-white border border-gray-200 rounded-2xl shadow-sm">
    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <h3 class="text-gray-700 font-bold text-lg mb-2">Nenhum pedido recente localizado</h3>
    <p class="text-xs text-gray-400 mb-6">Parece que você não possui transações pendentes de finalização.</p>
    <a href="<?= BASE_PATH ?>/cursos" class="inline-block bg-primary text-white font-semibold px-6 py-2.5 rounded-xl hover:bg-blue-900 transition-colors text-sm shadow-sm">
      Ver Catálogo de Cursos
    </a>
  </div>

  <div id="confirmacao-conteudo" class="hidden space-y-6">
    <!-- Card principal do pedido -->
    <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm text-center space-y-6">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-50 border border-green-200 text-green-500 mx-auto">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
      </div>

      <div class="space-y-2">
        <h2 class="text-2xl font-extrabold text-gray-800 tracking-tight">Pedido Recebido com Sucesso!</h2>
        <p class="text-sm text-gray-500">Obrigado pela sua compra. Seu pedido <strong class="text-gray-700">#<span id="pedido-id-txt"></span></strong> foi registrado.</p>
      </div>

      <div class="inline-block bg-gray-55 px-5 py-2.5 rounded-xl border border-gray-100">
        <span class="text-xs text-gray-400 block uppercase font-bold tracking-wider mb-0.5">Valor Total</span>
        <span id="pedido-total-txt" class="text-2xl font-black text-primary">R$ 0,00</span>
      </div>

      <div class="border-t border-gray-100 pt-6 space-y-4">
        <!-- Instruções de Pix -->
        <div id="bloco-pix" class="hidden space-y-4">
          <p class="text-sm text-gray-600 font-medium">Escaneie o QR Code abaixo pelo aplicativo do seu banco para pagar:</p>
          <div class="bg-gray-50 p-4 border border-gray-200 rounded-xl inline-block">
            <img id="pix-qr-img" src="" alt="QR Code PIX" class="w-48 h-48 mx-auto">
          </div>
          <div class="max-w-md mx-auto space-y-1.5 text-left">
            <label for="pix-codigo-input" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wide">Ou pague pelo Copia e Cola</label>
            <div class="flex gap-2">
              <input type="text" id="pix-codigo-input" readonly
                class="flex-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs text-gray-600 focus:outline-none">
              <button onclick="copiarPix()" class="bg-gray-100 hover:bg-gray-200 border border-gray-200 text-gray-700 text-xs font-bold px-3 py-2 rounded-lg transition-colors whitespace-nowrap">
                Copiar
              </button>
            </div>
            <p id="pix-copiado-msg" class="hidden text-xs text-green-600 font-semibold text-center mt-1">✓ Código copiado para a área de transferência!</p>
          </div>
        </div>

        <!-- Instruções de Boleto -->
        <div id="bloco-boleto" class="hidden space-y-4">
          <p class="text-sm text-gray-600 font-medium">Seu boleto bancário foi gerado com sucesso.</p>
          <div class="max-w-md mx-auto bg-gray-50 p-4 border border-gray-200 rounded-xl space-y-3 text-left">
            <div>
              <span class="text-[10px] font-bold text-gray-400 block uppercase">Código de Barras</span>
              <span id="boleto-codigo-txt" class="text-xs text-gray-700 font-mono break-all font-bold"></span>
            </div>
            <a href="#" id="boleto-link-pdf" target="_blank"
              class="w-full bg-primary/10 hover:bg-primary/20 text-primary font-bold py-2.5 rounded-lg text-xs transition-colors flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              Visualizar PDF do Boleto
            </a>
          </div>
        </div>

        <!-- Instruções de Cartão -->
        <div id="bloco-cartao" class="hidden space-y-2 py-4">
          <p class="text-sm text-gray-600 font-medium">✓ Pagamento por cartão de crédito aprovado!</p>
          <p class="text-xs text-gray-400">As vagas foram liberadas no seu perfil e os acessos já estão ativos.</p>
        </div>
      </div>
    </div>

    <!-- Painel de Simulação (Apenas em Testes) -->
    <div id="bloco-simulador" class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-6 shadow-sm space-y-4">
      <div class="flex items-start gap-3">
        <div class="bg-amber-100 text-amber-700 p-2 rounded-lg mt-0.5">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
        </div>
        <div class="space-y-1">
          <h4 class="font-extrabold text-amber-800 text-sm">Painel de Simulação de Pagamento (Ambiente de Teste)</h4>
          <p class="text-xs text-amber-700 leading-relaxed">
            Como solicitado, a integração real com gateway está desabilitada para homologação. Clique no botão abaixo para simular o recebimento da confirmação do pagamento e ativar os cursos na hora.
          </p>
        </div>
      </div>

      <div class="flex flex-wrap gap-3 items-center pt-2">
        <button onclick="simularConfirmacao()" id="btn-simular"
          class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow-sm transition-colors flex items-center gap-2">
          <span class="inline-block w-2.5 h-2.5 rounded-full bg-white animate-ping"></span>
          Simular Confirmação e Ativar
        </button>
        <a href="<?= BASE_PATH ?>/painel" id="btn-ir-painel" class="hidden bg-primary text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors">
          Ir para Área do Aluno
        </a>
      </div>
      <p id="simulador-status" class="hidden text-xs font-semibold text-green-700"></p>
    </div>
  </div>
</div>

<script>
  let pedidoData = null;

  document.addEventListener('DOMContentLoaded', () => {
    carregarPedidoConfirmacao();
  });

  function carregarPedidoConfirmacao() {
    try {
      pedidoData = JSON.parse(sessionStorage.getItem('act_ultimo_pedido')) || null;
    } catch {
      pedidoData = null;
    }

    if (!pedidoData) {
      document.getElementById('confirmacao-vazia').classList.remove('hidden');
      return;
    }

    document.getElementById('confirmacao-conteudo').classList.remove('hidden');

    // Popula dados gerais
    document.getElementById('pedido-id-txt').textContent = pedidoData.pedido_id;
    document.getElementById('pedido-total-txt').textContent = 'R$ ' + parseFloat(pedidoData.total_liquido).toFixed(2).replace('.', ',');

    // Trata forma de pagamento
    const fp = pedidoData.forma_pagamento;
    if (fp === 'pix') {
      document.getElementById('bloco-pix').classList.remove('hidden');
      document.getElementById('pix-qr-img').src = pedidoData.pix_qr;
      document.getElementById('pix-codigo-input').value = pedidoData.pix_code;
    } else if (fp === 'boleto') {
      document.getElementById('bloco-boleto').classList.remove('hidden');
      document.getElementById('boleto-codigo-txt').textContent = pedidoData.boleto_barcode;
      document.getElementById('boleto-link-pdf').href = pedidoData.boleto_pdf;
    } else if (fp === 'cartao') {
      document.getElementById('bloco-cartao').classList.remove('hidden');
      // Cartão ativa na hora, mas mantemos o simulador caso queiram forçar
      if (pedidoData.cartao_sucesso) {
        document.getElementById('bloco-simulador').classList.add('hidden');
        document.getElementById('btn-ir-painel').classList.remove('hidden');
        // Auto simula no backend
        simularConfirmacaoSilenciosa();
      }
    }
  }

  function copiarPix() {
    const input = document.getElementById('pix-codigo-input');
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value);

    const msg = document.getElementById('pix-copiado-msg');
    msg.classList.remove('hidden');
    setTimeout(() => { msg.classList.add('hidden'); }, 3000);
  }

  async function simularConfirmacaoSilenciosa() {
    try {
      await apiPost(BASE + '/api/checkout/simular-pagamento', { pedido_id: parseInt(pedidoData.pedido_id) });
    } catch {}
  }

  async function simularConfirmacao() {
    const btn = document.getElementById('btn-simular');
    const status = document.getElementById('simulador-status');
    
    btn.disabled = true;
    btn.textContent = 'Processando simulação...';
    status.classList.add('hidden');

    try {
      const res = await apiPost(BASE + '/api/checkout/simular-pagamento', { pedido_id: parseInt(pedidoData.pedido_id) });
      
      // Atualiza usuário local se a role mudou (ex: de aluno para gestor)
      if (res.updated_user) {
        authSetUser(res.updated_user);
      }

      status.textContent = '✓ ' + (res.message || 'Pagamento confirmado e matrículas ativadas!');
      status.className = 'text-xs font-semibold text-green-700 block';
      
      // Esconde botão simular e mostra botão ir painel
      btn.classList.add('hidden');
      document.getElementById('btn-ir-painel').classList.remove('hidden');

      // Limpa dados temporários do pedido recente para evitar reenviar
      sessionStorage.removeItem('act_ultimo_pedido');
    } catch (err) {
      alert(err.message || 'Erro ao simular confirmação.');
      btn.disabled = false;
      btn.textContent = 'Simular Confirmação e Ativar';
    }
  }
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
