// Player de aulas (substitui painel/curso/[id].vue)
var _B = _B || (() => (typeof BASE !== 'undefined' ? BASE : ''));

let playerData   = null;
let aulaAtual    = null;
let progressoMap = {};
let filtroAulas  = '';

function esc(s) {
  if (!s) return '';
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

async function carregarPlayer(cursoId) {
  try {
    playerData = await apiFetch(_B() + '/api/aluno/curso/' + cursoId);

    (playerData.progresso || []).forEach(p => { progressoMap[p.aula_id] = p; });

    document.getElementById('player-loading').classList.add('hidden');
    document.getElementById('player-content').classList.remove('hidden');
    
    // Nome do curso na barra lateral
    const sidebarTitle = document.getElementById('sidebar-curso-titulo');
    if (sidebarTitle) sidebarTitle.textContent = playerData.curso.titulo;

    // Dados do Instrutor no Card (Mockup)
    if (playerData.curso.instrutor_nome) {
      const nomeEl = document.getElementById('instrutor-nome');
      if (nomeEl) nomeEl.textContent = playerData.curso.instrutor_nome;
      const cargoEl = document.getElementById('instrutor-cargo');
      if (cargoEl) cargoEl.textContent = playerData.curso.instrutor_qualificacao || 'Professor(a)';
      const avatarEl = document.getElementById('instrutor-avatar');
      if (avatarEl) {
        avatarEl.src = playerData.curso.instrutor_avatar || 'https://ui-avatars.com/api/?background=0C1323&color=fff&name=' + encodeURIComponent(playerData.curso.instrutor_nome);
      }
      const cardEl = document.getElementById('instrutor-card');
      if (cardEl) cardEl.classList.remove('hidden');
    }

    renderSidebar();

    const todasAulas = (playerData.curso.modulos || []).flatMap(m => m.aulas || []);
    const proxima    = todasAulas.find(a => !progressoMap[a.id]?.concluida) || todasAulas[0];
    if (proxima) abrirAula(proxima);

    atualizarProgressoLabel();
  } catch (e) {
    document.getElementById('player-loading').innerHTML = `<p class="text-red-400">Erro ao carregar: ${e.message}</p>`;
  }
}

function renderSidebar() {
  const container = document.getElementById('sidebar-modulos');
  if (!container || !playerData) return;

  const termo = filtroAulas.trim().toLowerCase();
  const html = (playerData.curso.modulos || []).map((mod) => {
    const aulasReais = (mod.aulas || []).filter(a => !termo || `${mod.titulo} ${a.titulo} ${a.descricao || ''}`.toLowerCase().includes(termo));
    if (!aulasReais.length) return '';

    // Insere dinamicamente um quiz virtual após cada aula real que não seja do tipo quiz
    const aulasComQuiz = [];
    aulasReais.forEach(a => {
      aulasComQuiz.push(a);
      if (a.tipo !== 'quiz') {
        aulasComQuiz.push({
          id: `quiz_din_${a.id}`,
          titulo: `Quiz de Fixação — ${a.titulo}`,
          tipo: 'quiz',
          e_prova: 0,
          parent_aula_id: a.id,
          is_dinamico: true
        });
      }
    });

    const total = mod.aulas?.length || 0;
    const concluidas = (mod.aulas || []).filter(a => progressoMap[a.id]?.concluida).length;
    const pct = total ? Math.round((concluidas / total) * 100) : 0;

    return `
      <section class="rounded-xl border border-gray-100 bg-white overflow-hidden shadow-sm">
        <!-- Cabeçalho do Módulo (Mockup: título e barra progressiva) -->
        <div class="border-b border-gray-100 bg-gray-50/50 px-3.5 py-3">
          <div>
            <h3 class="text-xs font-bold text-gray-700 leading-snug">${esc(mod.titulo)}</h3>
            <div class="flex items-center gap-2 mt-1.5">
              <span class="text-[10px] font-semibold text-gray-400 shrink-0">${concluidas}/${total}</span>
              <div class="w-full bg-gray-200 h-1 rounded-full overflow-hidden">
                <div class="bg-secondary h-1 rounded-full transition-all" style="width: ${pct}%"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="divide-y divide-gray-50">
          ${aulasComQuiz.map((a, ai) => lessonButtonHtml(a, ai)).join('')}
        </div>
      </section>
    `;
  }).join('');

  container.innerHTML = html || '<p class="rounded-xl bg-white p-4 text-center text-xs text-gray-400">Nenhuma aula encontrada.</p>';
}

function lessonButtonHtml(aula, index) {
  const concluida = aula.is_dinamico
    ? localStorage.getItem('quiz_completed_' + aula.id) === 'true'
    : progressoMap[aula.id]?.concluida;
  const ativa = aulaAtual?.id === aula.id;
  const tipoLabel = aula.tipo === 'quiz' ? 'Quiz' : 'Aula';
  const onClickParam = typeof aula.id === 'string' ? `'${aula.id}'` : aula.id;

  return `
    <button onclick="abrirAulaId(${onClickParam})"
      class="w-full text-left transition-colors px-3.5 py-3 flex items-center justify-between gap-3 ${ativa ? 'bg-gray-100' : 'hover:bg-gray-50'} focus:outline-none">
      <div class="flex items-center gap-3 min-w-0">
        <!-- Índice -->
        <span class="text-[10px] font-bold text-gray-400 shrink-0 w-4">${aula.is_dinamico ? '...' : String(index + 1).padStart(2, '0')}</span>
        
        <!-- Thumbnail / Ícone -->
        ${aula.tipo === 'quiz'
          ? `
            <div class="h-8 w-8 shrink-0 flex items-center justify-center rounded-lg bg-gray-100 text-gray-400">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M9 8h2m-6 12h14a2 2 0 002-2V6a2 2 0 00-2-2H7L5 6v12a2 2 0 002 2z"/></svg>
            </div>
            `
          : `
            <div class="h-8 w-12 shrink-0 rounded overflow-hidden bg-gray-800 relative shadow-sm">
              <div class="absolute inset-0 bg-black/30 flex items-center justify-center text-white">
                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
              </div>
            </div>
            `
        }
        
        <!-- Título e Subtítulo -->
        <div class="min-w-0">
          <p class="text-[11px] font-bold text-gray-800 truncate leading-snug">${esc(aula.titulo)}</p>
          <p class="text-[9px] text-gray-400 mt-0.5 uppercase tracking-wide font-semibold">${tipoLabel}</p>
        </div>
      </div>
      
      <!-- Status Ícone -->
      <div class="shrink-0">
        ${concluida
          ? `<svg class="h-4 w-4 text-secondary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>`
          : `<svg class="h-3.5 w-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>`
        }
      </div>
    </button>
  `;
}

function filtrarAulas(valor) {
  filtroAulas = valor || '';
  renderSidebar();
}

function abrirAulaId(aulaId) {
  if (typeof aulaId === 'string' && aulaId.startsWith('quiz_din_')) {
    const parentId = parseInt(aulaId.replace('quiz_din_', ''));
    const todasAulas = (playerData.curso.modulos || []).flatMap(m => m.aulas || []);
    const parentAula = todasAulas.find(a => a.id === parentId);
    if (parentAula) {
      const quizAula = {
        id: aulaId,
        titulo: `Quiz de Fixação — ${parentAula.titulo}`,
        tipo: 'quiz',
        e_prova: 0,
        parent_aula_id: parentId,
        is_dinamico: true
      };
      abrirAula(quizAula);
    }
    return;
  }

  const todasAulas = (playerData.curso.modulos || []).flatMap(m => m.aulas || []);
  const aula = todasAulas.find(a => a.id === aulaId);
  if (aula) abrirAula(aula);
}

function abrirAula(aula) {
  if (typeof pararProctoring === 'function') {
    pararProctoring();
  }

  aulaAtual = aula;
  document.getElementById('aula-titulo').textContent = aula.titulo;
  document.getElementById('aula-descricao').textContent = aula.descricao || 'Sem descrição cadastrada para esta aula.';

  // Nome do módulo correspondente no topo
  const modulo = (playerData?.curso?.modulos || []).find(m => (m.aulas || []).some(a => a.id === (aula.is_dinamico ? aula.parent_aula_id : aula.id))) || {};
  document.getElementById('player-titulo').textContent = modulo.titulo || playerData?.curso?.titulo || '';

  const videoWrapper = document.getElementById('video-wrapper');
  const quizContainer = document.getElementById('quiz-container');
  const btn = document.getElementById('btn-concluir');

  // Fecha gaveta de anotações ao mudar de aula
  const drawer = document.getElementById('anotacao-drawer');
  const caret = document.getElementById('anotacao-caret');
  if (drawer) {
    drawer.classList.add('hidden');
    caret?.classList.remove('rotate-180');
  }

  if (aula.tipo === 'quiz') {
    if (videoWrapper) videoWrapper.classList.add('hidden');
    if (btn) btn.classList.add('hidden');

    if (quizContainer) {
      quizContainer.classList.remove('hidden');
      if (aula.is_dinamico) {
        carregarQuizDinamico(aula.id, playerData.matricula.id, 'quiz-container');
      } else {
        carregarQuiz(aula.id, playerData.matricula.id, 'quiz-container');
      }
    }
  } else {
    if (videoWrapper) videoWrapper.classList.remove('hidden');
    if (btn) {
      btn.classList.remove('hidden');
      const jaConcluida = progressoMap[aula.id]?.concluida;
      btn.textContent = jaConcluida ? 'Aula Concluída' : 'Marcar como concluída';
      btn.disabled = !!jaConcluida;
      // Atualiza classe do botão para indicar conclusão
      if (jaConcluida) {
        btn.className = "shrink-0 rounded-lg bg-gray-200 px-4 py-2.5 text-xs font-bold text-gray-500 shadow-sm cursor-not-allowed";
      } else {
        btn.className = "shrink-0 rounded-lg bg-secondary px-4 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:bg-green-600 disabled:opacity-60";
      }
    }

    if (quizContainer) quizContainer.classList.add('hidden');

    const iframe = document.getElementById('player-iframe');
    const url = aula.video_url || aula.url || '';
    if (iframe) iframe.src = url ? converterUrlEmbed(url) : '';
  }

  renderSidebar();
}

function converterUrlEmbed(url) {
  const ytMatch = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/))([^&?/]+)/);
  if (ytMatch) return `https://www.youtube.com/embed/${ytMatch[1]}?rel=0`;

  const vimeoMatch = url.match(/vimeo\.com\/(\d+)/);
  if (vimeoMatch) return `https://player.vimeo.com/video/${vimeoMatch[1]}`;

  return url;
}

