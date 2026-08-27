// Motor de Proctoring (Anti-Cheat) para Avaliações EAD ActShare
// Em conformidade com o padrão Exemplar Global

let proctoringState = {
  aulaId: null,
  matriculaId: null,
  cameraStream: null,
  screenStream: null,
  active: false,
  fullscreenAttempts: 0,
  eventListeners: {}
};

// Pega base do servidor
const getBaseUrl = () => (typeof BASE !== 'undefined' ? BASE : '');

// Inicia o Proctoring
async function iniciarProctoring(aulaId, matriculaId) {
  if (proctoringState.active) {
    pararProctoring();
  }

  proctoringState.aulaId = aulaId;
  proctoringState.matriculaId = matriculaId;
  proctoringState.active = true;
  proctoringState.fullscreenAttempts = 0;

  // Cria os elementos do Proctoring na tela
  criarElementosInterfaceProctoring();

  // Exibe o setup de permissões
  document.getElementById('proctoring-setup-overlay').classList.remove('hidden');
}

// Para o Proctoring e limpa todos os recursos
function pararProctoring() {
  if (!proctoringState.active) return;

  // Para streams de mídia
  if (proctoringState.cameraStream) {
    proctoringState.cameraStream.getTracks().forEach(track => track.stop());
    proctoringState.cameraStream = null;
  }
  if (proctoringState.screenStream) {
    proctoringState.screenStream.getTracks().forEach(track => track.stop());
    proctoringState.screenStream = null;
  }

  // Remove os listeners de eventos
  removerListenersProctoring();

  // Esconde elementos visuais
  const overlay = document.getElementById('proctoring-setup-overlay');
  const camWidget = document.getElementById('proctoring-camera-widget');
  const watermark = document.getElementById('proctoring-watermark');
  
  if (overlay) overlay.remove();
  if (camWidget) camWidget.remove();
  if (watermark) watermark.remove();

  // Tenta sair do modo tela cheia se estiver ativo
  try {
    if (document.fullscreenElement) {
      document.exitFullscreen();
    }
  } catch {}

  proctoringState.active = false;
}

// Cria elementos do layout (Overlay, Webcam Preview e Marca d'Água)
function criarElementosInterfaceProctoring() {
  // 1. Overlay de bloqueio/permissões
  if (!document.getElementById('proctoring-setup-overlay')) {
    const overlay = document.createElement('div');
    overlay.id = 'proctoring-setup-overlay';
    overlay.className = 'absolute inset-0 bg-gray-900/95 backdrop-blur-md z-50 rounded-2xl flex flex-col items-center justify-center p-6 text-center text-white space-y-6 hidden';
    overlay.innerHTML = `
      <div class="w-14 h-14 bg-amber-500/20 text-amber-500 rounded-full flex items-center justify-center mb-2 animate-pulse">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
      </div>
      <h3 class="text-xl font-extrabold tracking-tight">Monitoramento Anti-Fraude Ativado</h3>
      <p class="text-xs text-gray-400 max-w-md leading-relaxed">
        Esta prova é monitorada para atender aos requisitos de conformidade internacional (<strong>Exemplar Global</strong>). Habilite as permissões abaixo para iniciar:
      </p>
      
      <div class="w-full max-w-xs space-y-2.5 text-left text-xs">
        <div id="status-cam-row" class="flex justify-between items-center bg-white/5 border border-white/10 px-4 py-3 rounded-xl">
          <span class="font-medium">1. Câmera de Identificação</span>
          <span id="proc-status-camera" class="font-bold text-red-400 flex items-center gap-1">Pendente</span>
        </div>
        <div id="status-screen-row" class="flex justify-between items-center bg-white/5 border border-white/10 px-4 py-3 rounded-xl">
          <span class="font-medium">2. Compartilhamento de Tela</span>
          <span id="proc-status-screen" class="font-bold text-red-400 flex items-center gap-1">Pendente</span>
        </div>
        <div id="status-fs-row" class="flex justify-between items-center bg-white/5 border border-white/10 px-4 py-3 rounded-xl">
          <span class="font-medium">3. Modo Tela Cheia</span>
          <span id="proc-status-fs" class="font-bold text-red-400 flex items-center gap-1">Pendente</span>
        </div>
      </div>
      
      <button id="btn-proctoring-start" onclick="executarSequenciaPermissoes()"
        class="bg-secondary hover:bg-green-600 text-white font-bold px-6 py-3 rounded-xl text-xs tracking-wide transition-all shadow-lg uppercase">
        Autorizar e Iniciar Avaliação
      </button>
    `;
    const container = document.getElementById('quiz-container');
    if (container) {
      container.style.position = 'relative';
      container.appendChild(overlay);
    }
  }

  // 2. Widget de Câmera Flutuante
  if (!document.getElementById('proctoring-camera-widget')) {
    const camWidget = document.createElement('div');
    camWidget.id = 'proctoring-camera-widget';
    camWidget.className = 'fixed bottom-4 right-4 w-36 h-28 bg-black border border-gray-700 rounded-2xl overflow-hidden shadow-2xl z-50 pointer-events-none hidden transition-all duration-300';
    camWidget.innerHTML = `
      <video id="proctoring-video" autoplay muted playsinline class="w-full h-full object-cover"></video>
      <div class="absolute bottom-1 left-2 text-[8px] bg-red-600 text-white px-1.5 py-0.5 rounded font-bold uppercase tracking-wider animate-pulse flex items-center gap-1">
        <span class="w-1.5 h-1.5 rounded-full bg-white"></span> Gravando
      </div>
    `;
    document.body.appendChild(camWidget);
  }

  // 3. Marca d'Água Dinâmica
  if (!document.getElementById('proctoring-watermark')) {
    const user = authGetUser();
    const nome = user ? user.nome : 'Aluno';
    const email = user ? user.email : 'email@actshare.com.br';
    const ip = typeof CANDIDATE_IP !== 'undefined' ? CANDIDATE_IP : '127.0.0.1';
    const dataAtual = new Date().toLocaleDateString('pt-BR');
    const txtWatermark = `${nome} | ${email} | IP: ${ip} | ${dataAtual}`;

    const watermark = document.createElement('div');
    watermark.id = 'proctoring-watermark';
    watermark.className = 'absolute inset-0 pointer-events-none z-40 overflow-hidden select-none opacity-[0.03] text-gray-900 text-[10px] font-bold uppercase grid grid-cols-3 grid-rows-3 items-center justify-items-center hidden';
    
    for (let i = 0; i < 9; i++) {
      const span = document.createElement('span');
      span.className = '-rotate-12 transform select-none whitespace-nowrap';
      span.textContent = txtWatermark;
      watermark.appendChild(span);
    }
    const container = document.getElementById('quiz-container');
    if (container) container.appendChild(watermark);
  }
}

