<?php $pageTitle = 'Combo — ActShare'; ?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div id="combo-loading" class="max-w-5xl mx-auto px-4 py-16 text-center text-gray-400">
  <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
  <p>Carregando combo...</p>
</div>

<div id="combo-content" class="hidden max-w-5xl mx-auto px-4 py-10">
  <div class="grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
      <div class="text-sm text-secondary font-medium mb-2">Combo</div>
      <h1 id="combo-titulo" class="text-3xl font-bold text-gray-800 mb-4"></h1>
      <p id="combo-descricao" class="text-gray-600 mb-6"></p>

      <h2 class="text-lg font-bold text-gray-800 mb-3">Treinamentos inclusos neste combo</h2>
      <div id="combo-cursos-list" class="space-y-3"></div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-6 h-fit sticky top-24">
      <img id="combo-thumb" src="" alt="" class="w-full rounded-lg mb-4 hidden">
      <div id="combo-preco" class="text-3xl font-bold text-primary mb-1"></div>
      <p id="combo-preco-comparativo" class="text-xs text-gray-400 mb-4"></p>
      <button id="btn-adicionar-carrinho-combo" onclick="adicionarComboAoCarrinho()" class="w-full bg-secondary text-white font-semibold py-3 rounded-lg hover:bg-green-600 transition-colors">
        Adicionar ao Carrinho
      </button>
    </div>
  </div>
</div>

<div id="combo-error" class="hidden max-w-xl mx-auto px-4 py-20 text-center">
  <p class="text-gray-500">Combo não encontrado.</p>
  <a href="<?= BASE_PATH ?>/cursos" class="mt-4 inline-block text-primary hover:underline">← Voltar aos treinamentos</a>
</div>

<script>
  const comboId = <?= (int)($_GET['id'] ?? 0) ?>;
  let comboData = null;

  async function carregarCombo() {
    try {
      comboData = await apiFetch(BASE + '/api/combos/' + comboId);
      renderCombo();
    } catch {
      document.getElementById('combo-loading').classList.add('hidden');
      document.getElementById('combo-error').classList.remove('hidden');
    }
  }

  function renderCombo() {
    document.getElementById('combo-loading').classList.add('hidden');
    document.getElementById('combo-content').classList.remove('hidden');

    document.getElementById('combo-titulo').textContent = comboData.titulo;
    document.getElementById('combo-descricao').textContent = comboData.descricao || '';
    document.getElementById('combo-preco').textContent = 'R$ ' + parseFloat(comboData.preco).toFixed(2).replace('.', ',');

    const somaIndividual = (comboData.cursos || []).reduce((s, c) => s + parseFloat(c.preco), 0);
    if (somaIndividual > parseFloat(comboData.preco)) {
      document.getElementById('combo-preco-comparativo').textContent =
        'Comprando separado: R$ ' + somaIndividual.toFixed(2).replace('.', ',');
    }

    if (comboData.thumb_url) {
      const img = document.getElementById('combo-thumb');
      img.src = comboData.thumb_url;
      img.classList.remove('hidden');
    }

    document.getElementById('combo-cursos-list').innerHTML = (comboData.cursos || []).map(c => `
      <div class="flex items-center gap-4 border border-gray-200 rounded-xl p-4">
        ${c.thumb_url ? `<img src="${c.thumb_url}" class="w-20 h-14 object-cover rounded-lg flex-shrink-0">` : ''}
        <div class="flex-1">
          <p class="font-semibold text-gray-800">${c.titulo}</p>
          <p class="text-xs text-gray-400">${c.carga_horaria_horas || 0}h de conteúdo</p>
        </div>
      </div>
    `).join('');
  }

  function adicionarComboAoCarrinho() {
    let cart = [];
    try { cart = JSON.parse(localStorage.getItem('act_carrinho')) || []; } catch { cart = []; }

    const existingIndex = cart.findIndex(item => item.combo_id == comboId);
    if (existingIndex > -1) {
      cart[existingIndex].vagas = (cart[existingIndex].vagas || 0) + 1;
    } else {
      cart.push({
        combo_id: comboId,
        titulo: comboData.titulo,
        preco: parseFloat(comboData.preco),
        thumb_url: comboData.thumb_url || '',
        vagas: 1,
      });
    }

    localStorage.setItem('act_carrinho', JSON.stringify(cart));
    if (typeof updateCartCountBadge === 'function') updateCartCountBadge();
    window.location.href = BASE + '/carrinho';
  }

  document.addEventListener('DOMContentLoaded', carregarCombo);
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