async function marcarConcluida() {
  if (!aulaAtual || !playerData) return;
  const btn = document.getElementById('btn-concluir');
  btn.disabled = true;
  btn.textContent = 'Salvando...';

  try {
    await apiPost(_B() + '/api/aluno/progresso', {
      matricula_id: playerData.matricula.id,
      aula_id: aulaAtual.id,
      concluida: true,
      tempo_parada: 0,
    });

    progressoMap[aulaAtual.id] = { concluida: true };
    btn.textContent = 'Concluida';
    atualizarProgressoLabel();
    renderSidebar();

    const todasAulas = (playerData.curso.modulos || []).flatMap(m => m.aulas || []);
    const idx = todasAulas.findIndex(a => a.id === aulaAtual.id);
    const proxima = todasAulas[idx + 1];
    if (proxima) setTimeout(() => abrirAula(proxima), 800);
  } catch {
    btn.disabled = false;
    btn.textContent = 'Marcar como concluida';
  }
}

function atualizarProgressoLabel() {
  const todasAulas = (playerData?.curso?.modulos || []).flatMap(m => m.aulas || []);
  const concluidas = todasAulas.filter(a => progressoMap[a.id]?.concluida).length;
  const percentual = todasAulas.length ? Math.round((concluidas / todasAulas.length) * 100) : 0;

  const label = document.getElementById('player-progresso-label');
  if (label) label.textContent = `${concluidas} de ${todasAulas.length} aulas`;

  const percentLabel = document.getElementById('player-progresso-percent');
  if (percentLabel) percentLabel.textContent = `${percentual}%`;

  // Atualiza círculo SVG dinamicamente
  const svgCircle = document.getElementById('player-progresso-svg');
  if (svgCircle) {
    svgCircle.setAttribute('stroke-dasharray', `${percentual}, 100`);
  }

  const certCard = document.getElementById('certificado-card');
  const certLock = document.getElementById('certificado-lock');
  if (certCard && certLock && todasAulas.length > 0) {
    if (concluidas === todasAulas.length) {
      certCard.classList.remove('hidden');
      certLock.classList.add('hidden');
      const certBtn = document.getElementById('btn-emitir-certificado');
      if (certBtn) {
        certBtn.href = '#';
        certBtn.onclick = (e) => interceptarDownloadCertificado(e, playerData.curso.id);
      }
    } else {
      certCard.classList.add('hidden');
      certLock.classList.remove('hidden');
    }
  }
}

// Gaveta de Anotações Persistida (Mockup / LocalStorage)
function toggleAnotacoes() {
  const drawer = document.getElementById('anotacao-drawer');
  const caret = document.getElementById('anotacao-caret');
  if (!drawer) return;
  const isHidden = drawer.classList.contains('hidden');
  if (isHidden) {
    drawer.classList.remove('hidden');
    caret?.classList.add('rotate-180');
    if (aulaAtual) {
      document.getElementById('anotacao-texto').value = localStorage.getItem(`anotacao_${aulaAtual.id}`) || '';
    }
  } else {
    drawer.classList.add('hidden');
    caret?.classList.remove('rotate-180');
  }
}

function salvarAnotacao() {
  if (!aulaAtual) return;
  const txt = document.getElementById('anotacao-texto').value;
  localStorage.setItem(`anotacao_${aulaAtual.id}`, txt);
  alert('Anotação salva com sucesso!');
  toggleAnotacoes();
}
