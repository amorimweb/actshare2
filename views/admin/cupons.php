<?php $pageTitle = 'Cupons de Desconto — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
  <div>
    <h1 class="text-2xl font-bold text-slate-800">Cupons de Desconto</h1>
    <p class="text-xs text-slate-400 mt-1">Crie códigos promocionais de desconto fixo ou percentual aplicados sobre o carrinho de compras.</p>
  </div>
  <button onclick="abrirModalNovoCupom()" class="bg-secondary text-white text-xs font-semibold uppercase tracking-wider px-5 py-3 rounded-lg hover:bg-emerald-600 transition-all shadow-sm">
    + Novo Cupom
  </button>
</div>

<!-- Regras de Desconto (indicação, fidelidade, progressivo) -->
<div class="bg-white rounded-xl border border-slate-200 p-5 mb-6 shadow-sm">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h2 class="font-bold text-slate-800 text-sm">Regras de Desconto</h2>
      <p class="text-xs text-slate-400 mt-0.5">Percentuais usados automaticamente no carrinho — cupom por indicação, fidelidade e desconto progressivo por volume de vagas.</p>
    </div>
    <button onclick="salvarConfiguracoes()" id="btn-salvar-config" class="bg-primary text-white text-xs font-semibold uppercase tracking-wider px-4 py-2.5 rounded-lg hover:bg-slate-800 transition-all">Salvar Regras</button>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <div class="space-y-3">
      <p class="text-[11px] font-bold text-slate-500 uppercase">Cupom por Indicação (BtoC)</p>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-[10px] text-slate-500 mb-0.5">Desconto para indicado (%)</label>
          <input type="number" id="cfg-cupom-indicacao-percentual" min="0" max="100" class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-sm">
        </div>
        <div>
          <label class="block text-[10px] text-slate-500 mb-0.5">Validade (dias)</label>
          <input type="number" id="cfg-cupom-indicacao-validade-dias" min="1" class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-sm">
        </div>
      </div>

      <p class="text-[11px] font-bold text-slate-500 uppercase pt-2">Fidelidade</p>
      <div>
        <label class="block text-[10px] text-slate-500 mb-0.5">Desconto p/ ex-aluno com curso concluído (%)</label>
        <input type="number" id="cfg-desconto-fidelidade-percentual" min="0" max="100" class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-sm">
      </div>
    </div>

    <div class="space-y-2">
      <p class="text-[11px] font-bold text-slate-500 uppercase">Desconto Progressivo por Volume (vagas B2B)</p>
      <div class="grid grid-cols-3 gap-2 items-end">
        <span class="text-[10px] text-slate-400 col-span-3">Faixa 1 (de / até vagas / % desconto)</span>
        <input type="number" id="cfg-desconto-progressivo-faixa1-min" min="1" class="border border-slate-200 rounded-lg px-2 py-1.5 text-xs">
        <input type="number" id="cfg-desconto-progressivo-faixa1-max" min="1" class="border border-slate-200 rounded-lg px-2 py-1.5 text-xs">
        <input type="number" id="cfg-desconto-progressivo-faixa1-percentual" min="0" max="100" class="border border-slate-200 rounded-lg px-2 py-1.5 text-xs">
      </div>
      <div class="grid grid-cols-3 gap-2 items-end">
        <span class="text-[10px] text-slate-400 col-span-3">Faixa 2</span>
        <input type="number" id="cfg-desconto-progressivo-faixa2-min" min="1" class="border border-slate-200 rounded-lg px-2 py-1.5 text-xs">
        <input type="number" id="cfg-desconto-progressivo-faixa2-max" min="1" class="border border-slate-200 rounded-lg px-2 py-1.5 text-xs">
        <input type="number" id="cfg-desconto-progressivo-faixa2-percentual" min="0" max="100" class="border border-slate-200 rounded-lg px-2 py-1.5 text-xs">
      </div>
      <div class="grid grid-cols-3 gap-2 items-end">
        <span class="text-[10px] text-slate-400 col-span-3">Faixa 3 (acima de)</span>
        <input type="number" id="cfg-desconto-progressivo-faixa3-min" min="1" class="border border-slate-200 rounded-lg px-2 py-1.5 text-xs">
        <div></div>
        <input type="number" id="cfg-desconto-progressivo-faixa3-percentual" min="0" max="100" class="border border-slate-200 rounded-lg px-2 py-1.5 text-xs">
      </div>
    </div>
  </div>
  <div id="config-msg" class="hidden mt-3 text-xs rounded-lg px-3 py-2"></div>
</div>

<!-- Tabela de Cupons -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
  <table class="w-full text-left border-collapse text-sm">
    <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
      <tr>
        <th class="px-6 py-4">Código</th>
        <th class="px-6 py-4">Tipo</th>
        <th class="px-6 py-4">Valor</th>
        <th class="px-6 py-4">Validade</th>
        <th class="px-6 py-4">Limite de Usos</th>
        <th class="px-6 py-4">Qtd Usos</th>
        <th class="px-6 py-4">Ações</th>
      </tr>
    </thead>
    <tbody id="cupons-tbody" class="divide-y divide-slate-100">
      <tr>
        <td colspan="7" class="text-center py-12 text-slate-400">Carregando cupons...</td>
      </tr>
    </tbody>
  </table>