// Sequência lógica de ativação de permissões
async function executarSequenciaPermissoes() {
  const btn = document.getElementById('btn-proctoring-start');
  btn.disabled = true;
  btn.textContent = 'Processando...';

  // A. Câmera e B. Compartilhamento de tela são OPCIONAIS — o escopo pede
  // captura "somente se o usuário aceitar". Negar não bloqueia a prova, só
  // fica registrado que a sessão não teve esse tipo de captura.
  await requestCamera();
  await requestScreenShare();

  // C. Tela Cheia continua obrigatória (essa sim trava a prova se negada).
  const fsOk = await requestFullscreenMode();
  if (!fsOk) {
    btn.disabled = false;
    btn.textContent = 'Autorizar e Iniciar Avaliação';
    return;
  }

  // Se tudo foi autorizado, libera a prova
  finalizarAtivacaoProctoring();
}

// 1. Solicita e exibe Câmera
async function requestCamera() {
  const statusEl = document.getElementById('proc-status-camera');
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 320, height: 240 } });
    proctoringState.cameraStream = stream;
    
    // Liga o feed de vídeo
    const video = document.getElementById('proctoring-video');
    if (video) {
      video.srcObject = stream;
    }
    
    statusEl.innerHTML = `✓ Ativo`;
    statusEl.className = 'font-bold text-green-500 flex items-center gap-1';
    
    // Monitora track desabilitado
    stream.getVideoTracks()[0].addEventListener('mute', () => {
      registrarInfracao('camera_status', 'Câmera foi mutada ou desativada pelo usuário');
    });

    return true;
  } catch (err) {
    statusEl.innerHTML = `— Não autorizado`;
    statusEl.className = 'font-bold text-slate-400 flex items-center gap-1';
    registrarInfracao('camera_nao_autorizada', 'Aluno optou por não autorizar a webcam.');
    return false;
  }
}

// 2. Solicita Compartilhamento de Tela
async function requestScreenShare() {
  const statusEl = document.getElementById('proc-status-screen');
  try {
    const stream = await navigator.mediaDevices.getDisplayMedia({ video: true });
    proctoringState.screenStream = stream;

    statusEl.innerHTML = `✓ Ativo`;
    statusEl.className = 'font-bold text-green-500 flex items-center gap-1';

    // Monitora fim do compartilhamento de tela
    const screenTrack = stream.getVideoTracks()[0];
    screenTrack.addEventListener('ended', () => {
      registrarInfracao('compartilhamento_status', 'O compartilhamento de tela foi interrompido.');
      bloquearProvaPorInfracao('Você interrompeu o compartilhamento da tela inteira. A prova foi bloqueada.');
    });

    return true;
  } catch (err) {
    statusEl.innerHTML = `— Não autorizado`;
    statusEl.className = 'font-bold text-slate-400 flex items-center gap-1';
    registrarInfracao('tela_nao_autorizada', 'Aluno optou por não autorizar o compartilhamento de tela.');
    return false;
  }
}

