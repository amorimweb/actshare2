<?php $pageTitle = 'Emissão Manual de Certificados — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
  <div>
    <h1 class="text-2xl font-bold text-slate-800">Emissão de Certificados</h1>
    <p class="text-xs text-slate-400 mt-1">Gere certificados avulsos de forma manual ou importe vários alunos em lote colando dados CSV.</p>
  </div>
  <div class="flex gap-2">
    <button onclick="abrirModalImportarLote()" class="border border-slate-200 bg-white text-slate-700 text-xs font-semibold uppercase tracking-wider px-5 py-3 rounded-lg hover:bg-slate-50 transition-all shadow-sm">
      Importar Lote (CSV)
    </button>
    <button onclick="abrirModalNovoCertManual()" class="bg-secondary text-white text-xs font-semibold uppercase tracking-wider px-5 py-3 rounded-lg hover:bg-emerald-600 transition-all shadow-sm">
      + Emitir Individual
    </button>
  </div>
</div>

<!-- Tabela de Certificados Manuais -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
  <table class="w-full text-left border-collapse text-sm">
    <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
      <tr>
        <th class="px-6 py-4">Nome do Aluno</th>
        <th class="px-6 py-4">Treinamento</th>
        <th class="px-6 py-4">Carga Horária</th>
        <th class="px-6 py-4">Data Conclusão</th>
        <th class="px-6 py-4">Tipo</th>
        <th class="px-6 py-4">Código Autenticidade</th>
        <th class="px-6 py-4">Ações</th>
      </tr>
    </thead>
    <tbody id="certificados-tbody" class="divide-y divide-slate-100">
      <tr>
        <td colspan="7" class="text-center py-12 text-slate-400">Carregando certificados...</td>
      </tr>
    </tbody>
  </table>
</div>

