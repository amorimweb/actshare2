<?php $pageTitle = 'Certificado — ActShare'; ?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 py-8">
  <div id="cert-loading" class="text-center py-16 text-gray-400">
    <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
    <p>Carregando certificado...</p>
  </div>

  <div id="cert-content" class="hidden text-center">
    <div class="overflow-x-auto p-4 flex justify-center bg-gray-100/50 rounded-3xl border border-gray-200 shadow-sm max-w-full">
      <!-- Canvas de Certificado A4 Deitado (1123x794 px) -->
      <div id="cert-canvas" class="relative w-[1123px] h-[794px] bg-cover bg-no-repeat bg-center shadow-2xl border border-gray-300 bg-white flex-shrink-0" style="background-image: url('<?= BASE_PATH ?>/assets/img/certificado_default.png')">
        <!-- Nome do Aluno -->
        <div id="field-nome" class="absolute font-bold uppercase" style="font-family: 'Outfit', sans-serif;"></div>
        <!-- Texto explicativo (aprovado vs participou) -->
        <div id="field-texto" class="absolute font-medium" style="font-family: 'Inter', sans-serif;"></div>
        <!-- Nome do Treinamento -->
        <div id="field-curso" class="absolute font-extrabold" style="font-family: 'Outfit', sans-serif;"></div>
        <!-- Data de Conclusão -->
        <div id="field-data" class="absolute font-semibold" style="font-family: 'Inter', sans-serif;"></div>
        <!-- Nome do Instrutor -->
        <div id="field-instrutor" class="absolute text-center" style="font-family: 'Inter', sans-serif;">
          <div class="w-48 border-t border-gray-400 mb-1 mx-auto"></div>
          <span id="field-instrutor-nome" class="font-bold block"></span>
          <span class="text-[10px] text-gray-400 block">Instrutor Responsável</span>
        </div>
        <!-- Código de Autenticidade -->
        <div id="field-codigo" class="absolute font-mono text-[10px]" style="font-family: monospace;"></div>
        <!-- QR Code de validação -->
        <img id="field-qrcode" class="absolute" alt="QR Code de validação">
      </div>
    </div>
    
    <div class="text-center mt-8 space-x-4">
      <button onclick="window.print()" class="bg-primary hover:bg-slate-900 text-white font-bold px-8 py-3.5 rounded-xl transition-all shadow-md text-xs uppercase tracking-wider">
        Imprimir / Salvar PDF
      </button>
      <a href="<?= BASE_PATH ?>/painel" class="inline-block border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold px-8 py-3.5 rounded-xl transition-all text-xs uppercase tracking-wider">
        Voltar ao Painel
      </a>
    </div>
  </div>

  <div id="cert-error" class="hidden text-center py-16 max-w-md mx-auto">
    <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-4 mx-auto border border-red-150">
      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <h4 id="cert-error-title" class="font-bold text-gray-800 text-sm">Certificado Indisponível</h4>
    <p id="cert-error-desc" class="text-xs text-gray-500 mt-2 leading-relaxed">Este treinamento precisa estar 100% concluído para liberação do documento.</p>
    <a href="<?= BASE_PATH ?>/painel" class="mt-6 inline-block bg-primary text-white font-semibold px-5 py-2.5 rounded-xl text-xs">← Voltar ao painel</a>
  </div>
</div>

<style>
@media print {
  /* Oculta tudo na impressão exceto o canvas do certificado */
  body * {
    visibility: hidden;
  }
  #cert-canvas, #cert-canvas * {
    visibility: visible;
  }
  #cert-canvas {
    position: absolute;
    left: 0;
    top: 0;
    margin: 0;
    border: none;
    box-shadow: none;
    transform: none !important;
  }
  @page {
    size: A4 landscape;
    margin: 0;
  }
}
</style>

