<?php $pageTitle = 'Pesquisa de Satisfação — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="mb-8">
  <h1 class="text-2xl font-bold text-slate-800">Pesquisa de Satisfação</h1>
  <p class="text-xs text-slate-400 mt-1">Monitore o feedback dos alunos sobre a didática, o conteúdo, o material e a plataforma.</p>
</div>

<!-- Grid de Indicadores consolidado -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="satisfacao-relatorio-container">
  <div class="col-span-full text-center py-12 text-slate-400 bg-white rounded-xl border border-slate-200 shadow-sm">
    Carregando dados da pesquisa...
  </div>
</div>

<script>
  function esc(s) {
    if (!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
  }

  document.addEventListener('DOMContentLoaded', () => {
    carregarRelatorioSatisfacao();
  });

  async function carregarRelatorioSatisfacao() {
    const container = document.getElementById('satisfacao-relatorio-container');
    try {
      const data = await apiFetch(BASE + '/api/admin/satisfacao/relatorio');
      
      if (!data || !data.length) {
        container.innerHTML = `
          <div class="col-span-full text-center py-12 text-slate-400 bg-white rounded-xl border border-slate-200 shadow-sm">
            Nenhuma resposta registrada até o momento.
          </div>
        `;
        return;
      }

      // Calcula média global de satisfação
      let somaMedias = 0;
      data.forEach(item => somaMedias += parseFloat(item.media));
      const mediaGlobal = (somaMedias / data.length).toFixed(1);

      let cardsHtml = `
        <div class="col-span-full bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
          <div>
            <h3 class="font-bold text-slate-800 text-base">Satisfação Global</h3>
            <p class="text-xs text-slate-400 mt-0.5">Média geral ponderada obtida de todas as avaliações respondidas.</p>
          </div>
          <div class="flex items-center gap-4 bg-slate-50 border border-slate-100 px-5 py-3.5 rounded-2xl">
            <span class="text-3xl font-extrabold text-slate-800">${mediaGlobal}</span>
            <div class="flex flex-col">
              <div class="flex text-red-500 gap-0.5">
                ${gerarEstrelasHtml(mediaGlobal)}
              </div>
              <span class="text-[10px] font-bold text-slate-400 mt-0.5 uppercase tracking-wide">Média ActShare</span>
            </div>
          </div>
        </div>
      `;

      cardsHtml += data.map(item => {
        const percentualLargura = (parseFloat(item.media) / 5) * 100;
        
        return `
          <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex justify-between items-start gap-4">
              <p class="font-bold text-slate-700 text-xs sm:text-sm leading-relaxed">${esc(item.texto)}</p>
              <span class="text-xs font-bold text-slate-400 bg-slate-100 rounded-full px-2 py-0.5 whitespace-nowrap">${item.total_respostas} voto(s)</span>
            </div>
            
            <div class="flex items-center justify-between gap-4">
              <!-- Visualizador de Corações -->
              <div class="flex text-red-500 gap-1">
                ${gerarEstrelasHtml(item.media)}
              </div>
              <span class="text-lg font-extrabold text-slate-800">${parseFloat(item.media).toFixed(1)}</span>
            </div>

            <!-- Barra de Progresso visual -->
            <div class="relative w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
              <div class="absolute top-0 left-0 bottom-0 bg-secondary rounded-full" style="width: ${percentualLargura}%"></div>
            </div>
          </div>
        `;
      }).join('');

      container.innerHTML = cardsHtml;
    } catch (e) {
      container.innerHTML = `
        <div class="col-span-full text-center py-12 text-red-500 bg-white rounded-xl border border-slate-200 shadow-sm">
          Falha ao carregar relatório: ${e.message}
        </div>
      `;
    }
  }

  function gerarEstrelasHtml(media) {
    const valor = parseFloat(media);
    let html = '';
    for (let i = 1; i <= 5; i++) {
      if (valor >= i) {
        // Cheia (coração vermelho)
        html += '<span class="text-base">♥</span>';
      } else if (valor > i - 1) {
        // Metade ou aproximada (vamos pintar de vermelho também)
        html += '<span class="text-base text-red-400 opacity-60">♥</span>';
      } else {
        // Vazia
        html += '<span class="text-base text-slate-200">♥</span>';
      }
    }
    return html;
  }
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
