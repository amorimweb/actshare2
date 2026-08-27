<?php
require_once __DIR__ . '/../config.php';
$pageTitle = 'Visualizar Curso - ActShare';
require __DIR__ . '/layout/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8">
  <div id="player-loading" class="text-center py-20 text-gray-400">
    <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
    <p>Carregando curso...</p>
  </div>

  <div id="player-content" class="hidden">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
      <div class="flex items-center justify-between gap-4 bg-primary px-5 py-3 text-white">
        <a href="<?= BASE_PATH ?>/painel" class="flex items-center gap-2 text-sm font-semibold text-white/90 hover:text-white">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          ActShare Academy
        </a>
        <div class="flex items-center gap-3 text-white/80">
          <a href="<?= BASE_PATH ?>/certificado" class="text-xs font-semibold hover:text-white">Certificados</a>
          <a href="<?= BASE_PATH ?>/meus-dados" class="h-8 w-8 rounded-full bg-white/15 flex items-center justify-center hover:bg-white/25" aria-label="Minha conta">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          </a>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_360px] gap-6">
        <!-- Coluna Esquerda: Player e Detalhes da Aula -->
        <main class="min-w-0 p-5 lg:p-6">
          <!-- Cabeçalho da Aula (Mockup) -->
          <div class="mb-4 flex items-start justify-between gap-4">
            <div class="min-w-0">
              <p id="player-titulo" class="mb-1 text-[11px] font-bold uppercase tracking-wider text-gray-400"></p>
              <h1 id="aula-titulo" class="text-lg sm:text-xl font-extrabold leading-tight text-gray-900"></h1>
            </div>
            <!-- Menu de opções (ellipsis) -->
            <button class="text-gray-400 hover:text-primary transition-colors p-1" aria-label="Mais opções">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
            </button>
          </div>

          <!-- Video Wrapper -->
          <div id="video-wrapper" class="relative aspect-video overflow-hidden rounded-xl bg-black shadow-lg">
            <iframe id="player-iframe" class="absolute inset-0 h-full w-full border-0" allowfullscreen></iframe>
          </div>

          <!-- Quiz / Provas Container -->
          <div id="quiz-container" class="hidden"></div>

          <!-- Barra de Interações sob o Vídeo (Mockup) -->
          <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-100 pb-4">
            <!-- Botão de Conclusão -->
            <div class="flex items-center gap-3 min-w-0">
              <button id="btn-concluir" onclick="marcarConcluida()"
                class="shrink-0 rounded-lg bg-secondary px-4 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:bg-green-600 disabled:opacity-60">
                Marcar como concluída
              </button>
            </div>

            <!-- Botões de Ações rápidas (Mockup) -->
            <div class="flex flex-wrap items-center gap-1.5 text-xs text-gray-500">
              <button type="button" class="flex items-center gap-1.5 border border-gray-200 rounded-lg px-3 py-2 font-semibold hover:bg-gray-50 hover:text-primary transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>
                <span>70 likes</span>
              </button>
              <button type="button" class="flex items-center justify-center border border-gray-200 rounded-lg p-2 hover:bg-gray-50 hover:text-primary transition-colors" aria-label="Dislike">
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M19 15h4V3h-4v12zm-4 2c0 1.1-.9 2-2 2H6.69l-.95 4.57-.03.32c0 .41.17.79.44 1.06L7.17 25l6.58-6.59c.37-.36.59-.86.59-1.41V7c0-1.1-.9-2-2-2H3.31l-3.02 7.05c-.09.23-.14.47-.14.73v2z"/></svg>
              </button>
              <button type="button" class="flex items-center gap-1.5 border border-gray-200 rounded-lg px-3 py-2 font-semibold hover:bg-gray-50 hover:text-primary transition-colors">
                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                <span>Amei!</span>
              </button>
              
              <!-- Toggle switch mock -->
              <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-1.5">
                <span class="text-[10px] font-bold text-gray-450 uppercase">Auto</span>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" checked class="sr-only peer">
                  <div class="w-7 h-4 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-secondary"></div>
                </label>
              </div>

              <!-- Velocidade -->
              <button type="button" class="flex items-center gap-1 border border-gray-200 rounded-lg px-3 py-2 font-semibold hover:bg-gray-50 transition-colors">
                <span>1x</span>
                <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
              </button>

              <!-- Outros botões -->
              <button type="button" class="border border-gray-200 rounded-lg p-2 hover:bg-gray-50 transition-colors" title="Modo Cinema">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
              </button>
              <button type="button" class="border border-gray-200 rounded-lg p-2 hover:bg-gray-50 transition-colors" title="Miniplayer">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </button>
              <button type="button" class="border border-gray-200 rounded-lg p-2 hover:bg-gray-50 transition-colors" title="Tela Cheia">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4h4m12 0h-4v4M4 16v4h4m12 0h-4v-4"/></svg>
              </button>
            </div>
          </div>

          <!-- Gaveta de Anotações (Adicionar anotação) -->
          <div class="mt-4 border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm">
            <button onclick="toggleAnotacoes()" class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 transition-colors text-left focus:outline-none">
              <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">Adicionar anotação</span>
              <svg id="anotacao-caret" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="anotacao-drawer" class="hidden p-4 border-t border-gray-100 space-y-3">
              <textarea id="anotacao-texto" placeholder="Escreva sua anotação sobre esta aula aqui..." rows="3"
                class="w-full rounded-lg border border-gray-200 p-3 text-xs text-gray-700 outline-none transition focus:border-secondary focus:ring-2 focus:ring-secondary/10 resize-none"></textarea>
              <div class="flex justify-end">
                <button onclick="salvarAnotacao()" class="bg-secondary hover:bg-emerald-600 text-white font-bold text-xs uppercase px-4 py-2 rounded-lg transition-colors">
                  Salvar anotação
                </button>
              </div>
            </div>
          </div>

          <!-- Descrição da Aula -->
          <section class="mt-5">
            <h2 class="mb-2 text-sm font-bold uppercase tracking-wide text-gray-400">Sobre esta aula</h2>
            <p id="aula-descricao" class="text-sm leading-relaxed text-gray-600"></p>
          </section>
        </main>

        <!-- Coluna Direita: Playlist e Certificados -->
        <aside class="border-t border-gray-100 bg-gray-50 p-4 lg:border-l lg:border-t-0 flex flex-col gap-4">
          <!-- Cabeçalho da Playlist (Mockup) -->
          <div class="flex items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
              <!-- SVG Circular Progress (Mockup 13%) -->
              <div class="relative h-12 w-12 shrink-0">
                <svg class="h-full w-full -rotate-90" viewBox="0 0 36 36">
                  <path class="text-gray-100" stroke="currentColor" stroke-width="3.5" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                  <path id="player-progresso-svg" class="text-secondary transition-all duration-500" stroke="currentColor" stroke-width="3.5" stroke-dasharray="0, 100" stroke-linecap="round" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                </svg>
                <span id="player-progresso-percent" class="absolute inset-0 flex items-center justify-center text-[10px] font-bold text-gray-800">0%</span>
              </div>
              <div class="min-w-0">
                <h2 id="sidebar-curso-titulo" class="line-clamp-2 text-xs font-bold leading-snug text-gray-900"></h2>
                <p id="player-progresso-label" class="mt-0.5 text-[10px] font-semibold text-gray-400"></p>
              </div>
            </div>
            <!-- Voltar para o painel -->
            <a href="<?= BASE_PATH ?>/painel" class="text-gray-400 hover:text-primary transition-colors p-1" title="Voltar ao Painel">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
          </div>

          <!-- Filtro de Busca -->
          <label class="relative block">
            <input id="aula-search" type="search" placeholder="Pesquisar por conteúdos..." oninput="filtrarAulas(this.value)"
              class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-3 pr-10 text-xs text-gray-700 outline-none transition focus:border-secondary focus:ring-2 focus:ring-secondary/10">
            <svg class="absolute right-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </label>

          <!-- Listagem dos Módulos e Aulas -->
          <div id="sidebar-modulos" class="max-h-[380px] space-y-3 overflow-y-auto pr-1"></div>

          <!-- Card de Certificado (Mockup) -->
          <div id="certificado-container">
            <!-- Disponível -->
            <div id="certificado-card" class="hidden rounded-xl border border-green-200 bg-green-50/50 p-3.5 flex items-center justify-between shadow-sm">
              <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 text-green-700 rounded-lg">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                  <h4 class="text-xs font-bold text-green-800">Ver Certificado</h4>
                  <p class="text-[10px] text-green-600">Disponível para download</p>
                </div>
              </div>
              <a href="#" id="btn-emitir-certificado" target="_blank" class="p-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
              </a>
            </div>
            <!-- Bloqueado -->
            <div id="certificado-lock" class="rounded-xl border border-gray-100 bg-white p-3.5 flex items-center justify-between shadow-sm">
              <div class="flex items-center gap-3">
                <div class="p-2 bg-gray-100 text-gray-400 rounded-lg">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                  <h4 class="text-xs font-bold text-gray-500">Ver certificado</h4>
                  <p class="text-[10px] text-gray-400 font-medium">Conclua todas as aulas</p>
                </div>
              </div>
              <span class="text-gray-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              </span>
            </div>
          </div>

          <!-- Card do Instrutor (Mockup) -->
          <div id="instrutor-card" class="bg-white border border-gray-200 rounded-xl p-3.5 flex items-center justify-between shadow-sm hidden">
            <div class="flex items-center gap-3">
              <img id="instrutor-avatar" src="" alt="Instrutor" class="w-9 h-9 rounded-full object-cover border border-gray-200">
              <div>
                <h4 id="instrutor-nome" class="text-xs font-bold text-gray-800"></h4>
                <p id="instrutor-cargo" class="text-[10px] text-gray-400">Professor(a)</p>
              </div>
            </div>
            <span class="text-gray-300">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </span>
          </div>
        </aside>
      </div>
    </div>
  </div>
