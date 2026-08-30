<?php $pageTitle = 'Banco de Questões — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
  <div>
    <h1 class="text-2xl font-bold text-slate-800">Banco de Questões</h1>
    <p class="text-xs text-slate-400 mt-1">Gerencie as perguntas e alternativas dos questionários de fixação e exames oficiais.</p>
  </div>
  <div class="flex gap-2">
    <button onclick="abrirModalImportarCsv()" class="bg-white border border-slate-200 text-slate-600 text-xs font-semibold uppercase tracking-wider px-5 py-3 rounded-lg hover:bg-slate-50 transition-all shadow-sm">
      Importar CSV
    </button>
    <button onclick="abrirModalNovaPergunta()" class="bg-secondary text-white text-xs font-semibold uppercase tracking-wider px-5 py-3 rounded-lg hover:bg-emerald-600 transition-all shadow-sm">
      + Nova Pergunta
    </button>
  </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-xl border border-slate-200 p-5 mb-6 shadow-sm">
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
      <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Filtrar por Curso</label>
      <select id="filtro-curso" onchange="carregarFiltroModulos(); carregarPerguntasAdmin();" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary/20">
        <option value="">Todos os cursos</option>
      </select>
    </div>
    <div>
      <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Filtrar por Módulo</label>
      <select id="filtro-modulo" onchange="carregarFiltroAulas(); carregarPerguntasAdmin();" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary/20">
        <option value="">Todos os módulos</option>
      </select>
    </div>
    <div>
      <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Filtrar por Aula/Exame</label>
      <select id="filtro-aula" onchange="carregarPerguntasAdmin();" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary/20">
        <option value="">Todas as aulas</option>
      </select>
    </div>
  </div>
</div>

<!-- Tabela de Questões -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
  <table class="w-full text-left border-collapse text-sm">
    <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
      <tr>
        <th class="px-6 py-4">Pergunta</th>
        <th class="px-6 py-4">Treinamento / Módulo / Aula</th>
        <th class="px-6 py-4">Qtd Alternativas</th>
        <th class="px-6 py-4">Ações</th>
      </tr>
    </thead>
    <tbody id="perguntas-tbody" class="divide-y divide-slate-100">
      <tr>
        <td colspan="4" class="text-center py-12 text-slate-400">Carregando perguntas...</td>
      </tr>
    </tbody>
  </table>
</div>

