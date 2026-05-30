// Player de aulas (substitui painel/curso/[id].vue)
var _B = _B || (() => (typeof BASE !== 'undefined' ? BASE : ''));

let playerData   = null;
let aulaAtual    = null;
let progressoMap = {};

function esc(s) {
  if (!s) return '';
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

async function carregarPlayer(cursoId) {
  try {
    playerData = await apiFetch(_B() + '/api/aluno/curso/' + cursoId);

    // Monta mapa de progresso: aula_id -> {concluida, tempo_parada}
    (playerData.progresso || []).forEach(p => { progressoMap[p.aula_id] = p; });

    document.getElementById('player-loading').classList.add('hidden');
    document.getElementById('player-content').classList.remove('hidden');
    document.getElementById('player-titulo').textContent = playerData.curso.titulo;

    renderSidebar();

    // Abre a primeira aula não concluída (ou a primeira)
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
  if (!container) return;

  container.innerHTML = (playerData.curso.modulos || []).map((mod, mi) => `
    <div>
      <div class="text-xs font-bold text-gray-500 uppercase tracking-wider px-2 py-2 mt-3 border-b border-gray-100 pb-1 mb-2">${esc(mod.titulo)}</div>
      <div class="space-y-1">
        ${(mod.aulas || []).map(a => {
          const p       = progressoMap[a.id];
          const concl   = p?.concluida;
          const ativo   = aulaAtual?.id === a.id;
          return `
            <button onclick="abrirAulaId(${a.id})"
              class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-xl text-sm transition-colors
                ${ativo ? 'bg-primary text-white shadow-sm font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'}">
              ${concl
                ? `<svg class="w-4 h-4 flex-shrink-0 text-secondary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>`
                : `<svg class="w-4 h-4 flex-shrink-0 text-gray-400 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg>`
              }
              <span class="truncate">${esc(a.titulo)}</span>
            </button>
          `;
        }).join('')}
      </div>
    </div>
  `).join('');
}

function abrirAulaId(aulaId) {
  const todasAulas = (playerData.curso.modulos || []).flatMap(m => m.aulas || []);
  const aula = todasAulas.find(a => a.id === aulaId);
  if (aula) abrirAula(aula);
}

function abrirAula(aula) {
  if (typeof pararProctoring === 'function') {
    pararProctoring();
  }
  aulaAtual = aula;
  document.getElementById('aula-titulo').textContent    = aula.titulo;
  document.getElementById('aula-descricao').textContent = aula.descricao || '';

  const videoWrapper = document.getElementById('video-wrapper');
  const quizContainer = document.getElementById('quiz-container');
  const btn = document.getElementById('btn-concluir');

  if (aula.tipo === 'quiz') {
    // Esconder player de vídeo e botão de concluir
    if (videoWrapper) videoWrapper.classList.add('hidden');
    if (btn) btn.classList.add('hidden');
    
    // Mostrar quiz container e carregar
    if (quizContainer) {
      quizContainer.classList.remove('hidden');
      carregarQuiz(aula.id, playerData.matricula.id, 'quiz-container');
    }
  } else {
    // Mostrar player de vídeo e botão de concluir
    if (videoWrapper) videoWrapper.classList.remove('hidden');
    if (btn) {
      btn.classList.remove('hidden');
      const jaConcluida = progressoMap[aula.id]?.concluida;
      btn.textContent = jaConcluida ? '✓ Concluída' : 'Marcar como concluída';
      btn.disabled    = !!jaConcluida;
    }
    
    // Esconder quiz container
    if (quizContainer) quizContainer.classList.add('hidden');

    const iframe = document.getElementById('player-iframe');
    const url    = aula.video_url || aula.url || '';
    if (iframe) iframe.src = url ? converterUrlEmbed(url) : '';
  }

  renderSidebar();
}

function converterUrlEmbed(url) {
  // YouTube
  const ytMatch = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/))([^&?/]+)/);
  if (ytMatch) return `https://www.youtube.com/embed/${ytMatch[1]}?rel=0`;
  // Vimeo
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
      aula_id:      aulaAtual.id,
      concluida:    true,
      tempo_parada: 0,
    });
    progressoMap[aulaAtual.id] = { concluida: true };
    btn.textContent = '✓ Concluída';
    atualizarProgressoLabel();
    renderSidebar();

    // Avança para próxima aula automaticamente
    const todasAulas = (playerData.curso.modulos || []).flatMap(m => m.aulas || []);
    const idx        = todasAulas.findIndex(a => a.id === aulaAtual.id);
    const proxima    = todasAulas[idx + 1];
    if (proxima) setTimeout(() => abrirAula(proxima), 800);
  } catch {
    btn.disabled = false;
    btn.textContent = 'Marcar como concluída';
  }
}

function atualizarProgressoLabel() {
  const todasAulas = (playerData?.curso?.modulos || []).flatMap(m => m.aulas || []);
  const concluidas = todasAulas.filter(a => progressoMap[a.id]?.concluida).length;
  const label = document.getElementById('player-progresso-label');
  if (label) label.textContent = `${concluidas}/${todasAulas.length} aulas`;

  // Controle de exibição do certificado
  const certCard = document.getElementById('certificado-card');
  const certLock = document.getElementById('certificado-lock');
  if (certCard && certLock && todasAulas.length > 0) {
    if (concluidas === todasAulas.length) {
      certCard.classList.remove('hidden');
      certLock.classList.add('hidden');
      const certBtn = document.getElementById('btn-emitir-certificado');
      if (certBtn) {
        certBtn.href = _B() + '/certificado?curso=' + playerData.curso.id;
      }
    } else {
      certCard.classList.add('hidden');
      certLock.classList.remove('hidden');
    }
  }
}
