<?php $pageTitle = 'Exame — ActShare'; ?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="max-w-3xl mx-auto px-4 py-10">

  <div id="exame-escolha" class="hidden">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Escolha o exame</h1>
    <div id="exame-escolha-lista" class="grid sm:grid-cols-2 gap-4"></div>
  </div>

  <div id="exame-loading" class="text-center py-16 text-gray-400">
    <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
    <p>Carregando exame...</p>
  </div>

  <div id="exame-bloqueado" class="hidden bg-white border border-gray-200 rounded-2xl p-10 text-center">
    <h2 class="text-lg font-bold text-gray-800 mb-2">Não foi possível abrir o exame</h2>
    <p id="exame-bloqueado-msg" class="text-sm text-gray-500"></p>
    <a href="<?= BASE_PATH ?>/painel" class="inline-block mt-6 text-primary font-semibold hover:underline">← Voltar ao Painel</a>
  </div>

  <!-- Prova em andamento -->
  <div id="exame-prova" class="hidden">
    <div class="flex items-center justify-between mb-6 sticky top-16 bg-gray-50/95 backdrop-blur py-3 z-10">
      <h1 id="exame-titulo" class="text-xl font-bold text-gray-800"></h1>
      <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-4 py-2 shadow-sm">
        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span id="exame-timer" class="font-mono font-bold text-gray-800 text-sm">--:--</span>
      </div>
    </div>

    <div id="exame-perguntas-container" class="space-y-6"></div>

    <button onclick="finalizarExame()" class="w-full bg-secondary text-white font-bold py-3.5 rounded-xl hover:bg-green-600 transition-colors mt-6">
      Finalizar Exame
    </button>
  </div>

  <!-- Confirmação antes de finalizar -->
  <div id="modal-confirmar-exame" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-md p-6">
      <h2 class="text-lg font-bold text-gray-800 mb-3">Finalizar exame?</h2>
      <p id="confirmar-exame-texto" class="text-sm text-gray-600 mb-6">Tem certeza que deseja finalizar? Essa ação não pode ser desfeita.</p>
      <div class="flex gap-3">
        <button onclick="confirmarFinalizarExame()" class="flex-1 bg-secondary text-white font-bold py-2.5 rounded-lg">Sim, finalizar</button>
        <button onclick="document.getElementById('modal-confirmar-exame').classList.add('hidden')" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg">Cancelar</button>
      </div>
    </div>
  </div>

  <!-- Resultado -->
  <div id="exame-resultado" class="hidden bg-white border border-gray-200 rounded-2xl p-10 text-center">
    <div id="resultado-icone" class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center"></div>
    <h2 id="resultado-titulo" class="text-xl font-bold mb-2"></h2>
    <p id="resultado-detalhe" class="text-sm text-gray-500 mb-1"></p>
    <p id="resultado-retake" class="text-xs text-amber-600 mt-2"></p>
    <a href="<?= BASE_PATH ?>/painel" class="inline-block mt-6 text-primary font-semibold hover:underline">← Voltar ao Painel</a>
  </div>

</div>

