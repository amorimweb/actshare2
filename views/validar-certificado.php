<?php $pageTitle = 'Validação de Certificado — ActShare'; ?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="max-w-xl mx-auto px-4 py-16">
  <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-xl">
    <div class="text-center mb-6">
      <img src="<?= BASE_PATH ?>/assets/img/logo-act2.png" alt="ActShare" class="h-14 mx-auto mb-4">
      <h1 class="text-xl font-bold text-slate-800">Verificação de Autenticidade</h1>
      <p class="text-xs text-slate-400 mt-1">Consulte a validade de certificados emitidos pela ActShare EAD.</p>
    </div>

    <!-- Campo de Busca -->
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Código de Autenticidade</label>
        <div class="flex gap-2">
          <input type="text" id="input-codigo-val" class="flex-1 border border-slate-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 bg-slate-50 uppercase font-mono tracking-wider" placeholder="Ex: QUA-48D7-1A">
          <button onclick="buscarValidadorCertificado()" class="bg-primary text-white text-xs font-bold uppercase tracking-wider px-6 py-3 rounded-lg hover:bg-slate-800 transition-colors">
            Verificar
          </button>
        </div>
      </div>

      <!-- Spinner -->
      <div id="val-loader" class="hidden text-center py-8">
        <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-2"></div>
        <p class="text-xs text-slate-400">Pesquisando certificado...</p>
      </div>

      <!-- Painel de Resultados (Sucesso) -->
      <div id="val-sucesso" class="hidden bg-emerald-50 border-2 border-emerald-500/30 rounded-xl p-6 space-y-4 animate-fadeIn">
        <div class="flex items-center gap-3 text-emerald-800 font-bold text-sm">
          <svg class="w-6 h-6 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
          Certificado Autêntico e Válido!
        </div>
        
        <div class="divide-y divide-emerald-500/10 text-xs text-slate-700 space-y-2.5 pt-2">
          <div class="flex justify-between pt-2">
            <span class="text-slate-400 font-medium">Nome do Aluno:</span>
            <span class="font-bold text-slate-800" id="res-aluno"></span>
          </div>
          <div class="flex justify-between pt-2">
            <span class="text-slate-400 font-medium">Treinamento:</span>
            <span class="font-bold text-slate-800 text-right" id="res-curso"></span>
          </div>
          <div class="flex justify-between pt-2">
            <span class="text-slate-400 font-medium">Carga Horária:</span>
            <span class="font-semibold text-slate-800" id="res-carga"></span>
          </div>
          <div class="flex justify-between pt-2">
            <span class="text-slate-400 font-medium">Data de Conclusão:</span>
            <span class="font-semibold text-slate-800" id="res-data"></span>
          </div>
          <div class="flex justify-between pt-2">
            <span class="text-slate-400 font-medium">Tipo:</span>
            <span class="font-semibold text-slate-800 uppercase" id="res-tipo"></span>
          </div>
          <div class="flex justify-between pt-2">
            <span class="text-slate-400 font-medium">Código de Autenticidade:</span>
            <span class="font-mono font-bold text-slate-800" id="res-codigo"></span>
          </div>
          <div class="flex justify-between pt-2">
            <span class="text-slate-400 font-medium">Instrutor Responsável:</span>
            <span class="font-semibold text-slate-800" id="res-instrutor"></span>
          </div>
        </div>

        <div class="pt-4 border-t border-emerald-500/10 text-center">
          <button onclick="imprimirCertificadoValido()" class="bg-primary text-white text-xs font-bold uppercase tracking-wider py-2.5 px-6 rounded-lg hover:bg-slate-800 transition-colors shadow-sm">
            Visualizar / Imprimir PDF
          </button>
        </div>
      </div>

      <!-- Painel de Resultados (Erro) -->
      <div id="val-erro" class="hidden bg-red-50 border-2 border-red-500/20 text-red-800 rounded-xl p-5 flex items-start gap-3 animate-fadeIn">
        <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
          <p class="text-sm font-bold">Certificado Não Localizado</p>
          <p class="text-xs text-red-700/80 mt-1">Verifique se o código foi digitado corretamente. Caso o erro persista, entre em contato com nosso suporte.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Container oculto de impressão com layout de Certificado Premium -->