<!-- Modal Emissão Individual -->
<div id="modal-cert-manual" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0 animate-modalEntrance">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
      <h2 class="text-lg font-bold text-slate-800">Emitir Certificado Avulso</h2>
      <button onclick="fecharModalCertManual()" class="text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <form id="form-cert-manual" class="space-y-4">
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nome Completo do Aluno *</label>
        <input type="text" id="cert-cliente-nome" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Ex: Robson da Silva">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nome do Curso / Treinamento *</label>
        <input type="text" id="cert-curso-nome" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Ex: ISO 9001:2015 Sistema de Gestão da Qualidade">
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Carga Horária (h) *</label>
          <input type="number" id="cert-carga" required min="1" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Ex: 40">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Data de Conclusão *</label>
          <input type="date" id="cert-data" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 bg-slate-50">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tipo de Texto *</label>
          <select id="cert-tipo" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 bg-slate-50">
            <option value="participacao">Participou do treinamento...</option>
            <option value="aprovacao">Aprovado com sucesso na avaliação...</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nome do Instrutor *</label>
          <input type="text" id="cert-instrutor" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Ex: Prof. Carlos Eduardo">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">URL da Assinatura (JPG/PNG)</label>
        <input type="url" id="cert-assinatura" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="https://exemplo.com/assinatura.png">
      </div>

      <!-- Erros -->
      <div id="cert-erro" class="hidden bg-red-50 border border-red-200 text-red-750 text-xs font-semibold rounded-lg px-3 py-2"></div>

      <!-- Botões -->
      <div class="flex gap-3 pt-3 border-t border-slate-100">
        <button type="submit" id="btn-salvar-cert" class="flex-1 bg-primary text-white text-xs font-bold uppercase tracking-wider py-3 rounded-lg hover:bg-slate-800 transition-colors shadow-sm">
          Emitir Certificado
        </button>
        <button type="button" onclick="fecharModalCertManual()" class="px-5 py-3 border border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-slate-50 transition-colors">
          Cancelar
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Importação Lote (CSV) -->
<div id="modal-cert-lote" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-xl p-6 shadow-2xl max-h-[95vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0 animate-modalEntrance">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
      <h2 class="text-lg font-bold text-slate-800">Importação em Lote (CSV)</h2>
      <button onclick="fecharModalCertLote()" class="text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <div class="space-y-4">
      <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-800 leading-relaxed">
        <strong>Como importar:</strong> Cole os dados no formato CSV abaixo. Cada linha deve conter:<br>
        <code class="font-bold block my-1 p-1 bg-white/60 border border-blue-200/50 rounded">Nome Completo, Nome do Curso, Carga Horária, Data (AAAA-MM-DD), Tipo(participacao ou aprovacao), Nome do Instrutor</code>
        <em>Exemplo:</em><br>
        <code class="block mt-1 p-1 bg-white/60 border border-blue-200/50 rounded">Ana Souza, ISO 9001:2015 SGQ, 20, 2026-06-15, participacao, Prof. Marcos</code>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Cole as linhas do CSV *</label>
        <textarea id="lote-csv" rows="6" class="w-full border border-slate-200 rounded-lg p-3 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Ana Souza, ISO 9001:2015 SGQ, 20, 2026-06-15, participacao, Prof. Marcos&#10;Bruno Lima, Auditor Interno, 30, 2026-06-16, aprovacao, Prof. Marcos"></textarea>
      </div>

      <div id="lote-erro" class="hidden bg-red-50 border border-red-200 text-red-750 text-xs font-semibold rounded-lg px-3 py-2"></div>
      <div id="lote-sucesso" class="hidden bg-green-50 border border-green-200 text-green-750 text-xs font-semibold rounded-lg px-3 py-2"></div>

      <!-- Botões -->
      <div class="flex gap-3 pt-3 border-t border-slate-100">
        <button type="button" id="btn-processar-lote" onclick="processarLoteCSV()" class="flex-1 bg-primary text-white text-xs font-bold uppercase tracking-wider py-3 rounded-lg hover:bg-slate-800 transition-colors shadow-sm">
          Processar e Gravar
        </button>
        <button type="button" onclick="fecharModalCertLote()" class="px-5 py-3 border border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-slate-50 transition-colors">
          Fechar
        </button>
      </div>
    </div>
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

  let _certificadosList = [];

  document.addEventListener('DOMContentLoaded', () => {
    carregarCertificadosAdmin();
  });

  async function carregarCertificadosAdmin() {
    const tbody = document.getElementById('certificados-tbody');
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-12 text-slate-400">Carregando certificados...</td></tr>';

    try {
      _certificadosList = await apiFetch(BASE + '/api/admin/certificados');
      if (!_certificadosList.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-12 text-slate-400">Nenhum certificado emitido de forma manual.</td></tr>';
        return;
      }

      tbody.innerHTML = _certificadosList.map(c => {
        const dtConclusao = new Date(c.data_conclusao).toLocaleDateString('pt-BR');
        
        return `
          <tr class="hover:bg-slate-50/50 transition-colors">
            <td class="px-6 py-4 font-bold text-slate-800 text-xs sm:text-sm">${esc(c.cliente_nome)}</td>
            <td class="px-6 py-4 font-medium text-slate-700 text-xs">${esc(c.curso_nome)}</td>
            <td class="px-6 py-4 text-slate-500 text-xs">${c.carga_horaria}h</td>
            <td class="px-6 py-4 text-slate-500 text-xs">${dtConclusao}</td>
            <td class="px-6 py-4 text-xs font-semibold text-slate-500">
              <span class="px-2.5 py-0.5 rounded-full ${c.tipo_texto === 'aprovacao' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700'}">
                ${c.tipo_texto === 'aprovacao' ? 'Aprovação' : 'Participação'}
              </span>
            </td>
            <td class="px-6 py-4 font-bold text-slate-800 text-xs tracking-wider">${esc(c.codigo_autenticidade)}</td>
            <td class="px-6 py-4 text-xs">
              <div class="flex items-center gap-3">
                <a href="${BASE}/validar-certificado?codigo=${c.codigo_autenticidade}" target="_blank" class="text-secondary font-bold hover:underline">Imprimir / PDF</a>
              </div>
            </td>
          </tr>
        `;
      }).join('');
    } catch (e) {
      tbody.innerHTML = `<tr><td colspan="7" class="text-center py-12 text-red-500">Erro ao buscar certificados: ${e.message}</td></tr>`;
    }
  }

  function abrirModalNovoCertManual() {
    document.getElementById('cert-cliente-nome').value = '';
    document.getElementById('cert-curso-nome').value = '';
    document.getElementById('cert-carga').value = '';
    document.getElementById('cert-data').value = '';
    document.getElementById('cert-tipo').value = 'participacao';
    document.getElementById('cert-instrutor').value = '';
    document.getElementById('cert-assinatura').value = '';
    document.getElementById('cert-erro').classList.add('hidden');

    const modal = document.getElementById('modal-cert-manual');
    modal.classList.remove('hidden');
    setTimeout(() => {
      modal.querySelector('.bg-white').classList.remove('scale-95', 'opacity-0');
    }, 50);
  }

  function fecharModalCertManual() {
    const modal = document.getElementById('modal-cert-manual');
    const box = modal.querySelector('.bg-white');
    box.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
      modal.classList.add('hidden');
    }, 200);
  }

  function abrirModalImportarLote() {
    document.getElementById('lote-csv').value = '';
    document.getElementById('lote-erro').classList.add('hidden');
    document.getElementById('lote-sucesso').classList.add('hidden');

    const modal = document.getElementById('modal-cert-lote');
    modal.classList.remove('hidden');
    setTimeout(() => {
      modal.querySelector('.bg-white').classList.remove('scale-95', 'opacity-0');
    }, 50);
  }

  function fecharModalCertLote() {
    const modal = document.getElementById('modal-cert-lote');
    const box = modal.querySelector('.bg-white');
    box.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
      modal.classList.add('hidden');
      carregarCertificadosAdmin();
    }, 200);
  }

  // Submit Individual
  document.getElementById('form-cert-manual').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btn-salvar-cert');
    const err = document.getElementById('cert-erro');

    btn.disabled = true;
    btn.textContent = 'Gravando...';
    err.classList.add('hidden');

    const payload = {
      cliente_nome:   document.getElementById('cert-cliente-nome').value.trim(),
      curso_nome:     document.getElementById('cert-curso-nome').value.trim(),
      carga_horaria:  parseInt(document.getElementById('cert-carga').value) || 0,
      data_conclusao: document.getElementById('cert-data').value,
      tipo_texto:     document.getElementById('cert-tipo').value,
      instrutor_nome: document.getElementById('cert-instrutor').value.trim(),
      assinatura_url: document.getElementById('cert-assinatura').value.trim()
    };

    try {
      await apiPost(BASE + '/api/admin/certificados', payload);
      fecharModalCertManual();
      carregarCertificadosAdmin();
    } catch (ex) {
      err.textContent = ex.message;
      err.classList.remove('hidden');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Emitir Certificado';
    }
  });

  // Bulk CSV Processor
  async function processarLoteCSV() {
    const txtArea = document.getElementById('lote-csv');
    const btn = document.getElementById('btn-processar-lote');
    const err = document.getElementById('lote-erro');
    const suc = document.getElementById('lote-sucesso');

    err.classList.add('hidden');
    suc.classList.add('hidden');

    const csvText = txtArea.value.trim();
    if (!csvText) {
      err.textContent = 'Cole pelo menos uma linha de dados CSV.';
      err.classList.remove('hidden');
      return;
    }

    btn.disabled = true;
    btn.textContent = 'Processando lote...';

    const lines = csvText.split('\n');
    let sucessos = 0;
    let falhas = 0;
    let logErros = [];

    for (let i = 0; i < lines.length; i++) {
      const line = lines[i].trim();
      if (!line) continue;

      const cols = line.split(',').map(c => c.trim());
      if (cols.length < 6) {
        falhas++;
        logErros.push(`Linha ${i + 1}: Dados insuficientes (deve conter 6 colunas).`);
        continue;
      }

      const payload = {
        cliente_nome:   cols[0],
        curso_nome:     cols[1],
        carga_horaria:  parseInt(cols[2]) || 0,
        data_conclusao: cols[3],
        tipo_texto:     cols[4],
        instrutor_nome: cols[5],
        assinatura_url: ''
      };

      try {
        await apiPost(BASE + '/api/admin/certificados', payload);
        sucessos++;
      } catch (ex) {
        falhas++;
        logErros.push(`Linha ${i + 1} (${cols[0]}): ${ex.message}`);
      }
    }

    btn.disabled = false;
    btn.textContent = 'Processar e Gravar';

    if (sucessos > 0) {
      suc.innerHTML = `Lote processado com sucesso! <strong>${sucessos} certificado(s) emitido(s)</strong>.`;
      suc.classList.remove('hidden');
      txtArea.value = ''; // Limpa se teve sucesso total
    }
    if (falhas > 0) {
      err.innerHTML = `<strong>${falhas} linha(s) falharam:</strong><br>${logErros.join('<br>')}`;
      err.classList.remove('hidden');
    }
  }
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
