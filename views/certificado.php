<?php $pageTitle = 'Certificado — ActShare'; ?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="max-w-3xl mx-auto px-4 py-12">
  <div id="cert-loading" class="text-center py-16 text-gray-400">
    <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
    <p>Carregando certificado...</p>
  </div>

  <div id="cert-content" class="hidden">
    <div class="border-8 border-primary rounded-2xl p-12 text-center bg-white shadow-xl">
      <img src="<?= BASE_PATH ?>/assets/img/logo-act2.png" alt="ActShare" class="h-16 mx-auto mb-8">
      <p class="text-gray-500 text-sm uppercase tracking-widest mb-4">Certificamos que</p>
      <h1 id="cert-nome" class="text-4xl font-bold text-primary mb-4"></h1>
      <p class="text-gray-600 mb-2">concluiu com êxito o curso</p>
      <h2 id="cert-curso" class="text-2xl font-semibold text-gray-800 mb-8"></h2>
      <p id="cert-data" class="text-gray-400 text-sm"></p>
      <div class="mt-10 pt-8 border-t border-gray-200">
        <div class="flex justify-center">
          <div class="text-center">
            <div class="w-32 border-t-2 border-gray-400 mb-2 mx-auto"></div>
            <p id="cert-instrutor" class="text-sm text-gray-600 font-medium"></p>
            <p class="text-xs text-gray-400">Instrutor responsável</p>
          </div>
        </div>
      </div>
    </div>
    <div class="text-center mt-6">
      <button onclick="window.print()" class="bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-blue-900 transition-colors">
        Imprimir / Salvar PDF
      </button>
    </div>
  </div>

  <div id="cert-error" class="hidden text-center py-16">
    <p class="text-gray-500">Certificado não disponível. O curso precisa estar concluído.</p>
    <a href="<?= BASE_PATH ?>/painel" class="mt-4 inline-block text-primary hover:underline">← Voltar ao painel</a>
  </div>
</div>

<script>
  const params = new URLSearchParams(location.search);
  const cursoId = params.get('curso');

  async function carregarCertificado() {
    const user = authGetUser();
    if (!user) { window.location.href = BASE + '/login'; return; }
    if (!cursoId) { mostrarErro(); return; }

    try {
      const data = await apiFetch(BASE + '/api/aluno/curso/' + cursoId);
      if (!data.matricula?.concluido) { mostrarErro(); return; }

      document.getElementById('cert-loading').classList.add('hidden');
      document.getElementById('cert-content').classList.remove('hidden');
      document.getElementById('cert-nome').textContent    = user.nome;
      document.getElementById('cert-curso').textContent   = data.curso.titulo;
      document.getElementById('cert-instrutor').textContent = data.curso.instrutor?.nome || 'ActShare';
      const dt = data.matricula.data_conclusao
        ? new Date(data.matricula.data_conclusao).toLocaleDateString('pt-BR', {year:'numeric',month:'long',day:'numeric'})
        : new Date().toLocaleDateString('pt-BR', {year:'numeric',month:'long',day:'numeric'});
      document.getElementById('cert-data').textContent = dt;
    } catch { mostrarErro(); }
  }

  function mostrarErro() {
    document.getElementById('cert-loading').classList.add('hidden');
    document.getElementById('cert-error').classList.remove('hidden');
  }

  document.addEventListener('DOMContentLoaded', carregarCertificado);
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