// 3. Solicita Modo Tela Cheia
async function requestFullscreenMode() {
  const statusEl = document.getElementById('proc-status-fs');
  const container = document.getElementById('quiz-container');
  
  try {
    if (container.requestFullscreen) {
      await container.requestFullscreen();
    } else if (container.webkitRequestFullscreen) {
      await container.webkitRequestFullscreen();
    } else {
      throw new Error('Fullscreen não suportado');
    }
    
    statusEl.innerHTML = `✓ Ativo`;
    statusEl.className = 'font-bold text-green-500 flex items-center gap-1';
    return true;
  } catch (err) {
    statusEl.innerHTML = `✗ Negado`;
    statusEl.className = 'font-bold text-red-500 flex items-center gap-1';
    alert('Erro: Não foi possível entrar no modo Tela Cheia obrigatório.');
    return false;
  }
}

// Liberação total da prova e registro de listeners
function finalizarAtivacaoProctoring() {
  document.getElementById('proctoring-setup-overlay').classList.add('hidden');
  document.getElementById('proctoring-camera-widget').classList.remove('hidden');
  document.getElementById('proctoring-watermark').classList.remove('hidden');

  registrarListenersProctoring();
}

// Adiciona Listeners de Interceptação/Monitoramento
function registrarListenersProctoring() {
  const container = document.getElementById('quiz-container');

  // A. Detecção de Saída de Fullscreen
  proctoringState.eventListeners.fullscreenchange = () => {
    if (!document.fullscreenElement && !document.webkitFullscreenElement) {
      proctoringState.fullscreenAttempts++;
      registrarInfracao('saida_fullscreen', `Saiu do modo tela cheia. Tentativa número: ${proctoringState.fullscreenAttempts}`);
      
      if (proctoringState.fullscreenAttempts >= 3) {
        bloquearProvaPorInfracao('Você saiu do modo tela cheia mais de 3 vezes. A prova foi suspensa automaticamente.');
      } else {
        alert(`Atenção: O modo tela cheia é obrigatório. Retorne imediatamente. (${proctoringState.fullscreenAttempts}/3)`);
        requestFullscreenMode();
      }
    }
  };
  document.addEventListener('fullscreenchange', proctoringState.eventListeners.fullscreenchange);
  document.addEventListener('webkitfullscreenchange', proctoringState.eventListeners.fullscreenchange);

  // B. Detecção de Troca de Foco (Aba/Minimizar)
  proctoringState.eventListeners.visibilitychange = () => {
    if (document.hidden) {
      registrarInfracao('troca_foco_aba', 'Aba da avaliação perdeu foco (document ocultado).');
    }
  };
  document.addEventListener('visibilitychange', proctoringState.eventListeners.visibilitychange);

  // C. Detecção de Blur da janela
  proctoringState.eventListeners.blur = () => {
    registrarInfracao('troca_foco_aba', 'Janela perdeu foco (clique fora do navegador ou Alt+Tab).');
  };
  window.addEventListener('blur', proctoringState.eventListeners.blur);

  // D. Detecção de Botão Direito (Context Menu)
  proctoringState.eventListeners.contextmenu = (e) => {
    e.preventDefault();
    registrarInfracao('clique_direito', 'Tentativa de clique direito (menu de contexto).');
    mostrarAvisoToast('Ação bloqueada: Clique Direito não permitido!');
  };
  container.addEventListener('contextmenu', proctoringState.eventListeners.contextmenu);

  // E. Bloqueio de Atalhos Físicos (Ctrl+C, Ctrl+V, F12, Ctrl+U)
  proctoringState.eventListeners.keydown = (e) => {
    const ctrlKey = e.ctrlKey || e.metaKey;
    const key = e.key.toLowerCase();

    // Bloqueia Ctrl+C, Ctrl+V, Ctrl+X, Ctrl+U e F12
    if ((ctrlKey && ['c', 'v', 'x', 'u'].includes(key)) || key === 'f12') {
      e.preventDefault();
      registrarInfracao('atalho_bloqueado', `Atalho de teclado interceptado: ${e.key}`);
      mostrarAvisoToast(`Ação bloqueada: Atalho ${e.key} não permitido!`);
    }

    // Bloqueia PrintScreen
    if (e.key === 'PrintScreen' || e.keyCode === 44) {
      e.preventDefault();
      navigator.clipboard.writeText("").catch(() => {});
      registrarInfracao('print_screen', 'Tentativa de tirar Print Screen detectada.');
      mostrarAvisoToast('Ação bloqueada: Print Screen não permitido!');
    }
  };
  window.addEventListener('keydown', proctoringState.eventListeners.keydown);

  // F. Detecção de Mouse Sair da Página (mouseleave)
  proctoringState.eventListeners.mouseleave = () => {
    registrarInfracao('mouse_fora', 'Mouse saiu dos limites da janela da avaliação.');
  };
  document.addEventListener('mouseleave', proctoringState.eventListeners.mouseleave);

  // G. Detecção do PrintScreen em keyup
  proctoringState.eventListeners.keyup = (e) => {
    if (e.key === 'PrintScreen' || e.keyCode === 44) {
      navigator.clipboard.writeText("").catch(() => {});
      registrarInfracao('print_screen', 'Tentativa de tirar Print Screen detectada.');
      mostrarAvisoToast('Ação bloqueada: Print Screen não permitido!');
    }
  };
  window.addEventListener('keyup', proctoringState.eventListeners.keyup);
}