</div>

<!-- Modal Criar Cupom -->
<div id="modal-cupom" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl transform transition-all duration-300 scale-95 opacity-0 animate-modalEntrance">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
      <h2 id="modal-cupom-titulo" class="text-lg font-bold text-slate-800">Novo Cupom de Desconto</h2>
      <button onclick="fecharModalCupom()" class="text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <form id="form-cupom" class="space-y-4">
      <input type="hidden" id="cupom-id">
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Código do Cupom *</label>
        <input type="text" id="cupom-codigo" required maxlength="10" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 uppercase" placeholder="Ex: PROMO15">
        <p class="text-[10px] text-slate-400 mt-0.5">Letras e números, máximo 10 caracteres.</p>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tipo de Desconto *</label>
          <select id="cupom-tipo" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 bg-slate-50">
            <option value="porcentagem">Percentual (%)</option>
            <option value="fixo">Valor Fixo (R$)</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Valor *</label>
          <input type="number" id="cupom-valor" required min="0.01" step="0.01" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Ex: 15.00">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Validade *</label>
          <input type="date" id="cupom-validade" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 bg-slate-50">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Limite de Compras</label>
          <input type="number" id="cupom-limite" min="1" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Ex: 100">
          <p class="text-[10px] text-slate-400 mt-0.5">Deixe em branco para ilimitado.</p>
        </div>
      </div>

      <!-- Erros -->
      <div id="cupom-erro" class="hidden bg-red-50 border border-red-200 text-red-750 text-xs font-semibold rounded-lg px-3 py-2"></div>

      <!-- Botões -->
      <div class="flex gap-3 pt-3 border-t border-slate-100">
        <button type="submit" id="btn-salvar-cupom" class="flex-1 bg-primary text-white text-xs font-bold uppercase tracking-wider py-3 rounded-lg hover:bg-slate-800 transition-colors shadow-sm">
          Salvar Cupom
        </button>
        <button type="button" onclick="fecharModalCupom()" class="px-5 py-3 border border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-slate-50 transition-colors">
          Cancelar
        </button>
      </div>
    </form>
  </div>
</div>

<style>
  @keyframes modalEntrance {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
  }
  .animate-modalEntrance {
    animation: modalEntrance 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }
</style>

