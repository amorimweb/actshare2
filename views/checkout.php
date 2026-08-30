<?php
$pageTitle = 'Finalizar Compra — ActShare';
require __DIR__ . '/layout/header.php';
?>

<div class="max-w-5xl mx-auto px-4 py-10">
  <h1 class="text-3xl font-extrabold text-gray-800 mb-8 tracking-tight">Finalizar Compra</h1>

  <div class="grid lg:grid-cols-3 gap-8">
    <!-- Formulários de Faturamento e Pagamento -->
    <div class="lg:col-span-2 space-y-6">
      <!-- 1. Dados de Faturamento -->
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
          <span class="w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs">1</span>
          Dados de Faturamento
        </h3>
        
        <form id="billing-form" class="grid sm:grid-cols-2 gap-4">
          <div class="sm:col-span-2">
            <label for="bill-nome" class="block text-xs font-semibold text-gray-500 uppercase mb-1">Nome Completo / Razão Social</label>
            <input type="text" id="bill-nome" required
              class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-shadow">
          </div>

          <div>
            <label for="bill-email" class="block text-xs font-semibold text-gray-500 uppercase mb-1">E-mail</label>
            <input type="email" id="bill-email" disabled
              class="w-full px-3.5 py-2.5 bg-gray-100 border border-gray-200 rounded-xl text-sm text-gray-400 focus:outline-none cursor-not-allowed">
          </div>

          <div>
            <label for="bill-whatsapp" class="block text-xs font-semibold text-gray-500 uppercase mb-1">WhatsApp / Celular</label>
            <input type="text" id="bill-whatsapp" data-mask="telefone" placeholder="(11) 99999-9999" required maxlength="15"
              class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-shadow">
          </div>

          <div class="sm:col-span-2">
            <label for="bill-documento" class="block text-xs font-semibold text-gray-500 uppercase mb-1">CPF ou CNPJ</label>
            <input type="text" id="bill-documento" data-mask="documento" placeholder="000.000.000-00 ou 00.000.000/0000-00" required maxlength="18"
              class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-shadow">
          </div>
        </form>
      </div>

      <!-- 2. Método de Pagamento -->
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-6">
        <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
          <span class="w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs">2</span>
          Método de Pagamento
        </h3>

        <!-- Abas dos Métodos -->
        <div class="grid grid-cols-3 gap-3">
          <button onclick="selecionarMetodo('pix')" id="btn-metodo-pix"
            class="flex flex-col items-center justify-center py-4 border-2 rounded-xl transition-all focus:outline-none select-none border-primary bg-primary/5 text-primary">
            <svg class="w-6 h-6 mb-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 12h3v8h14v-8h3L12 2zm1 16H11v-2h2v2zm0-4H11V8h2v6z"/></svg>
            <span class="text-xs font-bold">PIX</span>
            <span class="text-[10px] text-gray-400 mt-0.5 hidden sm:block">Aprovação imediata</span>
          </button>

          <button onclick="selecionarMetodo('boleto')" id="btn-metodo-boleto"
            class="flex flex-col items-center justify-center py-4 border-2 rounded-xl transition-all focus:outline-none select-none border-gray-200 hover:border-gray-300 text-gray-600">
            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="text-xs font-bold">Boleto</span>
            <span class="text-[10px] text-gray-400 mt-0.5 hidden sm:block">1 a 2 dias úteis</span>
          </button>

          <button onclick="selecionarMetodo('cartao')" id="btn-metodo-cartao"
            class="flex flex-col items-center justify-center py-4 border-2 rounded-xl transition-all focus:outline-none select-none border-gray-200 hover:border-gray-300 text-gray-600">
            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            <span class="text-xs font-bold">Cartão</span>
            <span class="text-[10px] text-gray-400 mt-0.5 hidden sm:block">Até 12x s/ juros</span>
          </button>
        </div>

        <!-- Seções Específicas -->
        <!-- PIX Info -->
        <div id="info-pix" class="bg-gray-50 border border-gray-150 rounded-xl p-4 text-sm text-gray-600 space-y-2">
          <p class="font-bold text-gray-700">✓ O pagamento via PIX é o mais rápido e seguro.</p>
          <p>Ao finalizar, geraremos um QR Code dinâmico e o código "Copia e Cola" para pagamento instantâneo. Suas matrículas serão liberadas no mesmo instante!</p>
        </div>

        <!-- Boleto Info -->
        <div id="info-boleto" class="hidden bg-gray-50 border border-gray-150 rounded-xl p-4 text-sm text-gray-600 space-y-2">
          <p class="font-bold text-gray-700">✓ Pagamento via Boleto Bancário.</p>
          <p>O boleto gerado terá validade de 3 dias corridos. O banco compensará em até 2 dias úteis após o pagamento. As vagas estarão disponíveis somente após a compensação.</p>
        </div>

        <!-- Cartão Form -->
        <div id="info-cartao" class="hidden bg-gray-50 border border-gray-150 rounded-xl p-5 space-y-4">
          <div class="flex items-center gap-2 text-amber-600 bg-amber-50 border border-amber-100 rounded-lg p-3 text-xs">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span><strong>Ambiente de Testes:</strong> você pode digitar qualquer número fictício de cartão para simulação.</span>
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <label for="card-numero" class="block text-[10px] font-semibold text-gray-500 uppercase mb-0.5">Número do Cartão</label>
              <input type="text" id="card-numero" placeholder="4000 1234 5678 9010"
                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-shadow">
            </div>

            <div class="sm:col-span-2">
              <label for="card-nome" class="block text-[10px] font-semibold text-gray-500 uppercase mb-0.5">Nome Escrito no Cartão</label>
              <input type="text" id="card-nome" placeholder="NOME DO PORTADOR"
                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-shadow uppercase">
            </div>

            <div>
              <label for="card-validade" class="block text-[10px] font-semibold text-gray-500 uppercase mb-0.5">Validade</label>
              <input type="text" id="card-validade" placeholder="MM/AA"
                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-shadow">
            </div>

            <div>
              <label for="card-cvv" class="block text-[10px] font-semibold text-gray-500 uppercase mb-0.5">CVV</label>
              <input type="text" id="card-cvv" placeholder="123"
                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-shadow">
            </div>

            <div class="sm:col-span-2">
              <label for="card-parcelas" class="block text-[10px] font-semibold text-gray-500 uppercase mb-0.5">Opções de Parcelamento</label>
              <select id="card-parcelas"
                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-shadow">
                <option value="1">1x à vista</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Resumo do Pedido -->
    <div class="space-y-6">
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-6 sticky top-24" id="checkout-resumo">
        <h3 class="font-bold text-gray-800 text-lg">Resumo do Pedido</h3>
        
        <!-- Itens Comprados -->
        <div id="checkout-itens-mini" class="space-y-3 max-h-40 overflow-y-auto pr-1 border-b border-gray-100 pb-4"></div>

        <div class="space-y-3 text-sm text-gray-600 border-b border-gray-100 pb-4">
          <div class="flex justify-between">
            <span>Subtotal Bruto</span>
            <span id="resumo-subtotal" class="font-medium text-gray-800">R$ 0,00</span>
          </div>
          <div class="flex justify-between text-secondary hidden" id="resumo-desc-prog-block">
            <span>Desconto Progressivo</span>
            <span id="resumo-desc-prog">- R$ 0,00</span>
          </div>
          <div class="flex justify-between text-secondary hidden" id="resumo-desc-fidelidade-block">
            <span>Desconto Fidelidade (10%)</span>
            <span id="resumo-desc-fidelidade">- R$ 0,00</span>
          </div>
          <div class="flex justify-between text-secondary hidden" id="resumo-desc-cupom-block">
            <span>Cupom Aplicado</span>
            <span id="resumo-desc-cupom">- R$ 0,00</span>
          </div>
        </div>

        <div class="pt-2 flex justify-between items-baseline">
          <span class="font-bold text-gray-800 text-base">Total Final</span>
          <span id="resumo-total" class="text-2xl font-extrabold text-primary">R$ 0,00</span>
        </div>

        <div class="space-y-3">
          <button onclick="finalizarCompra()" id="btn-finalizar"
            class="w-full bg-primary text-white font-semibold py-3 rounded-xl hover:bg-blue-900 transition-colors text-sm shadow-sm flex items-center justify-center gap-2">
            Finalizar Compra
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          </button>

          <a href="<?= BASE_PATH ?>/cursos"
            class="w-full bg-white border border-gray-300 text-gray-700 font-semibold py-3 rounded-xl hover:bg-gray-50 transition-all text-sm shadow-sm flex items-center justify-center gap-2">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Continuar Comprando
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const CARRINHO_KEY = 'act_carrinho';
  const CUPOM_KEY = 'act_cupom_ativo';
  let cupomAtivo = null;
  let formaPagamento = 'pix'; // 'pix', 'boleto', 'cartao'
  let userLogged = null;
  let totalLiquidoGlobal = 0.00;
  let faixasDesconto = [];

  document.addEventListener('DOMContentLoaded', async () => {
    try { faixasDesconto = await apiFetch(BASE + '/api/configuracoes/desconto-progressivo'); } catch { faixasDesconto = []; }
    verificarAutenticacao();
  });

  function percentualProgressivo(vagas) {
    for (let i = faixasDesconto.length - 1; i >= 0; i--) {
      const f = faixasDesconto[i];
      if (vagas >= f.min && (f.max === null || vagas <= f.max)) return f.percentual;
    }
    return 0;
  }

  function verificarAutenticacao() {
    userLogged = authGetUser();
    if (!userLogged) {
      // Redireciona para o cadastro com retorno ao checkout
      window.location.href = BASE + '/registro?redirect=' + encodeURIComponent(BASE + '/checkout');
      return;
    }

    // Preenche dados do usuário
    document.getElementById('bill-nome').value = userLogged.nome || '';
    document.getElementById('bill-email').value = userLogged.email || '';
    document.getElementById('bill-whatsapp').value = userLogged.telefone || '';
    document.getElementById('bill-documento').value = userLogged.documento || '';

    // Tenta carregar informações anteriores se salvas (CPF/WhatsApp) ou deixa em branco para preencher
    carregarCarrinhoCheckout();
  }

  function getCarrinho() {
    try { return JSON.parse(localStorage.getItem(CARRINHO_KEY)) || []; } catch { return []; }
  }

  function carregarCarrinhoCheckout() {
    const cart = getCarrinho();
    if (cart.length === 0) {
      alert('Seu carrinho está vazio.');
      window.location.href = BASE + '/carrinho';
      return;
    }

    try { cupomAtivo = JSON.parse(localStorage.getItem(CUPOM_KEY)) || null; } catch { cupomAtivo = null; }

    renderItensMini(cart);
    calcularResumo(cart);
  }

  function renderItensMini(cart) {
    const container = document.getElementById('checkout-itens-mini');
    container.innerHTML = cart.map(item => {
      const preco = parseFloat(item.preco);
      const subtotal = preco * item.vagas;
      return `
        <div class="flex justify-between items-start gap-2 text-xs">
          <div class="flex-1">
            <span class="font-semibold text-gray-800 line-clamp-1">${esc(item.titulo)}</span>
            <span class="text-gray-400 block">${item.vagas} vaga(s) ${item.exames_selecionados ? '+ Exame ' + item.exames_selecionados.replace(/,/g, '/') : ''}</span>
          </div>
          <span class="font-bold text-gray-700 text-right whitespace-nowrap">R$ ${subtotal.toFixed(2).replace('.', ',')}</span>
        </div>
      `;
    }).join('');
  }

  async function calcularResumo(cart) {
    let subtotalBruto = 0;
    let descontoProg = 0;
    
    cart.forEach(item => {
      const preco = parseFloat(item.preco);
      const subtotalItem = preco * item.vagas;
      subtotalBruto += subtotalItem;
      
      const pct = percentualProgressivo(item.vagas);
      if (pct > 0) {
        descontoProg += subtotalItem * (pct / 100);
      }
    });

    document.getElementById('resumo-subtotal').textContent = `R$ ${subtotalBruto.toFixed(2).replace('.', ',')}`;
    
    const descProgBlock = document.getElementById('resumo-desc-prog-block');
    if (descontoProg > 0) {
      document.getElementById('resumo-desc-prog').textContent = `- R$ ${descontoProg.toFixed(2).replace('.', ',')}`;
      descProgBlock.classList.remove('hidden');
    } else {
      descProgBlock.classList.add('hidden');
    }

    let saldo = subtotalBruto - descontoProg;

    // Fidelidade (ex-aluno com curso concluído) - vamos bater na API ou simular?
    // Para simplificar e bater igual na API: faremos uma query rápida via fetch na API se pudermos, ou simulamos aqui também.
    // Mas a API de criar-pedido calcula isso com precisão no backend. 
    // Podemos buscar as matrículas do usuário logado para verificar se ele possui algum concluído
    let temConcluido = false;
    try {
      const matriculas = await apiFetch(BASE + '/api/aluno/matriculas');
      temConcluido = matriculas.some(m => m.concluido == 1);
    } catch {}

    let descontoFidelidade = 0;
    const descFidelidadeBlock = document.getElementById('resumo-desc-fidelidade-block');
    if (temConcluido) {
      descontoFidelidade = saldo * 0.10;
      document.getElementById('resumo-desc-fidelidade').textContent = `- R$ ${descontoFidelidade.toFixed(2).replace('.', ',')}`;
      descFidelidadeBlock.classList.remove('hidden');
    } else {
      descFidelidadeBlock.classList.add('hidden');
    }

    saldo -= descontoFidelidade;

    // Aplicar desconto de cupom se houver
    let descCupom = 0;
    const descCupomBlock = document.getElementById('resumo-desc-cupom-block');
    if (cupomAtivo) {
      if (cupomAtivo.tipo === 'porcentagem') {
        descCupom = saldo * (parseFloat(cupomAtivo.valor) / 100);
      } else {
        descCupom = parseFloat(cupomAtivo.valor);
      }
      
      descCupom = Math.min(saldo, descCupom);
      document.getElementById('resumo-desc-cupom').textContent = `- R$ ${descCupom.toFixed(2).replace('.', ',')}`;
      descCupomBlock.classList.remove('hidden');
    } else {
      descCupomBlock.classList.add('hidden');
    }

    totalLiquidoGlobal = Math.max(0.00, saldo - descCupom);
    document.getElementById('resumo-total').textContent = `R$ ${totalLiquidoGlobal.toFixed(2).replace('.', ',')}`;

    // Atualiza parcelas se for cartão
    atualizarOpcoesParcelamento();
  }

  function selecionarMetodo(metodo) {
    formaPagamento = metodo;
    
    // Classes de botões
    const btns = {
      pix: document.getElementById('btn-metodo-pix'),
      boleto: document.getElementById('btn-metodo-boleto'),
      cartao: document.getElementById('btn-metodo-cartao')
    };

    // Blocos de info
    const infos = {
      pix: document.getElementById('info-pix'),
      boleto: document.getElementById('info-boleto'),
      cartao: document.getElementById('info-cartao')
    };

    // Reset todos
    Object.keys(btns).forEach(key => {
      btns[key].className = "flex flex-col items-center justify-center py-4 border-2 rounded-xl transition-all focus:outline-none select-none border-gray-200 hover:border-gray-300 text-gray-600";
      infos[key].classList.add('hidden');
    });

    // Ativa selecionado
    btns[metodo].className = "flex flex-col items-center justify-center py-4 border-2 rounded-xl transition-all focus:outline-none select-none border-primary bg-primary/5 text-primary";
    infos[metodo].classList.remove('hidden');

    if (metodo === 'cartao') {
      atualizarOpcoesParcelamento();
    }
  }

  function atualizarOpcoesParcelamento() {
    const select = document.getElementById('card-parcelas');
    if (!select) return;
    
    select.innerHTML = '';
    
    // Até 12x sem juros
    for (let i = 1; i <= 12; i++) {
      const valorParcela = totalLiquidoGlobal / i;
      const option = document.createElement('option');
      option.value = i;
      option.textContent = i === 1 
        ? `1x à vista (R$ ${totalLiquidoGlobal.toFixed(2).replace('.', ',')})`
        : `${i}x sem juros de R$ ${valorParcela.toFixed(2).replace('.', ',')}`;
      select.appendChild(option);
    }
  }

  async function finalizarCompra() {
    // 1. Valida Billing details
    const billingForm = document.getElementById('billing-form');
    if (!billingForm.reportValidity()) {
      return;
    }
    const docInput = document.getElementById('bill-documento');
    const telInput = document.getElementById('bill-whatsapp');
    if (!documentoValido(docInput.value)) {
      alert('Informe um CPF (11 dígitos) ou CNPJ (14 dígitos) válido.');
      docInput.focus();
      return;
    }
    if (!telefoneValido(telInput.value)) {
      alert('Informe um telefone válido, com DDD.');
      telInput.focus();
      return;
    }

    // 2. Valida Cartão se for o selecionado
    if (formaPagamento === 'cartao') {
      const cardNum = document.getElementById('card-numero').value.trim();
      const cardName = document.getElementById('card-nome').value.trim();
      const cardVal = document.getElementById('card-validade').value.trim();
      const cardCvv = document.getElementById('card-cvv').value.trim();

      if (!cardNum || !cardName || !cardVal || !cardCvv) {
        alert('Por favor, preencha todos os campos do cartão de crédito.');
        return;
      }
    }

    const btn = document.getElementById('btn-finalizar');
    btn.disabled = true;
    btn.textContent = 'Processando pedido...';

    // Prepara payload
    const cart = getCarrinho();
    const itens = cart.map(item => item.combo_id
      ? { combo_id: parseInt(item.combo_id), vagas: parseInt(item.vagas) }
      : { curso_id: parseInt(item.curso_id), vagas: parseInt(item.vagas), com_prova: parseInt(item.com_prova || 0), exames_selecionados: item.exames_selecionados || '' }
    );

    const cupomCodigo = cupomAtivo ? cupomAtivo.codigo : '';

    try {
      const payload = {
        itens,
        cupom_codigo: cupomCodigo,
        forma_pagamento: formaPagamento
      };

      const res = await apiPost(BASE + '/api/checkout/criar-pedido', payload);
      
      // Sucesso! Limpa o carrinho
      localStorage.removeItem(CARRINHO_KEY);
      localStorage.removeItem(CUPOM_KEY);
      if (typeof updateCartCountBadge === 'function') {
        updateCartCountBadge();
      }

      // Salva dados do pedido criado na sessionStorage para a tela de confirmação
      sessionStorage.setItem('act_ultimo_pedido', JSON.stringify(res));

      // Redireciona
      window.location.href = BASE + '/pedido/confirmacao';

    } catch (err) {
      alert(err.message || 'Erro ao finalizar a compra. Tente novamente.');
      btn.disabled = false;
      btn.textContent = 'Finalizar Compra';
    }
  }

  function esc(s) {
    if (!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
