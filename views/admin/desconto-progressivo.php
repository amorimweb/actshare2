<?php $pageTitle = 'Desconto Progressivo — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="flex items-center justify-between mb-8">
  <div>
    <h1 class="text-2xl font-bold text-slate-800">Desconto Progressivo</h1>
    <p class="text-xs text-slate-400 mt-1">Faixas de desconto automático por quantidade de vagas compradas do mesmo treinamento.</p>
  </div>
  <button onclick="salvarFaixasProgressivo()" id="btn-salvar-faixas" class="bg-primary text-white text-xs font-semibold uppercase tracking-wider px-4 py-2.5 rounded-lg hover:bg-slate-800 transition-all">Salvar Faixas</button>
</div>

<div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
  <div class="grid grid-cols-[auto_1fr_1fr_1fr] gap-3 items-center text-xs font-bold text-slate-500 uppercase mb-2">
    <span>Faixa</span>
    <span>De (vagas)</span>
    <span>Até (vagas)</span>
    <span>% Desconto</span>
  </div>
  <div id="faixas-progressivo-list" class="space-y-2"></div>
  <div id="faixas-msg" class="hidden mt-4 text-xs rounded-lg px-3 py-2"></div>
</div>

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=10"></script>
<script>
  const TOTAL_FAIXAS = 8;
  document.addEventListener('DOMContentLoaded', carregarFaixasProgressivo);

  async function carregarFaixasProgressivo() {
    const list = document.getElementById('faixas-progressivo-list');
    try {
      const config = await apiFetch(BASE + '/api/admin/configuracoes');
      list.innerHTML = '';
      for (let i = 1; i <= TOTAL_FAIXAS; i++) {
        const isLast = i === TOTAL_FAIXAS;
        const min = config[`desconto_progressivo_faixa${i}_min`] ?? '';
        const max = config[`desconto_progressivo_faixa${i}_max`] ?? '';
        const pct = config[`desconto_progressivo_faixa${i}_percentual`] ?? '';
        const row = document.createElement('div');
        row.className = 'grid grid-cols-[auto_1fr_1fr_1fr] gap-3 items-center';
        row.innerHTML = `
          <span class="text-xs font-bold text-slate-400 w-14">Faixa ${i}</span>
          <input type="number" min="1" id="faixa-${i}-min" value="${min}" class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm">
          <input type="number" min="1" id="faixa-${i}-max" value="${max}" ${isLast ? 'placeholder="sem limite" disabled' : ''} class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm ${isLast ? 'bg-slate-50 text-slate-400' : ''}">
          <input type="number" min="0" max="100" step="0.1" id="faixa-${i}-percentual" value="${pct}" class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm">
        `;
        list.appendChild(row);
      }
    } catch (e) {
      list.innerHTML = `<p class="text-red-500 text-sm">Erro: ${e.message}</p>`;
    }
  }

  async function salvarFaixasProgressivo() {
    const btn = document.getElementById('btn-salvar-faixas');
    const msg = document.getElementById('faixas-msg');
    btn.disabled = true;
    try {
      const body = {};
      for (let i = 1; i <= TOTAL_FAIXAS; i++) {
        body[`desconto_progressivo_faixa${i}_min`] = document.getElementById(`faixa-${i}-min`).value;
        if (i < TOTAL_FAIXAS) body[`desconto_progressivo_faixa${i}_max`] = document.getElementById(`faixa-${i}-max`).value;
        body[`desconto_progressivo_faixa${i}_percentual`] = document.getElementById(`faixa-${i}-percentual`).value;
      }
      await apiPut(BASE + '/api/admin/configuracoes', body);
      msg.className = 'mt-4 text-xs rounded-lg px-3 py-2 bg-green-50 text-green-700 border border-green-200 block';
      msg.textContent = 'Faixas salvas com sucesso!';
    } catch (e) {
      msg.className = 'mt-4 text-xs rounded-lg px-3 py-2 bg-red-50 text-red-700 border border-red-200 block';
      msg.textContent = 'Erro: ' + e.message;
    } finally {
      btn.disabled = false;
    }
  }
</script>

    </div></main></div></body></html>