<script>
  const cursoId = <?= (int)($_GET['id'] ?? 0) ?>;
  const EXAME_TIPO_LABEL = { AVALIACAO: 'Avaliação', QM: 'Exame QM', AU: 'Exame AU', TL: 'Exame TL' };
  let tentativaAtual = null;
  let perguntasAtuais = [];
  let respostasAtuais = {};
  let timerInterval = null;
  let fimPrazo = null;

  document.addEventListener('DOMContentLoaded', async () => {
    const user = authGetUser();
    if (!user) { window.location.href = BASE + '/login?redirect=' + encodeURIComponent(location.href); return; }

    const exameIdParam = new URLSearchParams(location.search).get('exame_id');
    if (exameIdParam) {
      iniciarExame(parseInt(exameIdParam));
      return;
    }

    // Sem exame_id na URL: descobre quais exames o aluno escolheu para este curso
    try {
      const matriculas = await apiFetch(BASE + '/api/aluno/matriculas');
      const matricula = matriculas.find(m => m.curso_id == cursoId);
      const selecionados = (matricula?.exames_selecionados || '').split(',').filter(Boolean);
      if (!selecionados.length) {
        mostrarBloqueado('Você não optou por nenhuma Avaliação/Exame neste treinamento.');
        return;
      }

      const curso = await apiFetch(BASE + '/api/cursos/' + cursoId);
      const examesDisponiveis = (curso.exames || []).filter(ex => selecionados.includes(ex.tipo));

      if (examesDisponiveis.length === 1) {
        iniciarExame(examesDisponiveis[0].id);
        return;
      }

      document.getElementById('exame-loading').classList.add('hidden');
      document.getElementById('exame-escolha').classList.remove('hidden');
      document.getElementById('exame-escolha-lista').innerHTML = examesDisponiveis.map(ex => `
        <button onclick="iniciarExame(${ex.id})" class="text-left border border-gray-200 rounded-xl p-5 hover:border-primary hover:shadow-md transition-all bg-white">
          <span class="font-bold text-gray-800">${EXAME_TIPO_LABEL[ex.tipo] || ex.tipo}</span>
        </button>
      `).join('');
    } catch (e) {
      mostrarBloqueado(e.message || 'Erro ao carregar exame.');
    }
  });

  function mostrarBloqueado(msg) {
    document.getElementById('exame-loading').classList.add('hidden');
    document.getElementById('exame-escolha').classList.add('hidden');
    document.getElementById('exame-bloqueado').classList.remove('hidden');
    document.getElementById('exame-bloqueado-msg').textContent = msg;
  }

  async function iniciarExame(exameCursoId) {
    document.getElementById('exame-escolha').classList.add('hidden');
    document.getElementById('exame-loading').classList.remove('hidden');
    try {
      const data = await apiFetch(BASE + '/api/aluno/exame/' + exameCursoId);
      tentativaAtual = data.tentativa_id;
      perguntasAtuais = data.perguntas;
      respostasAtuais = {};

      document.getElementById('exame-loading').classList.add('hidden');
      document.getElementById('exame-prova').classList.remove('hidden');
      document.getElementById('exame-titulo').textContent = EXAME_TIPO_LABEL[data.exame.tipo] || data.exame.nome || 'Exame';

      // Tempo restante já vem calculado do servidor — evita qualquer
      // problema de fuso horário entre o banco e o navegador do aluno.
      fimPrazo = Date.now() + data.segundos_restantes * 1000;
      iniciarTimer();

      renderPerguntasExame();
    } catch (e) {
      mostrarBloqueado(e.message || 'Erro ao iniciar exame.');
    }
  }

  function renderPerguntasExame() {
    const container = document.getElementById('exame-perguntas-container');
    container.innerHTML = perguntasAtuais.map((p, i) => `
      <div class="bg-white border border-gray-200 rounded-xl p-5">
        <p class="font-semibold text-gray-800 mb-3">${i + 1}. ${esc(p.texto)}</p>
        ${p.imagem_url ? `<img src="${esc(p.imagem_url)}" class="max-w-full rounded-lg mb-3">` : ''}
        <div class="space-y-2">
          ${p.opcoes.map(o => `
            <label class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-2 cursor-pointer hover:bg-gray-50">
              <input type="checkbox" onchange="marcarResposta(${p.id}, ${o.id}, this.checked)" class="accent-primary">
              <span class="text-sm text-gray-700">${esc(o.texto)}</span>
            </label>
          `).join('')}
        </div>
      </div>
    `).join('');
  }

  function marcarResposta(perguntaId, opcaoId, marcado) {
    if (!respostasAtuais[perguntaId]) respostasAtuais[perguntaId] = [];
    if (marcado) {
      respostasAtuais[perguntaId].push(opcaoId);
    } else {
      respostasAtuais[perguntaId] = respostasAtuais[perguntaId].filter(id => id !== opcaoId);
    }
  }

  function iniciarTimer() {
    atualizarTimer();
    timerInterval = setInterval(atualizarTimer, 1000);
  }

  function atualizarTimer() {
    const restanteMs = fimPrazo - Date.now();
    if (restanteMs <= 0) {
      clearInterval(timerInterval);
      document.getElementById('exame-timer').textContent = '00:00';
      confirmarFinalizarExame(); // tempo esgotado: finaliza automaticamente
      return;
    }
    const min = Math.floor(restanteMs / 60000);
    const seg = Math.floor((restanteMs % 60000) / 1000);
    document.getElementById('exame-timer').textContent = `${String(min).padStart(2, '0')}:${String(seg).padStart(2, '0')}`;
  }

  function finalizarExame() {
    const totalPerguntas = perguntasAtuais.length;
    const respondidas = Object.values(respostasAtuais).filter(r => r.length > 0).length;
    const faltam = totalPerguntas - respondidas;

    document.getElementById('confirmar-exame-texto').textContent = faltam > 0
      ? `Você ainda tem ${faltam} pergunta(s) sem resposta. Elas serão consideradas erradas. Deseja finalizar mesmo assim?`
      : 'Tem certeza que deseja finalizar? Essa ação não pode ser desfeita.';
    document.getElementById('modal-confirmar-exame').classList.remove('hidden');
  }

  async function confirmarFinalizarExame() {
    document.getElementById('modal-confirmar-exame').classList.add('hidden');
    clearInterval(timerInterval);

    try {
      const resultado = await apiPost(BASE + '/api/aluno/exame/' + tentativaAtual + '/finalizar', { respostas: respostasAtuais });
      document.getElementById('exame-prova').classList.add('hidden');
      document.getElementById('exame-resultado').classList.remove('hidden');

      const aprovado = resultado.resultado === 'aprovado';
      document.getElementById('resultado-icone').className = `w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center ${aprovado ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'}`;
      document.getElementById('resultado-icone').innerHTML = aprovado
        ? '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
        : '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';

      document.getElementById('resultado-titulo').textContent = aprovado ? 'Aprovado!' : 'Reprovado';
      document.getElementById('resultado-titulo').className = `text-xl font-bold mb-2 ${aprovado ? 'text-green-700' : 'text-red-700'}`;
      document.getElementById('resultado-detalhe').textContent =
        `Do total de ${resultado.total_questoes} questões, você acertou ${resultado.acertos}, errou ${resultado.erros}, não respondeu ${resultado.nao_respondidas} (${resultado.percentual}% de acerto).`;

      if (!aprovado && resultado.prazo_retake_ate) {
        const data = new Date(resultado.prazo_retake_ate.replace(' ', 'T'));
        document.getElementById('resultado-retake').textContent = `Você pode refazer este exame até ${data.toLocaleDateString('pt-BR')}.`;
      }
    } catch (e) {
      alert(e.message || 'Erro ao finalizar exame.');
    }
  }

  function esc(s) {
    if (!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