<script>
  const params = new URLSearchParams(location.search);
  const cursoId = params.get('curso');
  const alunoId = params.get('aluno_id');

  // Configurações padrão de coordenadas caso não esteja definido no curso
  const defaultConfig = {
    nome: { x: 561, y: 280, font_size: 38, color: '#0C1323', center: true },
    texto: { x: 561, y: 365, font_size: 16, color: '#4B5563', center: true },
    curso: { x: 561, y: 420, font_size: 26, color: '#10B981', center: true },
    data: { x: 561, y: 500, font_size: 13, color: '#6B7280', center: true },
    instrutor: { x: 320, y: 620, font_size: 15, color: '#1F2937', center: true },
    codigo: { x: 800, y: 620, font_size: 11, color: '#9CA3AF', center: true },
    qrcode: { x: 1030, y: 690, size: 70 }
  };

  async function carregarCertificado() {
    const user = authGetUser();
    if (!user) { window.location.href = BASE + '/login'; return; }
    if (!cursoId) { mostrarErro("Parâmetro de treinamento inválido.", "Nenhum treinamento foi informado."); return; }

    try {
      const url = BASE + '/api/aluno/curso/' + cursoId + (alunoId ? `?aluno_id=${alunoId}` : '');
      const data = await apiFetch(url);
      
      if (!data.matricula?.concluido) {
        mostrarErro("Treinamento não concluído", "Você precisa completar todas as aulas e provas para liberar o certificado.");
        return;
      }

      // Verifica restrição de liberação corporativa
      if (data.bloqueado_empresa && user.role !== 'admin' && user.role !== 'gestor') {
        mostrarErro("Acesso Restrito", "Este certificado está configurado para liberação somente à empresa contratante. Por favor, solicite-o ao seu gestor.");
        return;
      }

      // Regra de Liberação do Curso: Somente Empresa
      if (data.curso.certificado_liberacao === 'empresa' && user.role !== 'admin' && user.role !== 'gestor') {
        mostrarErro("Acesso Restrito", "Este certificado está configurado para liberação somente à empresa contratante. Por favor, solicite-o ao seu gestor.");
        return;
      }

      // Carrega imagem customizada de template se houver
      if (data.curso.certificado_template_url) {
        document.getElementById('cert-canvas').style.backgroundImage = `url('${data.curso.certificado_template_url}')`;
      }

      // Parsing das coordenadas
      let config = defaultConfig;
      if (data.curso.certificado_config) {
        try {
          config = JSON.parse(data.curso.certificado_config);
        } catch(e) {
          console.warn("Erro ao fazer parse do certificado_config do curso, utilizando layout padrão.", e);
        }
      }

      // Aplica posições e estilos para os campos
      aplicarEstiloCampo('field-nome', config.nome, data.aluno_nome || user.nome);
      
      // Texto dinâmico: aprovado vs participou
      const txtExplicativo = data.has_approved_exam 
        ? "concluiu com êxito e obteve aproveitamento satisfatório no treinamento"
        : "participou com aproveitamento do treinamento";
      aplicarEstiloCampo('field-texto', config.texto, txtExplicativo);

      // Nome do certificado (se houver) ou título do curso
      const nomeCurso = data.curso.nome_certificado || data.curso.titulo;
      aplicarEstiloCampo('field-curso', config.curso, nomeCurso);

      // Data de conclusão formatada
      const dtFormatada = data.matricula.data_conclusao
        ? new Date(data.matricula.data_conclusao).toLocaleDateString('pt-BR', {year:'numeric',month:'long',day:'numeric'})
        : new Date().toLocaleDateString('pt-BR', {year:'numeric',month:'long',day:'numeric'});
      const dataTexto = "Concluído em " + dtFormatada;
      aplicarEstiloCampo('field-data', config.data, dataTexto);

      // Instrutor
      aplicarEstiloCampo('field-instrutor', config.instrutor, "");
      document.getElementById('field-instrutor-nome').textContent = data.curso.instrutor_nome || 'Coordenador ActShare';

      // Código de autenticidade sequencial ([CODIGO_CURSO]-[SEQ])
      const codCurso = (data.curso.codigo || 'CUR').toUpperCase();
      const codAutenticidade = `${codCurso}-${data.matricula.id}`;
      aplicarEstiloCampo('field-codigo', config.codigo, `Código de Autenticidade: ${codAutenticidade}`);

      // QR Code apontando para a validação pública do certificado
      const qrConfig = config.qrcode || defaultConfig.qrcode;
      const urlValidacao = window.location.origin + BASE + '/validar-certificado?codigo=' + encodeURIComponent(codAutenticidade);
      const qrImg = document.getElementById('field-qrcode');
      const qrSize = qrConfig.size || 70;
      qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=${qrSize * 2}x${qrSize * 2}&data=` + encodeURIComponent(urlValidacao);
      qrImg.style.left = qrConfig.x + 'px';
      qrImg.style.top = qrConfig.y + 'px';
      qrImg.style.width = qrSize + 'px';
      qrImg.style.height = qrSize + 'px';

      document.getElementById('cert-loading').classList.add('hidden');
      document.getElementById('cert-content').classList.remove('hidden');
    } catch (err) {
      mostrarErro("Erro de Conexão", err.message || "Não foi possível carregar as informações do certificado.");
    }
  }

  function aplicarEstiloCampo(id, styleObj, text) {
    const el = document.getElementById(id);
    if (!el || !styleObj) return;

    if (text) {
      el.textContent = text;
    }

    el.style.left = styleObj.x + 'px';
    el.style.top = styleObj.y + 'px';
    el.style.fontSize = (styleObj.font_size || 14) + 'px';
    el.style.color = styleObj.color || '#000000';

    if (styleObj.center) {
      el.style.transform = 'translateX(-50%)';
    } else {
      el.style.transform = 'none';
    }
  }

  function mostrarErro(titulo, desc) {
    document.getElementById('cert-loading').classList.add('hidden');
    document.getElementById('cert-content').classList.add('hidden');
    document.getElementById('cert-error').classList.remove('hidden');
    
    if (titulo) document.getElementById('cert-error-title').textContent = titulo;
    if (desc) document.getElementById('cert-error-desc').textContent = desc;
  }

  document.addEventListener('DOMContentLoaded', carregarCertificado);
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
