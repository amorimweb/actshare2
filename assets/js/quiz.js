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

  // Remove listeners antigos se houver
  if (window._examOfflineListener) {
    window.removeEventListener('offline', window._examOfflineListener);
    window._examOfflineListener = null;
  }
  if (window._examOnlineListener) {
    window.removeEventListener('online', window._examOnlineListener);
    window._examOnlineListener = null;
  }
  if (window._examTimerInterval) {
    clearInterval(window._examTimerInterval);
    window._examTimerInterval = null;
  }

  let headerHtml = `
    <div class="mb-6 pb-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
      <div>
        <h3 class="font-bold text-gray-800 text-base">${quizData.e_prova ? 'Exame Final de Certificação' : 'Questionário de Avaliação'}</h3>
        <p class="text-xs text-gray-400 mt-0.5">${quizData.e_prova ? 'Responda com atenção. Esta prova não permite segundas tentativas.' : 'Responda às questões para concluir a aula.'}</p>
      </div>
      ${quizData.e_prova && !finalizado && quizData.tempo_limite_minutos > 0 
        ? `<div class="text-xs font-bold px-3 py-1 bg-red-100 text-red-700 rounded-full flex items-center gap-1.5 shadow-sm">
             <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-ping"></span>
             Tempo Restante: <span id="exam-timer-display">--:--</span>
           </div>`
        : `<div class="text-xs font-semibold px-3 py-1 bg-gray-100 text-gray-600 rounded-full">
             Tentativas restantes: ${tentativas}
           </div>`
      }
    </div>
  `;

  let footerHtml = '';

  if (finalizado) {
    const corBg = aprovado ? 'bg-green-50 border-green-200 text-green-800' : 'bg-orange-50 border-orange-200 text-orange-800';
    const icone = aprovado 
      ? `<svg class="w-8 h-8 text-green-600 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>`
      : `<svg class="w-8 h-8 text-orange-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
    const titulo = aprovado ? 'Aprovado no Exame!' : 'Exame Finalizado';
    const desc = aprovado 
      ? 'Parabéns! Você atingiu a nota de corte e obteve a sua certificação.'
      : 'Infelizmente você não atingiu a nota de corte recomendada.';

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
        ${p.imagem_url ? `<img src="${esc(p.imagem_url)}" alt="" class="max-w-full rounded-xl mb-4 border border-gray-200">` : ''}
        <div class="space-y-2.5" id="opcoes-${p.id}">
          ${p.opcoes.map(o => {
            let labelClass = "flex items-center gap-3 cursor-pointer group p-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all";
            // Checkbox em vez de radio: uma pergunta pode ter mais de uma
            // alternativa correta (o admin marca quantas quiser em Banco de Questões).
            let radioHtml = `<input type="checkbox" name="q${p.id}" value="${o.id}" class="w-4 h-4 accent-primary rounded" onchange="quizResponder(${p.id}, ${o.id}, this.checked)">`;

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
      if (quizData.bloquear_proctoring && typeof iniciarProctoring === 'function') {
        iniciarProctoring(aulaId, matriculaId);
      }

      // Inicializa o cronômetro da prova se tempo_limite_minutos > 0
      if (quizData.tempo_limite_minutos > 0) {
        window._examTimeRemaining = quizData.tempo_limite_minutos * 60;
        window._examIsOffline = false;
        window._examOfflineTimeRemaining = 300;
        window._examDisconnectionsCount = 0;

        let overlay = document.getElementById('exam-disconnect-overlay');
        if (!overlay) {
          overlay = document.createElement('div');
          overlay.id = 'exam-disconnect-overlay';
          overlay.className = 'hidden absolute inset-0 bg-slate-900/90 backdrop-blur-md z-[99] flex flex-col items-center justify-center p-6 text-center text-white space-y-4 rounded-2xl';
          container.style.position = 'relative';
          container.appendChild(overlay);
        }

        const updateTimerDisplay = () => {
          const min = Math.floor(window._examTimeRemaining / 60);
          const sec = window._examTimeRemaining % 60;
          const el = document.getElementById('exam-timer-display');
          if (el) el.textContent = `${String(min).padStart(2, '0')}:${String(sec).padStart(2, '0')}`;
        };

        const handleExamOffline = () => {
          window._examIsOffline = true;
          window._examDisconnectionsCount++;
          
          if (typeof registrarInfracao === 'function') {
            registrarInfracao('conexao_offline', `Queda de conexão detectada. Queda nº: ${window._examDisconnectionsCount}`);
          }

          if (window._examDisconnectionsCount > 5) {
            clearInterval(window._examTimerInterval);
            alert('Você perdeu a conexão com a internet mais de 5 vezes. A prova foi finalizada.');
            enviarRespostasQuiz(aulaId, matriculaId, true);
            return;
          }

          const overlayEl = document.getElementById('exam-disconnect-overlay');
          if (overlayEl) {
            overlayEl.innerHTML = `
              <div class="w-12 h-12 bg-red-500/20 text-red-500 rounded-full flex items-center justify-center mb-2 animate-bounce">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-3.536 4.978 4.978 0 011.414-3.536m0 0L5.636 5.636m3.536 9.192L5.636 18.364m3.536-3.536L6.343 12m-.707 0a9 9 0 010-12.728M12 12a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
              </div>
              <h4 class="font-extrabold text-sm text-white">Sem Conexão com a Internet!</h4>
              <p class="text-xs text-slate-350 max-w-sm">O relógio da prova está pausado. Você tem <strong id="exam-reconnect-countdown">05:00</strong> para se reconectar. Quedas de conexão: ${window._examDisconnectionsCount}/5.</p>
            `;
            overlayEl.classList.remove('hidden');
          }
        };

        const handleExamOnline = () => {
          window._examIsOffline = false;
          window._examOfflineTimeRemaining = 300;
          
          if (typeof registrarInfracao === 'function') {
            registrarInfracao('conexao_online', 'Conexão restabelecida.');
          }

          const overlayEl = document.getElementById('exam-disconnect-overlay');
          if (overlayEl) overlayEl.classList.add('hidden');
        };

        window._examOfflineListener = handleExamOffline;
        window._examOnlineListener = handleExamOnline;
        window.addEventListener('offline', window._examOfflineListener);
        window.addEventListener('online', window._examOnlineListener);

        window._examTimerInterval = setInterval(() => {
          if (window._examIsOffline) {
            if (window._examOfflineTimeRemaining > 0) {
              window._examOfflineTimeRemaining--;
              const minOff = Math.floor(window._examOfflineTimeRemaining / 60);
              const secOff = window._examOfflineTimeRemaining % 60;
              const recEl = document.getElementById('exam-reconnect-countdown');
              if (recEl) recEl.textContent = `${String(minOff).padStart(2, '0')}:${String(secOff).padStart(2, '0')}`;
            } else {
              window._examTimeRemaining--;
            }
          } else {
            window._examTimeRemaining--;
          }

          updateTimerDisplay();

          if (window._examTimeRemaining <= 0) {
            clearInterval(window._examTimerInterval);
            alert('Tempo limite esgotado! A sua prova foi finalizada.');
            enviarRespostasQuiz(aulaId, matriculaId, true);
          }
        }, 1000);

        updateTimerDisplay();
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

function quizResponder(perguntaId, opcaoId, checked) {
  if (!window._quizRespostas) window._quizRespostas = {};
  const atuais = window._quizRespostas[perguntaId] || [];
  if (checked) {
    if (!atuais.includes(opcaoId)) atuais.push(opcaoId);
  } else {
    const idx = atuais.indexOf(opcaoId);
    if (idx > -1) atuais.splice(idx, 1);
  }
  window._quizRespostas[perguntaId] = atuais;
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
  document.querySelectorAll('#quiz-questions-list input[type="checkbox"]').forEach(chk => {
    chk.checked = false;
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

// ============================================================
// Questionários Dinâmicos de Fixação (Mapeados após cada aula)
// ============================================================

function carregarQuizDinamico(quizId, matriculaId, containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;

  const finalizado = localStorage.getItem('quiz_completed_' + quizId) === 'true';

  // Dados mockados e estruturados com as duas perguntas dinâmicas
  const quizData = {
    finalizado: finalizado,
    aprovado: finalizado,
    tentativas_restantes: 5,
    is_dinamico: true,
    perguntas: [
      {
        id: 1,
        texto: "Qual das seguintes condutas é a mais recomendada para aplicar as diretrizes aprendidas nesta aula?",
        opcoes: [
          { id: 11, texto: "Ignorar os processos formais para agilizar a entrega.", correta: false },
          { id: 12, texto: "Integrar as normas e práticas de conformidade na rotina diária e buscar a melhoria contínua.", correta: true },
          { id: 13, texto: "Delegar a responsabilidade exclusivamente para a gerência de TI ou qualidade.", correta: false }
        ]
      },
      {
        id: 2,
        texto: "Qual o principal benefício de realizar uma revisão estruturada conforme ensinado?",
        opcoes: [
          { id: 21, texto: "Reduzir o engajamento da equipe.", correta: false },
          { id: 22, texto: "Identificar desvios precocemente e garantir a conformidade e segurança dos dados.", correta: true },
          { id: 23, texto: "Evitar qualquer tipo de auditoria interna.", correta: false }
        ]
      }
    ]
  };

  renderQuizDinamico(container, quizData, quizId, matriculaId);
}

function renderQuizDinamico(container, quizData, quizId, matriculaId) {
  const perguntas = quizData.perguntas;
  const finalizado = quizData.finalizado;

  window._quizRespostas = {};

  let headerHtml = `
    <div class="mb-6 pb-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
      <div>
        <h3 class="font-bold text-gray-800 text-base">Questionário de Fixação</h3>
        <p class="text-xs text-gray-400 mt-0.5">Responda às questões para testar seus conhecimentos e fixar o conteúdo.</p>
      </div>
    </div>
  `;

  if (finalizado) {
    headerHtml = `
      <div class="border rounded-2xl p-5 mb-6 text-center bg-green-50 border-green-200 text-green-800 animate-fadeIn animate-duration-300">
        <svg class="w-8 h-8 text-green-600 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <h4 class="font-extrabold text-base mt-2">Aprovado no Questionário!</h4>
        <p class="text-xs mt-1 opacity-90 font-medium">Parabéns! Você respondeu corretamente e fixou o conteúdo desta etapa com sucesso.</p>
      </div>
    `;
  }

  const listHtml = perguntas.map((p, i) => {
    return `
      <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 mb-4">
        <p class="font-bold text-gray-800 text-xs sm:text-sm mb-3 leading-relaxed">${i + 1}. ${esc(p.texto)}</p>
        <div class="space-y-2" id="opcoes-${p.id}">
          ${p.opcoes.map(o => {
            let labelClass = "flex items-center gap-2.5 cursor-pointer group p-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 transition-all text-xs";
            let radioHtml = `<input type="radio" name="dq${p.id}" value="${o.id}" class="w-3.5 h-3.5 accent-secondary" onchange="quizResponder(${p.id}, ${o.id})">`;

            if (finalizado) {
              radioHtml = ''; // Não exibe inputs no gabarito
              if (o.correta) {
                labelClass = "flex items-center gap-2.5 p-2 rounded-lg border-2 border-green-500 bg-green-50/50 text-green-900 font-semibold text-xs";
              } else {
                labelClass = "flex items-center gap-2.5 p-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-400 opacity-60 text-xs";
              }
            }

            return `
              <label class="${labelClass}">
                ${radioHtml}
                ${o.correta && finalizado
                  ? `<svg class="w-3.5 h-3.5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>`
                  : ''
                }
                <span>${esc(o.texto)}</span>
              </label>
            `;
          }).join('')}
        </div>
      </div>
    `;
  }).join('');

  let footerHtml = '';
  if (!finalizado) {
    footerHtml = `
      <div id="quiz-erro" class="hidden mb-4 bg-red-50 border border-red-200 text-red-750 text-xs font-semibold rounded-lg px-3 py-2"></div>
      <button id="btn-enviar-quiz-din" onclick="enviarRespostasQuizDinamico('${quizId}', ${matriculaId})"
        class="w-full bg-secondary text-white font-bold py-2.5 rounded-lg hover:bg-emerald-600 transition-colors shadow-sm text-xs uppercase tracking-wide focus:outline-none">
        Enviar Respostas
      </button>
    `;
  }

  container.innerHTML = `
    <div class="animate-fadeIn animate-duration-300">
      ${headerHtml}
      <div id="quiz-questions-list">${listHtml}</div>
      ${footerHtml}
    </div>
  `;
}

