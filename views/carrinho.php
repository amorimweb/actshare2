<?php
$pageTitle = 'Meu Carrinho — ActShare';
require __DIR__ . '/layout/header.php';
?>

<div class="max-w-5xl mx-auto px-4 py-10">
  <h1 class="text-3xl font-extrabold text-gray-800 mb-8 tracking-tight">Carrinho de Compras</h1>

  <div class="grid lg:grid-cols-3 gap-8">
    <!-- Listagem de Itens -->
    <div class="lg:col-span-2 space-y-4">
      <div id="carrinho-loading" class="text-center py-12 text-gray-400">
        <div class="inline-block w-6 h-6 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
      </div>
      
      <div id="carrinho-itens-list" class="space-y-4 hidden"></div>
      
      <div id="carrinho-vazio" class="hidden bg-gray-50 border border-gray-100 rounded-2xl p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        <h3 class="text-gray-700 font-bold text-lg mb-2">Seu carrinho está vazio</h3>
        <p class="text-xs text-gray-400 mb-6">Navegue pelo catálogo para adicionar excelentes treinamentos.</p>
        <a href="<?= BASE_PATH ?>/cursos" class="inline-block bg-primary text-white font-semibold px-6 py-2.5 rounded-xl hover:bg-blue-900 transition-colors text-sm shadow-sm">
          Ver Catálogo de Treinamentos
        </a>
      </div>
    </div>

    <!-- Resumo do Pedido -->
    <div class="space-y-6">
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-6 sticky top-24" id="resumo-pedido">
        <h3 class="font-bold text-gray-800 text-lg">Resumo do Pedido</h3>
        
        <div class="space-y-3 text-sm text-gray-600 border-b border-gray-100 pb-4">
          <div class="flex justify-between">
            <span>Subtotal Bruto</span>
            <span id="resumo-subtotal" class="font-medium text-gray-800">R$ 0,00</span>
          </div>
          <div class="flex justify-between text-secondary hidden" id="resumo-desc-prog-block">
            <span>Desconto Progressivo B2B</span>
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

        <!-- Aplicação de Cupom -->
        <div class="space-y-2">
          <label for="input-cupom" class="block text-xs font-semibold text-gray-500 uppercase">Cupom de Desconto</label>
          <div class="flex gap-2">
            <input type="text" id="input-cupom" placeholder="Código do cupom..."
              class="flex-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-shadow uppercase">
            <button onclick="aplicarCupom()" id="btn-cupom" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold px-4 py-2 rounded-lg transition-colors border border-gray-200 shadow-sm">
              Aplicar
            </button>
          </div>
          <p id="cupom-msg" class="hidden text-xs font-medium"></p>
        </div>

        <div class="pt-4 border-t border-gray-100 flex justify-between items-baseline">
          <span class="font-bold text-gray-800 text-base">Total Final</span>
          <span id="resumo-total" class="text-2xl font-extrabold text-primary">R$ 0,00</span>
        </div>

        <div class="space-y-3">
          <button onclick="irParaCheckout()" id="btn-checkout"
            class="w-full bg-primary text-white font-semibold py-3 rounded-xl hover:bg-blue-900 transition-colors text-sm shadow-sm flex items-center justify-center gap-2">
            Ir para o Pagamento
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
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

  document.addEventListener('DOMContentLoaded', () => {
    carregarCarrinho();
  });

  function getCarrinho() {
    try { return JSON.parse(localStorage.getItem(CARRINHO_KEY)) || []; } catch { return []; }
  }

  function saveCarrinho(cart) {
    localStorage.setItem(CARRINHO_KEY, JSON.stringify(cart));
    if (typeof updateCartCountBadge === 'function') {
      updateCartCountBadge();
    }
  }

  function carregarCarrinho() {
    const list = document.getElementById('carrinho-itens-list');
    const loading = document.getElementById('carrinho-loading');
    const vazio = document.getElementById('carrinho-vazio');
    const resumo = document.getElementById('resumo-pedido');
    
    const cart = getCarrinho();
    loading.classList.add('hidden');
    
    // Carrega cupom salvo se houver
    try { cupomAtivo = JSON.parse(localStorage.getItem(CUPOM_KEY)) || null; } catch { cupomAtivo = null; }
    if (cupomAtivo) {
      document.getElementById('input-cupom').value = cupomAtivo.codigo;
      document.getElementById('input-cupom').disabled = true;
      document.getElementById('btn-cupom').textContent = 'Remover';
      document.getElementById('btn-cupom').onclick = removerCupom;
      
      const msg = document.getElementById('cupom-msg');
      msg.textContent = `✓ Cupom ${cupomAtivo.codigo} (${cupomAtivo.tipo === 'porcentagem' ? cupomAtivo.valor + '%' : 'R$ ' + cupomAtivo.valor}) ativo.`;
      msg.className = 'text-xs font-medium text-green-600 block';
    }

    if (cart.length === 0) {
      vazio.classList.remove('hidden');
      list.classList.add('hidden');
      resumo.classList.add('opacity-50', 'pointer-events-none');
      return;
    }

    vazio.classList.add('hidden');
    list.classList.remove('hidden');
    resumo.classList.remove('opacity-50', 'pointer-events-none');

    renderItens(cart);
    calcularResumo(cart);
  }

  function renderItens(cart) {
    const container = document.getElementById('carrinho-itens-list');
    
    container.innerHTML = cart.map((item, idx) => {
      const preco = parseFloat(item.preco);
      const subtotal = preco * item.vagas;
      
      // Info de B2B
      let b2bHtml = '';
      if (item.vagas >= 2 && item.vagas <= 5) {
        b2bHtml = `<span class="inline-block text-[10px] bg-orange-50 text-orange-600 border border-orange-100 rounded px-1.5 py-0.5 font-bold">5% desc. progressivo B2B</span>`;
      } else if (item.vagas >= 6 && item.vagas <= 10) {
        b2bHtml = `<span class="inline-block text-[10px] bg-orange-50 text-orange-600 border border-orange-100 rounded px-1.5 py-0.5 font-bold">10% desc. progressivo B2B</span>`;
      } else if (item.vagas > 10) {
        b2bHtml = `<span class="inline-block text-[10px] bg-orange-50 text-orange-600 border border-orange-100 rounded px-1.5 py-0.5 font-bold">15% desc. progressivo B2B</span>`;
      }

      return `
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex flex-col sm:flex-row gap-5 items-center justify-between">
          <div class="flex items-center gap-4 w-full sm:w-auto">
            ${item.thumb_url
              ? `<img src="${item.thumb_url}" alt="" class="w-16 h-16 rounded-xl object-cover flex-shrink-0">`
              : `<div class="w-16 h-16 rounded-xl bg-gradient-to-br from-primary to-blue-800 flex items-center justify-center text-white/20 font-bold text-xs flex-shrink-0">EAD</div>`
            }
            <div>
              <h4 class="font-bold text-gray-800 text-sm line-clamp-1">${esc(item.titulo)}</h4>
              <p class="text-xs text-gray-400 mt-0.5">Valor unitário: R$ ${preco.toFixed(2).replace('.', ',')}</p>
              <div class="mt-1 flex flex-wrap gap-1.5 items-center">
                ${item.com_prova 
                  ? `<span class="inline-block text-[9px] bg-green-50 text-green-600 border border-green-150 rounded px-1.5 py-0.5 font-bold">Com Prova de Certificação</span>`
                  : `<span class="inline-block text-[9px] bg-gray-50 text-gray-500 border border-gray-150 rounded px-1.5 py-0.5 font-bold">Sem Prova</span>`
                }
                ${b2bHtml}
              </div>
            </div>
          </div>

          <div class="flex items-center gap-6 justify-between w-full sm:w-auto border-t sm:border-t-0 pt-4 sm:pt-0">
            <!-- Controle de Vagas -->
            <div class="flex items-center border border-gray-300 rounded-xl overflow-hidden bg-gray-50 shadow-sm">
              <button onclick="alterarVagas(${idx}, -1)" class="px-3 py-1.5 text-gray-500 hover:bg-gray-100 transition-colors font-bold text-sm focus:outline-none">-</button>
              <span class="px-3 py-1 text-xs font-bold text-gray-700 select-none">${item.vagas} vaga(s)</span>
              <button onclick="alterarVagas(${idx}, 1)" class="px-3 py-1.5 text-gray-500 hover:bg-gray-100 transition-colors font-bold text-sm focus:outline-none">+</button>
            </div>
            
            <!-- Preço e Ação -->
            <div class="text-right flex flex-col items-end">
              <span class="font-extrabold text-gray-800 text-sm">R$ ${subtotal.toFixed(2).replace('.', ',')}</span>
              <button onclick="removerItem(${idx})" class="text-[10px] text-red-500 font-bold hover:text-red-700 hover:underline mt-1.5">Excluir</button>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }

  function alterarVagas(idx, diff) {
    const cart = getCarrinho();
    if (!cart[idx]) return;
    
    cart[idx].vagas = Math.max(1, cart[idx].vagas + diff);
    saveCarrinho(cart);
    carregarCarrinho();
  }

  function removerItem(idx) {
    const cart = getCarrinho();
    cart.splice(idx, 1);
    saveCarrinho(cart);
    
    // Se o carrinho ficou vazio, remove o cupom ativo
    if (cart.length === 0) {
      removerCupom();
    }
    
    carregarCarrinho();
  }

  function calcularResumo(cart) {
    let subtotalBruto = 0;
    let descontoProg = 0;
    
    cart.forEach(item => {
      const preco = parseFloat(item.preco);
      const subtotalItem = preco * item.vagas;
      subtotalBruto += subtotalItem;
      
      // Cálculo progressivo
      let pct = 0;
      if (item.vagas >= 2 && item.vagas <= 5) pct = 5;
      else if (item.vagas >= 6 && item.vagas <= 10) pct = 10;
      else if (item.vagas > 10) pct = 15;
      
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

    // Aplicar desconto de cupom se houver
    let descCupom = 0;
    const descCupomBlock = document.getElementById('resumo-desc-cupom-block');
    if (cupomAtivo) {
      if (cupomAtivo.tipo === 'porcentagem') {
        descCupom = saldo * (parseFloat(cupomAtivo.valor) / 100);
      } else {
        descCupom = parseFloat(cupomAtivo.valor);
      }
      
      descCupom = Math.min(saldo, descCupom); // Desconto não pode exceder o saldo
      document.getElementById('resumo-desc-cupom').textContent = `- R$ ${descCupom.toFixed(2).replace('.', ',')}`;
      descCupomBlock.classList.remove('hidden');
    } else {
      descCupomBlock.classList.add('hidden');
    }

    let totalFinal = Math.max(0.00, saldo - descCupom);
    document.getElementById('resumo-total').textContent = `R$ ${totalFinal.toFixed(2).replace('.', ',')}`;
  }

  async function aplicarCupom() {
    const input = document.getElementById('input-cupom');
    const btn = document.getElementById('btn-cupom');
    const msg = document.getElementById('cupom-msg');
    
    const codigo = input.value.trim();
    if (!codigo) return;

    const user = authGetUser();
    if (!user) {
      alert('Você precisa estar logado para aplicar cupons de desconto.');
      window.location.href = BASE + '/login?redirect=' + encodeURIComponent(location.href);
      return;
    }

    msg.classList.add('hidden');
    btn.disabled = true;
    btn.textContent = '...';

    try {
      const cart = getCarrinho();
      let subtotalBruto = 0;
      let descontoProg = 0;
      cart.forEach(item => {
        const preco = parseFloat(item.preco);
        const subtotalItem = preco * item.vagas;
        subtotalBruto += subtotalItem;
        let pct = 0;
        if (item.vagas >= 2 && item.vagas <= 5) pct = 5;
        else if (item.vagas >= 6 && item.vagas <= 10) pct = 10;
        else if (item.vagas > 10) pct = 15;
        if (pct > 0) {
          descontoProg += subtotalItem * (pct / 100);
        }
      });
      const saldo = subtotalBruto - descontoProg;

      const res = await apiPost(BASE + '/api/checkout/validar-cupom', { codigo, total: saldo });
      cupomAtivo = res.cupom;
      localStorage.setItem(CUPOM_KEY, JSON.stringify(cupomAtivo));
      
      input.disabled = true;
      btn.textContent = 'Remover';
      btn.disabled = false;
      btn.onclick = removerCupom;
      
      msg.textContent = `✓ Cupom ${cupomAtivo.codigo} (${cupomAtivo.tipo === 'porcentagem' ? cupomAtivo.valor + '%' : 'R$ ' + cupomAtivo.valor}) ativo.`;
      msg.className = 'text-xs font-medium text-green-600 block';
      
      // Recarrega resumo
      carregarCarrinho();
    } catch (err) {
      msg.textContent = `✗ ${err.message || 'Cupom inválido.'}`;
      msg.className = 'text-xs font-medium text-red-600 block';
      btn.disabled = false;
      btn.textContent = 'Aplicar';
    }
  }

  function removerCupom() {
    const input = document.getElementById('input-cupom');
    const btn = document.getElementById('btn-cupom');
    const msg = document.getElementById('cupom-msg');

    cupomAtivo = null;
    localStorage.removeItem(CUPOM_KEY);
    
    input.value = '';
    input.disabled = false;
    btn.textContent = 'Aplicar';
    btn.onclick = aplicarCupom;
    btn.disabled = false;
    msg.classList.add('hidden');

    carregarCarrinho();
  }

  function irParaCheckout() {
    const user = authGetUser();
    if (!user) {
      window.location.href = BASE + '/login?redirect=' + encodeURIComponent(BASE + '/checkout');
    } else {
      window.location.href = BASE + '/checkout';
    }
  }

  function esc(s) {
    if (!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