// Remove todos os event listeners ao fechar ou resetar a prova
function removerListenersProctoring() {
  if (proctoringState.eventListeners.fullscreenchange) {
    document.removeEventListener('fullscreenchange', proctoringState.eventListeners.fullscreenchange);
    document.removeEventListener('webkitfullscreenchange', proctoringState.eventListeners.fullscreenchange);
  }
  if (proctoringState.eventListeners.visibilitychange) {
    document.removeEventListener('visibilitychange', proctoringState.eventListeners.visibilitychange);
  }
  if (proctoringState.eventListeners.blur) {
    window.removeEventListener('blur', proctoringState.eventListeners.blur);
  }
  if (proctoringState.eventListeners.mouseleave) {
    document.removeEventListener('mouseleave', proctoringState.eventListeners.mouseleave);
  }
  if (proctoringState.eventListeners.keyup) {
    window.removeEventListener('keyup', proctoringState.eventListeners.keyup);
  }
  
  const container = document.getElementById('quiz-container');
  if (container && proctoringState.eventListeners.contextmenu) {
    container.removeEventListener('contextmenu', proctoringState.eventListeners.contextmenu);
  }
  
  if (proctoringState.eventListeners.keydown) {
    window.removeEventListener('keydown', proctoringState.eventListeners.keydown);
  }

  proctoringState.eventListeners = {};
}

// Salva a infração no banco de dados via API
async function registrarInfracao(tipo, detalhes) {
  if (!proctoringState.active) return;
  
  try {
    await apiPost(getBaseUrl() + '/api/aluno/quiz/proctoring', {
      matricula_id: proctoringState.matriculaId,
      aula_id: proctoringState.aulaId,
      tipo_evento: tipo,
      detalhes: detalhes
    });
  } catch (err) {
    console.error('Falha ao registrar log de proctoring:', err);
  }
}

// Bloqueia a avaliação se as regras críticas forem violadas
function bloquearProvaPorInfracao(mensagem) {
  // Para streams
  pararProctoring();

  const container = document.getElementById('quiz-container');
  if (container) {
    container.innerHTML = `
      <div class="bg-red-50 border-2 border-red-300 rounded-2xl p-8 text-center space-y-4 max-w-lg mx-auto my-10 shadow-lg">
        <svg class="w-16 h-16 text-red-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <h4 class="font-extrabold text-red-800 text-lg">Avaliação Bloqueada por Segurança</h4>
        <p class="text-sm text-red-700 leading-relaxed">${mensagem}</p>
        <p class="text-xs text-gray-500">Este incidente foi logado e reportado para a coordenação pedagógica.</p>
        <button onclick="location.reload()" class="bg-red-600 hover:bg-red-700 text-white font-bold px-5 py-2 rounded-lg text-xs transition-colors">
          Recarregar Tela
        </button>
      </div>
    `;
  }
}

// Exibe um toast pequeno na tela
function mostrarAvisoToast(mensagem) {
  let toast = document.getElementById('proctoring-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'proctoring-toast';
    toast.className = 'fixed top-20 left-1/2 -translate-x-1/2 bg-gray-900/90 text-white px-5 py-3 rounded-xl text-xs font-bold shadow-2xl z-50 border border-gray-700 pointer-events-none transition-all duration-300 flex items-center gap-2';
    document.body.appendChild(toast);
  }

  toast.innerHTML = `
    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <span>${mensagem}</span>
  `;
  toast.style.opacity = '1';
  
  setTimeout(() => {
    toast.style.opacity = '0';
  }, 2500);
}
