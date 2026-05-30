// Componente de quiz interativo (substitui LessonQuiz.vue)
var _B = _B || (() => (typeof BASE !== 'undefined' ? BASE : ''));
function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

async function carregarQuiz(aulaId, matriculaId, containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;

  container.innerHTML = '<div class="text-center py-6 text-gray-400"><div class="inline-block w-6 h-6 border-4 border-primary border-t-transparent rounded-full animate-spin"></div><p class="text-xs mt-2">Carregando questionário...</p></div>';

  try {
    const res = await apiFetch(_B() + '/api/aluno/quiz/' + aulaId);
    
    if (!res.perguntas || !res.perguntas.length) {
      container.innerHTML = '<p class="text-gray-400 text-sm text-center py-6">Nenhuma questão disponível para esta aula.</p>';
      return;
    }
    
    renderQuiz(container, res, aulaId, matriculaId);
  } catch (err) {
    container.innerHTML = `<p class="text-red-500 text-sm text-center py-6">Erro ao carregar o quizz: ${err.message}</p>`;
  }
}

function renderQuiz(container, quizData, aulaId, matriculaId) {
  const perguntas = quizData.perguntas;
  const finalizado = quizData.finalizado;
  const aprovado = quizData.aprovado;
  const tentativas = quizData.tentativas_restantes;

  window._quizRespostas = {};

  let headerHtml = `
    <div class="mb-6 pb-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
      <div>
        <h3 class="font-bold text-gray-800 text-base">Questionário de Avaliação</h3>
        <p class="text-xs text-gray-400 mt-0.5">Responda às questões para concluir a aula.</p>
      </div>
      <div class="text-xs font-semibold px-3 py-1 bg-gray-100 text-gray-600 rounded-full">
        Tentativas restantes: ${tentativas}
      </div>
    </div>
  `;

  let footerHtml = '';

  if (finalizado) {
    const corBg = aprovado ? 'bg-green-50 border-green-200 text-green-800' : 'bg-orange-50 border-orange-200 text-orange-800';
    const icone = aprovado 
      ? `<svg class="w-8 h-8 text-green-600 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>`
      : `<svg class="w-8 h-8 text-orange-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
    const titulo = aprovado ? 'Aprovado no Questionário!' : 'Questionário Finalizado';
    const desc = aprovado 
      ? 'Parabéns! Você atingiu a nota de corte e concluiu esta etapa.'
      : 'Você esgotou as suas tentativas, mas a aula foi marcada como concluída para permitir o seu progresso.';

    headerHtml = `
      <div class="border rounded-2xl p-5 mb-6 text-center ${corBg}">
        ${icone}
        <h4 class="font-extrabold text-base mt-2">${titulo}</h4>
        <p class="text-xs mt-1 opacity-90">${desc}</p>
      </div>
    `;
  } else {
    footerHtml = `
      <div id="quiz-erro" class="hidden mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3"></div>
      <div id="quiz-options-block" class="hidden border border-orange-200 bg-orange-50/50 rounded-2xl p-5 mb-4 space-y-4">
        <h4 class="font-bold text-orange-800 text-sm">Quizz não aprovado</h4>
        <p class="text-xs text-orange-700" id="quiz-fail-desc"></p>
        <div class="flex flex-col sm:flex-row gap-3">
          <button onclick="refazerQuizzForm()" class="bg-primary text-white text-xs font-semibold py-2 px-4 rounded-lg hover:bg-blue-900 transition-colors shadow-sm">
            Tentar Novamente
          </button>
          <button onclick="avancarQuizzSemAprovacao(${aulaId}, ${matriculaId})" class="bg-white border border-gray-300 text-gray-700 text-xs font-semibold py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
            Avançar (Desistir)
          </button>
        </div>
      </div>
      
      <button id="btn-enviar-quiz" onclick="enviarRespostasQuiz(${aulaId}, ${matriculaId})"
        class="w-full bg-primary text-white font-semibold py-3 rounded-xl hover:bg-blue-900 transition-colors shadow-sm disabled:opacity-60">
        Enviar Respostas
      </button>
    `;
  }

  const listHtml = perguntas.map((p, i) => {
    return `
      <div class="bg-gray-50/70 border border-gray-100 rounded-2xl p-5 mb-5">
        <p class="font-semibold text-gray-800 text-sm mb-4 leading-relaxed">${i + 1}. ${esc(p.texto)}</p>
        <div class="space-y-2.5" id="opcoes-${p.id}">
          ${p.opcoes.map(o => {
            let labelClass = "flex items-center gap-3 cursor-pointer group p-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all";
            let radioHtml = `<input type="radio" name="q${p.id}" value="${o.id}" class="w-4 h-4 accent-primary" onchange="quizResponder(${p.id}, ${o.id})">`;
            
            if (finalizado) {
              radioHtml = ''; // Não exibe inputs no gabarito
              if (o.correta) {
                labelClass = "flex items-center gap-3 p-2.5 rounded-xl border-2 border-green-500 bg-green-50/50 text-green-900 font-medium";
              } else {
                labelClass = "flex items-center gap-3 p-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-400 opacity-60";
              }
            }
            
            return `
              <label class="${labelClass}">
                ${radioHtml}
                ${o.correta && finalizado 
                  ? `<svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>`
                  : ''
                }
                <span class="text-xs leading-relaxed">${esc(o.texto)}</span>
              </label>
            `;
          }).join('')}
        </div>
        ${finalizado && p.justificativa 
          ? `<div class="mt-4 p-3 bg-blue-50/70 border border-blue-100 rounded-xl text-xs text-blue-800 leading-relaxed">
              <strong>Explicação:</strong> ${esc(p.justificativa)}
             </div>`
          : ''
        }
      </div>
    `;
  }).join('');

  container.innerHTML = `
    <div class="animate-fadeIn">
      ${headerHtml}
      <div id="quiz-questions-list">${listHtml}</div>
      ${footerHtml}
    </div>
  `;

  if (quizData.e_prova) {
    if (!quizData.com_prova) {
      container.innerHTML = `
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center space-y-4 max-w-md mx-auto my-8 shadow-sm">
          <svg class="w-12 h-12 text-amber-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          <h4 class="font-bold text-amber-800 text-sm">Prova Final de Certificação</h4>
          <p class="text-xs text-amber-700 leading-relaxed">Você adquiriu este curso na modalidade <strong>Sem Prova</strong>. Para realizar a avaliação oficial e obter seu certificado da Exemplar Global, faça o upgrade da sua matrícula.</p>
          <a href="https://wa.me/5511999999999" target="_blank" class="inline-block bg-primary text-white font-bold px-4 py-2 rounded-xl text-xs">
            Falar com o Suporte
          </a>
        </div>
      `;
      return;
    }

    if (!finalizado) {
      if (typeof iniciarProctoring === 'function') {
        iniciarProctoring(aulaId, matriculaId);
      }
    } else {
      if (typeof pararProctoring === 'function') {
        pararProctoring();
      }
    }
  } else {
    if (typeof pararProctoring === 'function') {
      pararProctoring();
    }
  }
}

function quizResponder(perguntaId, opcaoId) {
  if (!window._quizRespostas) window._quizRespostas = {};
  window._quizRespostas[perguntaId] = opcaoId;
}

function refazerQuizzForm() {
  const optionsBlock = document.getElementById('quiz-options-block');
  const btnSubmit = document.getElementById('btn-enviar-quiz');
  
  if (optionsBlock) optionsBlock.classList.add('hidden');
  if (btnSubmit) {
    btnSubmit.classList.remove('hidden');
    btnSubmit.disabled = false;
    btnSubmit.textContent = 'Enviar Respostas';
  }
  
  // Limpa inputs selecionados
  document.querySelectorAll('#quiz-questions-list input[type="radio"]').forEach(radio => {
    radio.checked = false;
  });
  window._quizRespostas = {};
}

async function avancarQuizzSemAprovacao(aulaId, matriculaId) {
  const btn = document.querySelector('#quiz-options-block button:nth-child(2)');
  if (btn) btn.disabled = true;

  try {
    await apiPost(_B() + '/api/aluno/quiz/responder', {
      aula_id:      aulaId,
      matricula_id: matriculaId,
      avancar:      true
    });
    
    // Atualiza progresso local
    if (typeof carregarPlayer === 'function') {
      carregarPlayer(cursoId);
    }
  } catch (err) {
    alert(err.message || 'Erro ao avançar.');
    if (btn) btn.disabled = false;
  }
}

async function enviarRespostasQuiz(aulaId, matriculaId) {
  const btn = document.getElementById('btn-enviar-quiz');
  const err = document.getElementById('quiz-erro');
  const optionsBlock = document.getElementById('quiz-options-block');
  
  btn.disabled = true;
  btn.textContent = 'Processando...';
  if (err) err.classList.add('hidden');

  try {
    const res = await apiPost(_B() + '/api/aluno/quiz/responder', {
      aula_id:      aulaId,
      matricula_id: matriculaId,
      respostas:    window._quizRespostas || {}
    });

    if (res.aprovado) {
      // Recarregar o player e o quiz (para modo finalizado)
      if (typeof carregarPlayer === 'function') {
        carregarPlayer(cursoId);
      }
    } else {
      // Exibe painel para refazer ou avançar
      btn.classList.add('hidden');
      if (optionsBlock) {
        optionsBlock.classList.remove('hidden');
        const count = res.tentativas_restantes;
        const msg = count > 0 
          ? `Sua nota foi de ${res.nota}%. A nota mínima é 70%. Você tem ainda <strong>${count} tentativa(s)</strong> restante(s).`
          : `Sua nota foi de ${res.nota}%. Você esgotou suas tentativas e não poderá refazer.`;
        document.getElementById('quiz-fail-desc').innerHTML = msg;
        
        // Se esgotou as tentativas, o botão de refazer some e o de avançar se destaca
        const btnRefazer = optionsBlock.querySelector('button:first-of-type');
        const btnAvancar = optionsBlock.querySelector('button:last-of-type');
        if (count <= 0) {
          if (btnRefazer) btnRefazer.classList.add('hidden');
          if (btnAvancar) {
            btnAvancar.className = "w-full bg-primary text-white text-xs font-semibold py-3 rounded-xl hover:bg-blue-900 transition-colors shadow-sm";
            btnAvancar.textContent = "Finalizar Questionário e Avançar";
          }
        }
      }
    }
  } catch (e) {
    if (err) {
      err.textContent = e.message || 'Erro ao processar questionário. Certifique-se de responder todas as questões.';
      err.classList.remove('hidden');
    }
    btn.disabled = false;
    btn.textContent = 'Enviar Respostas';
  }
}