async function enviarRespostasQuizDinamico(quizId, matriculaId) {
  const btn = document.getElementById('btn-enviar-quiz-din');
  const err = document.getElementById('quiz-erro');
  if (btn) {
    btn.disabled = true;
    btn.textContent = 'Processando...';
  }
  if (err) err.classList.add('hidden');

  // Validação das respostas (Pergunta 1 correta: 12, Pergunta 2 correta: 22)
  const ans1 = window._quizRespostas[1];
  const ans2 = window._quizRespostas[2];

  if (!ans1 || !ans2) {
    if (err) {
      err.textContent = 'Por favor, responda a todas as questões antes de enviar.';
      err.classList.remove('hidden');
    }
    if (btn) {
      btn.disabled = false;
      btn.textContent = 'Enviar Respostas';
    }
    return;
  }

  const isCorrect = (parseInt(ans1) === 12 && parseInt(ans2) === 22);

  if (isCorrect) {
    // Salva conclusão no localStorage
    localStorage.setItem('quiz_completed_' + quizId, 'true');

    // Conclui a AULA PAI correspondente no banco de dados
    const parentId = parseInt(quizId.replace('quiz_din_', ''));
    try {
      await apiPost(_B() + '/api/aluno/progresso', {
        matricula_id: matriculaId,
        aula_id: parentId,
        concluida: true,
        tempo_parada: 0
      });
      // Atualiza o progresso local
      progressoMap[parentId] = { concluida: true };
      
      // Atualiza labels de progresso geral e a barra lateral
      if (typeof atualizarProgressoLabel === 'function') {
        atualizarProgressoLabel();
      }
    } catch (e) {
      console.error('Falha ao registrar progresso no banco de dados:', e);
    }

    // Renderiza novamente o quiz no estado concluído/gabarito
    carregarQuizDinamico(quizId, matriculaId, 'quiz-container');
    
    // Atualiza a sidebar de aulas (exibindo os checkmarks correspondentes)
    if (typeof renderSidebar === 'function') {
      renderSidebar();
    }
  } else {
    if (err) {
      err.textContent = 'Respostas incorretas. Revise o material e tente novamente!';
      err.classList.remove('hidden');
    }
    if (btn) {
      btn.disabled = false;
      btn.textContent = 'Enviar Respostas';
    }
  }
}