<!-- Modal Criar / Editar Pergunta -->
<div id="modal-pergunta" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0 animate-modalEntrance">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
      <h2 id="pergunta-modal-titulo" class="text-lg font-bold text-slate-800">Nova Pergunta</h2>
      <button onclick="fecharModalPergunta()" class="text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <form id="form-pergunta" class="space-y-5">
      <input type="hidden" id="pergunta-id">
      
      <!-- Hierarquia de vinculação -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Treinamento *</label>
          <select id="cad-curso" required onchange="carregarCadModulos();" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 bg-slate-50">
            <option value="">Selecione...</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Módulo *</label>
          <select id="cad-modulo" required onchange="carregarCadAulas();" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 bg-slate-50">
            <option value="">Selecione...</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Aula / Exame *</label>
          <select id="cad-aula" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 bg-slate-50">
            <option value="">Selecione...</option>
          </select>
        </div>
      </div>

      <!-- Enunciado da pergunta -->
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Enunciado da Questão *</label>
        <textarea id="pergunta-texto" required rows="3" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Digite a pergunta..."></textarea>
      </div>

      <!-- Imagem de apoio -->
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Imagem de Apoio (opcional)</label>
        <input type="url" id="pergunta-imagem" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="https://...">
      </div>

      <!-- Justificativa -->
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Justificativa Pedagógica *</label>
        <textarea id="pergunta-justificativa" required rows="2" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Explicação sobre a alternativa correta (exibida ao aluno no quizz, após finalizar)"></textarea>
      </div>

      <!-- Alternativas -->
      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="block text-xs font-bold text-slate-500 uppercase">Alternativas de Resposta (marque 1 ou mais corretas, máx. 5 alternativas)</label>
          <button type="button" id="btn-add-alternativa" onclick="adicionarAlternativaCampo()" class="text-xs text-secondary font-bold hover:underline">+ Adicionar Alternativa</button>
        </div>

        <div id="alternativas-container" class="space-y-3">
          <!-- Geradas dinamicamente por JS -->
        </div>
      </div>

      <!-- Painel de Erros -->
      <div id="pergunta-erro" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3"></div>

      <!-- Botões -->
      <div class="flex gap-3 pt-3 border-t border-slate-100">
        <button type="submit" id="btn-salvar-pergunta" class="flex-1 bg-primary text-white text-xs font-bold uppercase tracking-wider py-3 rounded-lg hover:bg-slate-800 transition-colors shadow-sm">
          Salvar Pergunta
        </button>
        <button type="button" onclick="fecharModalPergunta()" class="px-5 py-3 border border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-slate-50 transition-colors">
          Cancelar
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Importar CSV -->
<div id="modal-csv" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
      <h2 class="text-lg font-bold text-slate-800">Importar Perguntas (CSV)</h2>
      <button onclick="document.getElementById('modal-csv').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Treinamento *</label>
        <select id="csv-curso" onchange="carregarCsvModulos()" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-slate-50"><option value="">Selecione...</option></select>
      </div>
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Módulo *</label>
        <select id="csv-modulo" onchange="carregarCsvAulas()" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-slate-50"><option value="">Selecione...</option></select>
      </div>
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Aula / Exame *</label>
        <select id="csv-aula" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-slate-50"><option value="">Selecione...</option></select>
      </div>
    </div>

    <p class="text-xs text-slate-500 mb-2">Cole abaixo, uma pergunta por linha, separado por <code class="bg-slate-100 px-1 rounded">;</code> — exportado do Excel/Sheets como CSV. Formato de cada linha:</p>
    <p class="text-[11px] font-mono bg-slate-50 border border-slate-200 rounded-lg p-2 mb-3 overflow-x-auto whitespace-nowrap">[Nro];pergunta;justificativa;alt1;correta1(0/1);alt2;correta2;alt3;correta3;alt4;correta4;alt5;correta5</p>
    <p class="text-[10px] text-slate-400 -mt-2 mb-3">"Nro" é opcional — só para você numerar as perguntas na sua planilha e rastrear mais fácil; não é salvo no sistema.</p>

    <textarea id="csv-texto" rows="8" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="O que é um projeto?;Projeto é um esforço temporário;Um trabalho contínuo e repetitivo;0;Um esforço temporário com início e fim definidos;1;Um departamento da empresa;0"></textarea>

    <div id="csv-resultado" class="hidden mt-3 text-xs rounded-lg px-3 py-2"></div>
    <div id="csv-erro" class="hidden mt-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-4 py-3"></div>

    <div class="flex gap-3 pt-4 mt-2 border-t border-slate-100">
      <button type="button" id="btn-importar-csv" onclick="importarPerguntasCsv()" class="flex-1 bg-primary text-white text-xs font-bold uppercase tracking-wider py-3 rounded-lg hover:bg-slate-800">Importar</button>
      <button type="button" onclick="document.getElementById('modal-csv').classList.add('hidden')" class="px-5 py-3 border border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-slate-50">Cancelar</button>
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
  let _cursosCache = [];
  let _perguntasList = [];

  function esc(s) {
    if (!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
  }

  document.addEventListener('DOMContentLoaded', async () => {
    await carregarCursosFiltros();
    await carregarPerguntasAdmin();
  });

  async function carregarCursosFiltros() {
    try {
      _cursosCache = await apiFetch(BASE + '/api/cursos');
      
      const filtroCurso = document.getElementById('filtro-curso');
      const cadCurso = document.getElementById('cad-curso');

      _cursosCache.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = c.titulo;
        filtroCurso.appendChild(opt.cloneNode(true));
        cadCurso.appendChild(opt);
      });
    } catch (e) {
      console.error('Erro ao carregar cursos:', e);
    }
  }

  function carregarFiltroModulos() {
    const cursoId = document.getElementById('filtro-curso').value;
    const selectMod = document.getElementById('filtro-modulo');
    selectMod.innerHTML = '<option value="">Todos os módulos</option>';
    document.getElementById('filtro-aula').innerHTML = '<option value="">Todas as aulas</option>';

    if (!cursoId) return;

    const curso = _cursosCache.find(c => c.id == cursoId);
    if (curso && curso.modulos) {
      curso.modulos.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m.id;
        opt.textContent = m.titulo;
        selectMod.appendChild(opt);
      });
    }
  }

  function carregarFiltroAulas() {
    const cursoId = document.getElementById('filtro-curso').value;
    const moduloId = document.getElementById('filtro-modulo').value;
    const selectAula = document.getElementById('filtro-aula');
    selectAula.innerHTML = '<option value="">Todas as aulas</option>';

    if (!cursoId || !moduloId) return;

    const curso = _cursosCache.find(c => c.id == cursoId);
    if (curso && curso.modulos) {
      const modulo = curso.modulos.find(m => m.id == moduloId);
      if (modulo && modulo.aulas) {
        modulo.aulas.forEach(a => {
          const opt = document.createElement('option');
          opt.value = a.id;
          opt.textContent = (a.e_prova ? '[EXAME] ' : '') + a.titulo;
          selectAula.appendChild(opt);
        });
      }
    }
  }

  // Modals de cadastro
  function carregarCadModulos(preselectModuloId = null) {
    const cursoId = document.getElementById('cad-curso').value;
    const selectMod = document.getElementById('cad-modulo');
    selectMod.innerHTML = '<option value="">Selecione...</option>';
    document.getElementById('cad-aula').innerHTML = '<option value="">Selecione...</option>';

    if (!cursoId) return;

    const curso = _cursosCache.find(c => c.id == cursoId);
    if (curso && curso.modulos) {
      curso.modulos.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m.id;
        opt.textContent = m.titulo;
        if (preselectModuloId && m.id == preselectModuloId) opt.selected = true;
        selectMod.appendChild(opt);
      });
    }
  }

  function carregarCadAulas(preselectAulaId = null) {
    const cursoId = document.getElementById('cad-curso').value;
    const moduloId = document.getElementById('cad-modulo').value;
    const selectAula = document.getElementById('cad-aula');
    selectAula.innerHTML = '<option value="">Selecione...</option>';

    if (!cursoId || !moduloId) return;

    const curso = _cursosCache.find(c => c.id == cursoId);
    if (curso && curso.modulos) {
      const modulo = curso.modulos.find(m => m.id == moduloId);
      if (modulo && modulo.aulas) {
        modulo.aulas.forEach(a => {
          const opt = document.createElement('option');
          opt.value = a.id;
          opt.textContent = (a.e_prova ? '[EXAME] ' : '') + a.titulo;
          if (preselectAulaId && a.id == preselectAulaId) opt.selected = true;
          selectAula.appendChild(opt);
        });
      }
    }
  }

  async function carregarPerguntasAdmin() {
    const tbody = document.getElementById('perguntas-tbody');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-12 text-slate-400">Carregando perguntas...</td></tr>';

    const cursoId = document.getElementById('filtro-curso').value;
    const moduloId = document.getElementById('filtro-modulo').value;
    const aulaId = document.getElementById('filtro-aula').value;

    let url = BASE + '/api/admin/perguntas';
    const params = [];
    if (cursoId) params.push('curso_id=' + cursoId);
    if (moduloId) params.push('modulo_id=' + moduloId);
    if (aulaId) params.push('aula_id=' + aulaId);
    if (params.length) url += '?' + params.join('&');

    try {
      _perguntasList = await apiFetch(url);
      if (!_perguntasList.length) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-12 text-slate-400">Nenhuma pergunta localizada para os filtros selecionados.</td></tr>';
        return;
      }

      tbody.innerHTML = _perguntasList.map(p => `
        <tr class="hover:bg-slate-50/50 transition-colors">
          <td class="px-6 py-4">
            <p class="font-semibold text-slate-800 text-xs sm:text-sm leading-relaxed">${esc(p.texto)}</p>
            ${p.justificativa ? `<p class="text-[11px] text-slate-400 mt-1 italic max-w-lg truncate">Explicação: ${esc(p.justificativa)}</p>` : ''}
          </td>
          <td class="px-6 py-4 text-xs">
            <span class="font-bold text-slate-700 block">${esc(p.curso_titulo || '—')}</span>
            <span class="text-slate-400 block">${esc(p.modulo_titulo || '—')}</span>
            <span class="text-slate-500 font-semibold block mt-0.5">${p.e_prova ? '🎯 [PROVA]' : '📖 [AULA]'} ${esc(p.aula_titulo || '—')}</span>
          </td>
          <td class="px-6 py-4 text-xs font-semibold text-slate-500">
            ${p.opcoes?.length || 0} alternativas
          </td>
          <td class="px-6 py-4 text-xs">
            <div class="flex items-center gap-3">
              <button onclick="editarPerguntaAdmin(${p.id})" class="text-primary font-bold hover:underline">Editar</button>
              <button onclick="excluirPerguntaAdmin(${p.id})" class="text-red-500 font-bold hover:underline">Excluir</button>
            </div>
          </td>
        </tr>
      `).join('');
    } catch (e) {
      tbody.innerHTML = `<tr><td colspan="4" class="text-center py-12 text-red-500">Falha ao buscar perguntas: ${e.message}</td></tr>`;
    }
  }

  // Alternativas (máx. 5, marca 1 ou mais como corretas com checkbox)
  const MAX_ALTERNATIVAS = 5;

  function atualizarBotaoAddAlternativa() {
    const container = document.getElementById('alternativas-container');
    const btn = document.getElementById('btn-add-alternativa');
    if (!container || !btn) return;
    btn.classList.toggle('hidden', container.children.length >= MAX_ALTERNATIVAS);
  }

  function adicionarAlternativaCampo(texto = '', correta = false) {
    const container = document.getElementById('alternativas-container');
    if (container.children.length >= MAX_ALTERNATIVAS) return;

    const div = document.createElement('div');
    div.className = "flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-lg p-2.5 animate-fadeIn";
    div.innerHTML = `
      <input type="checkbox" ${correta ? 'checked' : ''} class="w-4 h-4 accent-secondary flex-shrink-0 rounded" title="Marcar como correta">
      <input type="text" value="${esc(texto)}" required class="flex-1 bg-white border border-slate-200 rounded-md px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-primary" placeholder="Alternativa de resposta...">
      <button type="button" onclick="this.parentElement.remove(); atualizarBotaoAddAlternativa();" class="text-slate-400 hover:text-red-500 transition-colors flex-shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
      </button>
    `;
    container.appendChild(div);
    atualizarBotaoAddAlternativa();
  }

  function abrirModalNovaPergunta() {
    document.getElementById('pergunta-modal-titulo').textContent = 'Nova Pergunta';
    document.getElementById('pergunta-id').value = '';
    document.getElementById('pergunta-texto').value = '';
    document.getElementById('pergunta-imagem').value = '';
    document.getElementById('pergunta-justificativa').value = '';
    document.getElementById('cad-curso').value = '';
    document.getElementById('cad-modulo').innerHTML = '<option value="">Selecione...</option>';
    document.getElementById('cad-aula').innerHTML = '<option value="">Selecione...</option>';
    document.getElementById('alternativas-container').innerHTML = '';
    document.getElementById('pergunta-erro').classList.add('hidden');

    // Cria 3 alternativas padrão limpas
    adicionarAlternativaCampo('', false);
    adicionarAlternativaCampo('', false);
    adicionarAlternativaCampo('', false);
    atualizarBotaoAddAlternativa();

    const modal = document.getElementById('modal-pergunta');
    modal.classList.remove('hidden');
    // Delay para a animação CSS entrar
    setTimeout(() => {
      modal.querySelector('.bg-white').classList.remove('scale-95', 'opacity-0');
    }, 50);
  }

  function fecharModalPergunta() {
    const modal = document.getElementById('modal-pergunta');
    const box = modal.querySelector('.bg-white');
    box.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
      modal.classList.add('hidden');
    }, 200);
  }

  function editarPerguntaAdmin(id) {
    const p = _perguntasList.find(item => item.id == id);
    if (!p) return;

    document.getElementById('pergunta-modal-titulo').textContent = 'Editar Pergunta';
    document.getElementById('pergunta-id').value = p.id;
    document.getElementById('pergunta-texto').value = p.texto;
    document.getElementById('pergunta-imagem').value = p.imagem_url || '';
    document.getElementById('pergunta-justificativa').value = p.justificativa || '';

    document.getElementById('cad-curso').value = p.curso_id || '';
    carregarCadModulos(p.modulo_id);
    carregarCadAulas(p.aula_id);

    const container = document.getElementById('alternativas-container');
    container.innerHTML = '';
    
    if (p.opcoes && p.opcoes.length) {
      p.opcoes.forEach(o => {
        adicionarAlternativaCampo(o.texto, o.correta == 1);
      });
    }

    document.getElementById('pergunta-erro').classList.add('hidden');

    const modal = document.getElementById('modal-pergunta');
    modal.classList.remove('hidden');
    setTimeout(() => {
      modal.querySelector('.bg-white').classList.remove('scale-95', 'opacity-0');
    }, 50);
  }

  async function excluirPerguntaAdmin(id) {
    if (!confirm('Deseja excluir permanentemente esta pergunta e suas alternativas?')) return;
    try {
      await apiDelete(BASE + '/api/admin/perguntas/' + id);
      carregarPerguntasAdmin();
    } catch (e) {
      alert('Erro ao excluir: ' + e.message);
    }
  }

  // Importação em massa via CSV colado
  function abrirModalImportarCsv() {
    const selCurso = document.getElementById('csv-curso');
    selCurso.innerHTML = '<option value="">Selecione...</option>' +
      _cursosCache.map(c => `<option value="${c.id}">${esc(c.titulo)}</option>`).join('');
    document.getElementById('csv-modulo').innerHTML = '<option value="">Selecione...</option>';
    document.getElementById('csv-aula').innerHTML = '<option value="">Selecione...</option>';
    document.getElementById('csv-texto').value = '';
    document.getElementById('csv-erro').classList.add('hidden');
    document.getElementById('csv-resultado').classList.add('hidden');
    document.getElementById('modal-csv').classList.remove('hidden');
  }

  function carregarCsvModulos() {
    const cursoId = document.getElementById('csv-curso').value;
    const selectMod = document.getElementById('csv-modulo');
    selectMod.innerHTML = '<option value="">Selecione...</option>';
    document.getElementById('csv-aula').innerHTML = '<option value="">Selecione...</option>';
    const curso = _cursosCache.find(c => c.id == cursoId);
    (curso?.modulos || []).forEach(m => selectMod.innerHTML += `<option value="${m.id}">${esc(m.titulo)}</option>`);
  }

  function carregarCsvAulas() {
    const cursoId = document.getElementById('csv-curso').value;
    const moduloId = document.getElementById('csv-modulo').value;
    const selectAula = document.getElementById('csv-aula');
    selectAula.innerHTML = '<option value="">Selecione...</option>';
    const curso = _cursosCache.find(c => c.id == cursoId);
    const modulo = curso?.modulos?.find(m => m.id == moduloId);
    (modulo?.aulas || []).forEach(a => selectAula.innerHTML += `<option value="${a.id}">${(a.e_prova ? '[EXAME] ' : '') + esc(a.titulo)}</option>`);
  }

  function parseLinhaCsv(linha) {
    let campos = linha.split(';').map(c => c.trim());
    // Coluna opcional "Nro da Pergunta" no início (só numeração de apoio do
    // Admin durante a preparação da planilha — não é gravada em lugar nenhum).
    if (campos.length > 1 && /^\d+$/.test(campos[0])) {
      campos = campos.slice(1);
    }
    if (campos.length < 6) return null; // pergunta + justificativa + pelo menos 2 alternativas (4 campos)

    const [texto, justificativa, ...resto] = campos;
    if (!texto) return null;

    const opcoes = [];
    for (let i = 0; i < resto.length - 1; i += 2) {
      const altTexto = resto[i];
      const altCorreta = resto[i + 1];
      if (altTexto) opcoes.push({ texto: altTexto, correta: altCorreta === '1' });
    }
    if (opcoes.length < 2 || opcoes.length > 5) return null;
    if (!opcoes.some(o => o.correta)) return null;

    return { texto, justificativa: justificativa || '', opcoes };
  }

  async function importarPerguntasCsv() {
    const err = document.getElementById('csv-erro');
    const resultado = document.getElementById('csv-resultado');
    err.classList.add('hidden');
    resultado.classList.add('hidden');

    const cursoId  = document.getElementById('csv-curso').value;
    const moduloId = document.getElementById('csv-modulo').value;
    const aulaId   = document.getElementById('csv-aula').value;
    if (!cursoId || !moduloId || !aulaId) {
      err.textContent = 'Selecione o treinamento, módulo e aula/exame de destino.';
      err.classList.remove('hidden');
      return;
    }

    const linhas = document.getElementById('csv-texto').value.split('\n').map(l => l.trim()).filter(Boolean);
    if (!linhas.length) {
      err.textContent = 'Cole ao menos uma linha no formato indicado.';
      err.classList.remove('hidden');
      return;
    }

    const btn = document.getElementById('btn-importar-csv');
    btn.disabled = true; btn.textContent = 'Importando...';

    let sucesso = 0;
    const falhas = [];

    for (let i = 0; i < linhas.length; i++) {
      const parsed = parseLinhaCsv(linhas[i]);
      if (!parsed) { falhas.push(`Linha ${i + 1}: formato inválido.`); continue; }
      try {
        await apiPost(BASE + '/api/admin/perguntas', {
          texto: parsed.texto,
          justificativa: parsed.justificativa,
          curso_id: cursoId, modulo_id: moduloId, aula_id: aulaId,
          opcoes: parsed.opcoes
        });
        sucesso++;
      } catch (ex) {
        falhas.push(`Linha ${i + 1}: ${ex.message}`);
      }
    }

    btn.disabled = false; btn.textContent = 'Importar';

    resultado.className = 'mt-3 text-xs rounded-lg px-3 py-2 ' + (falhas.length ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-green-50 text-green-800 border border-green-200');
    resultado.innerHTML = `${sucesso} pergunta(s) importada(s) com sucesso.` + (falhas.length ? `<br>${falhas.length} com problema:<br>` + falhas.map(esc).join('<br>') : '');
    resultado.classList.remove('hidden');

    if (sucesso) carregarPerguntasAdmin();
  }

  // Submit
  document.getElementById('form-pergunta').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btn-salvar-pergunta');
    const err = document.getElementById('pergunta-erro');

    btn.disabled = true;
    btn.textContent = 'Gravando...';
    err.classList.add('hidden');

    // Validação de alternativas
    const altDivs = document.getElementById('alternativas-container').children;
    const opcoes = [];
    let temCorreta = false;

    for (let i = 0; i < altDivs.length; i++) {
      const chk = altDivs[i].querySelector('input[type="checkbox"]');
      const text = altDivs[i].querySelector('input[type="text"]').value.trim();
      const correta = chk.checked;

      opcoes.push({ texto: text, correta: correta });
      if (correta) temCorreta = true;
    }

    if (opcoes.length < 2) {
      err.textContent = 'Insira pelo menos duas alternativas de resposta.';
      err.classList.remove('hidden');
      btn.disabled = false;
      btn.textContent = 'Salvar Pergunta';
      return;
    }

    if (opcoes.length > 5) {
      err.textContent = 'No máximo 5 alternativas por pergunta.';
      err.classList.remove('hidden');
      btn.disabled = false;
      btn.textContent = 'Salvar Pergunta';
      return;
    }

    if (!temCorreta) {
      err.textContent = 'Marque pelo menos uma alternativa como correta.';
      err.classList.remove('hidden');
      btn.disabled = false;
      btn.textContent = 'Salvar Pergunta';
      return;
    }

    if (!document.getElementById('pergunta-justificativa').value.trim()) {
      err.textContent = 'A justificativa pedagógica é obrigatória.';
      err.classList.remove('hidden');
      btn.disabled = false;
      btn.textContent = 'Salvar Pergunta';
      return;
    }

    const payload = {
      texto:         document.getElementById('pergunta-texto').value.trim(),
      imagem_url:    document.getElementById('pergunta-imagem').value.trim() || null,
      justificativa: document.getElementById('pergunta-justificativa').value.trim(),
      curso_id:      document.getElementById('cad-curso').value || null,
      modulo_id:     document.getElementById('cad-modulo').value || null,
      aula_id:       document.getElementById('cad-aula').value || null,
      opcoes:        opcoes
    };

    const id = document.getElementById('pergunta-id').value;

    try {
      if (id) {
        await apiPut(BASE + '/api/admin/perguntas/' + id, payload);
      } else {
        await apiPost(BASE + '/api/admin/perguntas', payload);
      }
      fecharModalPergunta();
      carregarPerguntasAdmin();
    } catch (ex) {
      err.textContent = ex.message;
      err.classList.remove('hidden');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Salvar Pergunta';
    }
  });
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