<script>
  function esc(s) {
    if (!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
  }

  let _cuponsList = [];

  document.addEventListener('DOMContentLoaded', () => {
    carregarCuponsAdmin();
    carregarConfiguracoes();
  });

  const CONFIG_CAMPOS = [
    'cupom_indicacao_percentual', 'cupom_indicacao_validade_dias', 'desconto_fidelidade_percentual',
    'desconto_progressivo_faixa1_min', 'desconto_progressivo_faixa1_max', 'desconto_progressivo_faixa1_percentual',
    'desconto_progressivo_faixa2_min', 'desconto_progressivo_faixa2_max', 'desconto_progressivo_faixa2_percentual',
    'desconto_progressivo_faixa3_min', 'desconto_progressivo_faixa3_percentual',
  ];

  async function carregarConfiguracoes() {
    try {
      const cfg = await apiFetch(BASE + '/api/admin/configuracoes');
      CONFIG_CAMPOS.forEach(chave => {
        const el = document.getElementById('cfg-' + chave.replace(/_/g, '-'));
        if (el) el.value = cfg[chave];
      });
    } catch (e) { console.error('Erro ao carregar configurações:', e); }
  }

  async function salvarConfiguracoes() {
    const btn = document.getElementById('btn-salvar-config');
    const msg = document.getElementById('config-msg');
    btn.disabled = true;
    const body = {};
    CONFIG_CAMPOS.forEach(chave => {
      const el = document.getElementById('cfg-' + chave.replace(/_/g, '-'));
      if (el) body[chave] = el.value;
    });
    try {
      await apiPut(BASE + '/api/admin/configuracoes', body);
      msg.className = 'mt-3 text-xs rounded-lg px-3 py-2 bg-green-50 text-green-700 border border-green-200';
      msg.textContent = 'Regras salvas com sucesso.';
      msg.classList.remove('hidden');
    } catch (e) {
      msg.className = 'mt-3 text-xs rounded-lg px-3 py-2 bg-red-50 text-red-700 border border-red-200';
      msg.textContent = e.message;
      msg.classList.remove('hidden');
    } finally {
      btn.disabled = false;
      setTimeout(() => msg.classList.add('hidden'), 4000);
    }
  }

  async function carregarCuponsAdmin() {
    const tbody = document.getElementById('cupons-tbody');
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-12 text-slate-400">Carregando cupons...</td></tr>';

    try {
      _cuponsList = await apiFetch(BASE + '/api/admin/cupons');
      if (!_cuponsList.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-12 text-slate-400">Nenhum cupom cadastrado.</td></tr>';
        return;
      }

      tbody.innerHTML = _cuponsList.map(c => {
        const valStr = c.tipo === 'porcentagem' ? `${parseFloat(c.valor)}%` : `R$ ${parseFloat(c.valor).toFixed(2).replace('.', ',')}`;
        const dtValidade = new Date(c.validade).toLocaleDateString('pt-BR');
        
        return `
          <tr class="hover:bg-slate-50/50 transition-colors">
            <td class="px-6 py-4 font-bold text-slate-800 tracking-wider">${esc(c.codigo)}</td>
            <td class="px-6 py-4 text-xs font-medium text-slate-500">
              <span class="px-2 py-1 rounded-full ${c.tipo === 'porcentagem' ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-700'}">
                ${c.tipo === 'porcentagem' ? 'Percentual' : 'Fixo'}
              </span>
            </td>
            <td class="px-6 py-4 font-semibold text-slate-700">${valStr}</td>
            <td class="px-6 py-4 text-slate-500 text-xs">${dtValidade}</td>
            <td class="px-6 py-4 text-slate-500 text-xs font-medium">${c.limite_uso !== null ? c.limite_uso : 'Ilimitado'}</td>
            <td class="px-6 py-4 font-bold text-slate-800 text-xs">${c.usos} uso(s)</td>
            <td class="px-6 py-4 text-xs">
              <button onclick="abrirModalEditarCupom(${c.id})" class="text-primary font-bold hover:underline mr-3">Editar</button>
              <button onclick="excluirCupomAdmin(${c.id})" class="text-red-500 font-bold hover:underline">Excluir</button>
            </td>
          </tr>
        `;
      }).join('');
    } catch (e) {
      tbody.innerHTML = `<tr><td colspan="7" class="text-center py-12 text-red-500">Erro ao buscar cupons: ${e.message}</td></tr>`;
    }
  }

  function abrirModalNovoCupom() {
    document.getElementById('modal-cupom-titulo').textContent = 'Novo Cupom de Desconto';
    document.getElementById('cupom-id').value = '';
    document.getElementById('cupom-codigo').value = '';
    document.getElementById('cupom-tipo').value = 'porcentagem';
    document.getElementById('cupom-valor').value = '';
    document.getElementById('cupom-validade').value = '';
    document.getElementById('cupom-limite').value = '';
    document.getElementById('cupom-erro').classList.add('hidden');

    const modal = document.getElementById('modal-cupom');
    modal.classList.remove('hidden');
    setTimeout(() => {
      modal.querySelector('.bg-white').classList.remove('scale-95', 'opacity-0');
    }, 50);
  }

  function abrirModalEditarCupom(id) {
    const c = _cuponsList.find(x => x.id === id);
    if (!c) return;

    document.getElementById('modal-cupom-titulo').textContent = 'Editar Cupom de Desconto';
    document.getElementById('cupom-id').value = c.id;
    document.getElementById('cupom-codigo').value = c.codigo;
    document.getElementById('cupom-tipo').value = c.tipo;
    document.getElementById('cupom-valor').value = parseFloat(c.valor);
    document.getElementById('cupom-validade').value = (c.validade || '').substring(0, 10);
    document.getElementById('cupom-limite').value = c.limite_uso !== null ? c.limite_uso : '';
    document.getElementById('cupom-erro').classList.add('hidden');

    const modal = document.getElementById('modal-cupom');
    modal.classList.remove('hidden');
    setTimeout(() => {
      modal.querySelector('.bg-white').classList.remove('scale-95', 'opacity-0');
    }, 50);
  }

  function fecharModalCupom() {
    const modal = document.getElementById('modal-cupom');
    const box = modal.querySelector('.bg-white');
    box.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
      modal.classList.add('hidden');
    }, 200);
  }

  async function excluirCupomAdmin(id) {
    if (!confirm('Deseja excluir permanentemente este cupom?')) return;
    try {
      await apiDelete(BASE + '/api/admin/cupons/' + id);
      carregarCuponsAdmin();
    } catch (e) {
      alert('Erro ao excluir: ' + e.message);
    }
  }

  document.getElementById('form-cupom').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btn-salvar-cupom');
    const err = document.getElementById('cupom-erro');

    btn.disabled = true;
    btn.textContent = 'Gravando...';
    err.classList.add('hidden');

    const payload = {
      codigo:     document.getElementById('cupom-codigo').value.trim(),
      tipo:       document.getElementById('cupom-tipo').value,
      valor:      parseFloat(document.getElementById('cupom-valor').value) || 0,
      validade:   document.getElementById('cupom-validade').value,
      limite_uso: document.getElementById('cupom-limite').value !== '' ? parseInt(document.getElementById('cupom-limite').value) : null
    };
    const id = document.getElementById('cupom-id').value;

    try {
      if (id) await apiPut(BASE + '/api/admin/cupons/' + id, payload);
      else    await apiPost(BASE + '/api/admin/cupons', payload);
      fecharModalCupom();
      carregarCuponsAdmin();
    } catch (ex) {
      err.textContent = ex.message;
      err.classList.remove('hidden');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Salvar Cupom';
    }
  });
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
