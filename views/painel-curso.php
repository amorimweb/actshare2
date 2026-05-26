<?php
require_once __DIR__ . '/../config.php';
$pageTitle = 'Visualizar Curso — ActShare';
require __DIR__ . '/layout/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8">
  <div id="player-loading" class="text-center py-20 text-gray-400">
    <div class="inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
    <p>Carregando curso...</p>
  </div>

  <div id="player-content" class="hidden">
    <!-- Header/Breadcrumb local -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-4 border-b border-gray-200">
      <div>
        <a href="<?= BASE_PATH ?>/painel" class="text-sm font-medium text-primary hover:underline flex items-center gap-1 mb-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          Voltar para meus cursos
        </a>
        <h1 id="player-titulo" class="text-2xl font-bold text-gray-800"></h1>
      </div>
      <div class="bg-gray-100 px-4 py-2 rounded-xl text-sm font-semibold text-gray-600 shadow-sm" id="player-progresso-label"></div>
    </div>

    <!-- Grid do player -->
    <div class="grid lg:grid-cols-3 gap-8">
      <!-- Coluna Principal (Player e Info) -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Player Wrapper -->
        <div id="video-wrapper" class="bg-black rounded-2xl overflow-hidden aspect-video shadow-lg relative">
          <iframe id="player-iframe" class="w-full h-full border-0 absolute inset-0" allowfullscreen></iframe>
        </div>
        
        <!-- Quiz Wrapper -->
        <div id="quiz-container" class="hidden"></div>
        
        <!-- Detalhes da Aula -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 pb-4 border-b border-gray-100">
            <h2 id="aula-titulo" class="text-xl font-bold text-gray-800"></h2>
            <button id="btn-concluir" onclick="marcarConcluida()"
              class="w-full sm:w-auto bg-secondary text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-green-600 transition-colors shadow-sm disabled:opacity-60">
              Marcar como concluída
            </button>
          </div>
          <p id="aula-descricao" class="text-gray-600 text-sm leading-relaxed"></p>
        </div>
      </div>

      <!-- Coluna Lateral (Cronograma / Aulas) -->
      <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
          <h3 class="font-bold text-gray-800 text-lg mb-4">Cronograma de Aulas</h3>
          <div id="sidebar-modulos" class="space-y-4"></div>
          
          <!-- Card de Certificado integrado no cronograma -->
          <div id="certificado-card" class="mt-6 pt-6 border-t border-gray-100 hidden">
            <div class="bg-gradient-to-br from-primary to-blue-800 text-white rounded-xl p-4 shadow-md text-center">
              <svg class="w-10 h-10 text-secondary mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/></svg>
              <h4 class="font-bold mb-1">Certificado Disponível!</h4>
              <p class="text-xs text-blue-200 mb-3">Você concluiu 100% das aulas deste curso.</p>
              <a href="#" id="btn-emitir-certificado" class="inline-block w-full bg-secondary hover:bg-green-600 text-white text-xs font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm">
                Emitir meu certificado
              </a>
            </div>
          </div>
          <div id="certificado-lock" class="mt-6 pt-6 border-t border-gray-100">
            <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 text-center">
              <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              <h4 class="font-bold text-gray-700 text-sm mb-1">Certificado de Conclusão</h4>
              <p class="text-xs text-gray-400">Conclua todas as aulas para liberar o certificado.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const CANDIDATE_IP = '<?= $_SERVER['REMOTE_ADDR'] ?>';
</script>
<script src="<?= BASE_PATH ?>/assets/js/proctoring.js"></script>
<script src="<?= BASE_PATH ?>/assets/js/quiz.js"></script>
<script src="<?= BASE_PATH ?>/assets/js/player.js"></script>
<script>
  const cursoId = <?= (int)($_GET['id'] ?? 0) ?>;
  document.addEventListener('DOMContentLoaded', () => {
    const user = authGetUser();
    if (!user) { window.location.href = BASE + '/login'; return; }
    carregarPlayer(cursoId);
  });
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