<div id="print-certificate-container" class="hidden fixed inset-0 bg-white z-[9999] overflow-auto p-12 text-center select-none flex flex-col justify-between" style="font-family: 'Inter', sans-serif;">
  <div class="border-[12px] border-primary rounded-3xl p-16 h-full flex flex-col justify-between items-center relative">
    
    <!-- Logo -->
    <img src="<?= BASE_PATH ?>/assets/img/logo-act2.png" alt="ActShare" class="h-20 mb-6 mx-auto">
    
    <!-- Título do Certificado -->
    <div class="space-y-4">
      <h2 class="text-slate-400 font-bold uppercase tracking-widest text-sm">Certificado de Conclusão</h2>
      <p class="text-slate-500 text-base" id="print-txt-intermediario">Certificamos que o aluno(a)</p>
      <h1 class="text-4xl font-extrabold text-primary" id="print-aluno-nome"></h1>
    </div>

    <!-- Texto de aprovação/conclusão -->
    <div class="space-y-2 max-w-xl mx-auto my-6">
      <p class="text-slate-600 text-sm leading-relaxed" id="print-texto-tipo">
        concluiu com êxito o treinamento
      </p>
      <h2 class="text-2xl font-bold text-slate-800" id="print-curso-titulo"></h2>
      <p class="text-slate-500 text-xs">
        com carga horária de <span id="print-carga-horaria"></span> horas, concluído em <span id="print-data-conclusao"></span>.
      </p>
    </div>

    <!-- Rodapé: Assinatura e Código -->
    <div class="w-full flex flex-col sm:flex-row justify-between items-end mt-12 gap-8">
      <!-- Validação -->
      <div class="text-left">
        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Código de Autenticidade</p>
        <p class="text-sm font-mono font-bold text-slate-700 mt-1" id="print-codigo-autenticidade"></p>
        <p class="text-[9px] text-slate-400 mt-0.5">Valide em: <?= SITE_URL ?>/validar-certificado</p>
      </div>

      <!-- Assinatura do Instrutor -->
      <div class="text-center min-w-[180px]">
        <div class="h-12 flex items-end justify-center mb-1">
          <img id="print-assinatura-img" src="" alt="Assinatura" class="h-10 w-auto max-w-[150px] object-contain hidden">
        </div>
        <div class="border-t border-slate-300 pt-1.5">
          <p class="text-xs font-bold text-slate-700" id="print-instrutor-nome"></p>
          <p class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold">Instrutor Responsável</p>
        </div>
      </div>
    </div>

  </div>
</div>

<style>
  @media print {
    body * { display: none !important; }
    #print-certificate-container, #print-certificate-container * { display: flex !important; }
    #print-certificate-container { position: absolute; left: 0; top: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; overflow: hidden; padding: 0; }
  }
</style>

<script>
  let _certificadoValidado = null;

  document.addEventListener('DOMContentLoaded', () => {
    // Se vier código na URL (?codigo=xxx) preenche e busca
    const params = new URLSearchParams(window.location.search);
    const cod = params.get('codigo');
    if (cod) {
      document.getElementById('input-codigo-val').value = cod;
      buscarValidadorCertificado();
    }
  });

  async function buscarValidadorCertificado() {
    const input = document.getElementById('input-codigo-val');
    const codigo = input.value.trim().toUpperCase();
    const loader = document.getElementById('val-loader');
    const painelSucesso = document.getElementById('val-sucesso');
    const painelErro = document.getElementById('val-erro');

    painelSucesso.classList.add('hidden');
    painelErro.classList.add('hidden');

    if (!codigo) return;

    loader.classList.remove('hidden');

    try {
      const res = await apiFetch(BASE + '/api/certificados/validar/' + encodeURIComponent(codigo));
      loader.classList.add('hidden');
      
      _certificadoValidado = res;

      document.getElementById('res-aluno').textContent = res.cliente_nome;
      document.getElementById('res-curso').textContent = res.curso_nome;
      document.getElementById('res-carga').textContent = res.carga_horaria + ' horas';
      
      const dt = new Date(res.data_conclusao).toLocaleDateString('pt-BR', {year:'numeric',month:'long',day:'numeric'});
      document.getElementById('res-data').textContent = dt;
      document.getElementById('res-tipo').textContent = res.tipo_texto === 'aprovacao' ? 'Aprovação / Certificação' : 'Participação';
      document.getElementById('res-codigo').textContent = res.codigo_autenticidade;
      document.getElementById('res-instrutor').textContent = res.instrutor_nome;

      painelSucesso.classList.remove('hidden');
    } catch (e) {
      loader.classList.add('hidden');
      painelErro.classList.remove('hidden');
      _certificadoValidado = null;
    }
  }

  function imprimirCertificadoValido() {
    if (!_certificadoValidado) return;

    const res = _certificadoValidado;

    // Popula o container de impressão
    document.getElementById('print-aluno-nome').textContent = res.cliente_nome;
    document.getElementById('print-curso-titulo').textContent = res.curso_nome;
    document.getElementById('print-carga-horaria').textContent = res.carga_horaria;
    
    const dt = new Date(res.data_conclusao).toLocaleDateString('pt-BR', {year:'numeric',month:'long',day:'numeric'});
    document.getElementById('print-data-conclusao').textContent = dt;
    
    document.getElementById('print-codigo-autenticidade').textContent = res.codigo_autenticidade;
    document.getElementById('print-instrutor-nome').textContent = res.instrutor_nome;

    if (res.tipo_texto === 'aprovacao') {
      document.getElementById('print-txt-intermediario').textContent = 'Certificamos que o aluno(a) concluiu a avaliação com sucesso e obteve a aprovação no treinamento';
      document.getElementById('print-texto-tipo').textContent = 'do curso de qualificação em:';
    } else {
      document.getElementById('print-txt-intermediario').textContent = 'Certificamos que o aluno(a)';
      document.getElementById('print-texto-tipo').textContent = 'participou e concluiu o treinamento de:';
    }

    const imgAss = document.getElementById('print-assinatura-img');
    if (res.assinatura_url) {
      imgAss.src = res.assinatura_url;
      imgAss.classList.remove('hidden');
    } else {
      imgAss.src = '';
      imgAss.classList.add('hidden');
    }

    // Dispara a impressão nativa
    window.print();
  }
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