</div>

<script>
  const CANDIDATE_IP = '<?= $_SERVER['REMOTE_ADDR'] ?>';
</script>
<script src="<?= BASE_PATH ?>/assets/js/proctoring.js?v=3"></script>
<script src="<?= BASE_PATH ?>/assets/js/quiz.js?v=3"></script>
<script src="<?= BASE_PATH ?>/assets/js/player.js?v=3"></script>
<script>
  const cursoId = <?= (int)($_GET['id'] ?? 0) ?>;
  document.addEventListener('DOMContentLoaded', () => {
    const user = authGetUser();
    if (!user) { window.location.href = BASE + '/login'; return; }
    carregarPlayer(cursoId);
  });
</script>

<!-- Modal de Pesquisa de Satisfação -->
<div id="modal-satisfacao" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm hidden animate-fadeIn">
  <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-2xl max-w-xl w-full mx-4 relative overflow-hidden animate-scaleUp">
    <!-- Header -->
    <div class="text-center pb-4 mb-4 border-b border-gray-100">
      <h3 class="text-lg font-extrabold text-primary flex items-center justify-center gap-2">
        <svg class="w-6 h-6 text-red-500 fill-current animate-pulse" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
        Pesquisa de Satisfação
      </h3>
      <p class="text-xs text-gray-500 mt-1">Sua opinião é fundamental para nós! Avalie sua experiência com corações (1 a 5).</p>
    </div>

    <!-- Erro -->
    <div id="satisfacao-erro" class="hidden mb-4 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl px-4 py-3"></div>

    <!-- Form -->
    <form id="form-satisfacao" onsubmit="enviarPesquisaSatisfacao(event)" class="space-y-4">
      <div id="perguntas-satisfacao-container" class="space-y-4 max-h-[350px] overflow-y-auto pr-2">
        <!-- Perguntas inseridas dinamicamente -->
      </div>

      <!-- Footer -->
      <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
        <button type="submit" id="btn-submit-satisfacao" class="w-full bg-secondary hover:bg-emerald-600 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-md text-xs uppercase tracking-wider">
          Enviar e Liberar Certificado
        </button>
      </div>
    </form>
  </div>
</div>

<style>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
@keyframes scaleUp {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.animate-fadeIn {
  animation: fadeIn 0.25s ease-out forwards;
}
.animate-scaleUp {
  animation: scaleUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>

<script>
async function interceptarDownloadCertificado(event, cursoId) {
  event.preventDefault();
  
  try {
    const res = await apiFetch(BASE + '/api/aluno/satisfacao/status/' + playerData.matricula.id);
    if (res.respondida) {
      window.open(BASE + '/certificado?curso=' + cursoId, '_blank');
    } else {
      renderizarPesquisaSatisfacao(res.perguntas);
    }
  } catch (err) {
    alert('Erro ao carregar status do certificado: ' + err.message);
  }
}

function renderizarPesquisaSatisfacao(perguntas) {
  const container = document.getElementById('perguntas-satisfacao-container');
  if (!container) return;
  
  container.innerHTML = perguntas.map((p, idx) => {
    return `
      <div class="space-y-1.5 bg-gray-50 p-4 border border-gray-150 rounded-2xl text-left">
        <p class="text-xs font-bold text-gray-700 leading-relaxed">${idx + 1}. ${esc(p.texto)}</p>
        <div class="flex items-center gap-2 mt-2" data-question-id="${p.id}">
          <input type="hidden" name="perg_${p.id}" value="0" class="rating-value">
          ${[1, 2, 3, 4, 5].map(rating => `
            <button type="button" onclick="setHeartRating(${p.id}, ${rating})" class="heart-btn text-gray-300 hover:scale-115 active:scale-95 transition-all text-3xl focus:outline-none">♥</button>
          `).join('')}
        </div>
      </div>
    `;
  }).join('');
  
  document.getElementById('modal-satisfacao').classList.remove('hidden');
}

function setHeartRating(questionId, rating) {
  const container = document.querySelector(`[data-question-id="${questionId}"]`);
  if (!container) return;
  
  container.querySelector('.rating-value').value = rating;
  
  const buttons = container.querySelectorAll('.heart-btn');
  buttons.forEach((btn, idx) => {
    if (idx < rating) {
      btn.classList.remove('text-gray-300');
      btn.classList.add('text-red-500');
    } else {
      btn.classList.remove('text-red-500');
      btn.classList.add('text-gray-300');
    }
  });
}

async function enviarPesquisaSatisfacao(event) {
  event.preventDefault();
  
  const form = document.getElementById('form-satisfacao');
  const errEl = document.getElementById('satisfacao-erro');
  const btn = document.getElementById('btn-submit-satisfacao');
  
  errEl.classList.add('hidden');
  
  const ratingInputs = form.querySelectorAll('.rating-value');
  const respostas = {};
  let preencheuTudo = true;
  
  ratingInputs.forEach(input => {
    const pId = input.name.replace('perg_', '');
    const val = parseInt(input.value);
    if (!val || val < 1 || val > 5) {
      preencheuTudo = false;
    }
    respostas[pId] = val;
  });
  
  if (!preencheuTudo) {
    errEl.textContent = 'Por favor, avalie todas as perguntas com corações (1 a 5).';
    errEl.classList.remove('hidden');
    return;
  }
  
  btn.disabled = true;
  btn.textContent = 'Enviando...';
  
  try {
    await apiPost(BASE + '/api/aluno/satisfacao/responder', {
      matricula_id: playerData.matricula.id,
      respostas: respostas
    });
    
    document.getElementById('modal-satisfacao').classList.add('hidden');
    window.open(BASE + '/certificado?curso=' + playerData.curso.id, '_blank');
    carregarPlayer(playerData.curso.id);
  } catch (err) {
    errEl.textContent = err.message || 'Erro ao enviar respostas.';
    errEl.classList.remove('hidden');
    btn.disabled = false;
    btn.textContent = 'Enviar e Liberar Certificado';
  }
}
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
