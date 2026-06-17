<?php $pageTitle = 'Editar Curso — ActShare'; ?>
<?php require __DIR__ . '/../layout/admin-header.php'; ?>

<div class="mb-6">
  <a href="<?= BASE_PATH ?>/admin/cursos" class="text-sm text-primary hover:underline">← Voltar aos cursos</a>
</div>

<div id="curso-admin-loading" class="text-gray-400 py-12 text-center">Carregando...</div>

<div id="curso-admin-content" class="hidden space-y-8">
  <!-- Info do curso -->
  <div class="bg-white rounded-xl border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 id="ca-titulo" class="text-xl font-bold text-gray-800"></h1>
      <div class="flex gap-2">
        <button onclick="editarCursoInfo()" class="text-sm border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-50">Editar</button>
        <button onclick="excluirCurso()" class="text-sm bg-red-50 border border-red-200 text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-100">Excluir</button>
      </div>
    </div>
    <p id="ca-descricao" class="text-gray-500 text-sm"></p>
  </div>

  <!-- Módulos e aulas -->
  <div class="bg-white rounded-xl border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-5">
      <h2 class="font-semibold text-gray-700">Módulos</h2>
      <button onclick="abrirModalModulo()" class="text-sm bg-primary text-white px-3 py-1.5 rounded-lg hover:bg-blue-900">+ Módulo</button>
    </div>
    <div id="modulos-admin-list" class="space-y-4"></div>
  </div>
</div>

<!-- Modal módulo -->
<div id="modal-modulo" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-md p-6">
    <h2 id="mod-modal-titulo" class="text-lg font-bold text-gray-800 mb-4">Novo Módulo</h2>
    <form id="form-modulo" class="space-y-4">
      <input type="hidden" id="mod-id">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
        <input type="text" id="mod-titulo" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
        <input type="number" id="mod-ordem" value="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div class="flex gap-3">
        <button type="submit" class="flex-1 bg-primary text-white font-medium py-2.5 rounded-lg">Salvar</button>
        <button type="button" onclick="fecharModalModulo()" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal aula -->
<div id="modal-aula" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
    <h2 id="aula-modal-titulo" class="text-lg font-bold text-gray-800 mb-4">Nova Aula</h2>
    <form id="form-aula" class="space-y-4">
      <input type="hidden" id="aula-id">
      <input type="hidden" id="aula-modulo-id">
      
      <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Título *</label>
        <input type="text" id="aula-titulo" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tipo de Conteúdo</label>
          <select id="aula-tipo" onchange="toggleCamposTipoAula()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="video">Vídeo Aula</option>
            <option value="texto">Texto / Leitura</option>
            <option value="pdf">Material PDF</option>
            <option value="quiz">Quizz / Teste</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase mb-1">É Exame Final?</label>
          <select id="aula-e-prova" onchange="toggleCamposTipoAula()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="0">Não</option>
            <option value="1">Sim</option>
          </select>
        </div>
      </div>

      <div id="campo-url-bloco">
        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">URL do Vídeo / Link Documento</label>
        <input type="url" id="aula-url" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>

      <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Descrição / Instruções</label>
        <textarea id="aula-descricao" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ordem</label>
          <input type="number" id="aula-ordem" value="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Duração (min)</label>
          <input type="number" id="aula-duracao" value="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
      </div>

      <!-- Configurações de Quizz e Avaliação -->
      <div id="bloco-quiz-config" class="hidden bg-slate-50 border border-slate-200 rounded-lg p-3 space-y-3">
        <h4 class="font-bold text-xs text-slate-700">Parâmetros do Questionário</h4>
        
        <div>
          <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Perguntas a sortear no pool</label>
          <input type="number" id="aula-quizz-qtd-perguntas" min="1" value="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>

        <div id="bloco-prova-exclusivo" class="hidden space-y-3">
          <label class="flex items-center gap-1.5 text-xs text-gray-700 font-semibold mt-1">
            <input type="checkbox" id="aula-exemplar-global" class="rounded accent-primary"> Exemplar Global
          </label>
          
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Nota de Corte</label>
              <select id="aula-nota-corte-tipo" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs bg-white">
                <option value="percentual">Percentual (%)</option>
                <option value="questoes">Qtd Questões</option>
              </select>
            </div>
            <div>
              <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Valor Nota</label>
              <input type="number" id="aula-nota-corte-valor" min="1" value="70" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs">
            </div>
          </div>

          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Tempo Limite (min)</label>
              <input type="number" id="aula-tempo-limite" min="0" value="0" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs" title="0 para ilimitado">
            </div>
            <div class="flex items-center">
              <label class="flex items-center gap-1.5 text-[11px] text-gray-700 font-semibold mt-4">
                <input type="checkbox" id="aula-bloquear-proctoring" class="rounded accent-primary"> Proctoring (Anti-cheat)
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="flex-1 bg-primary text-white font-medium py-2.5 rounded-lg">Salvar</button>
        <button type="button" onclick="fecharModalAula()" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar Informações do Curso -->
<div id="modal-editar-curso" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
    <h2 class="text-lg font-bold text-gray-800 mb-5">Editar Informações do Curso</h2>
    <form id="form-editar-curso" class="space-y-4">
      
      <div class="grid grid-cols-3 gap-3">
        <div class="col-span-2">
          <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Título do Curso (Loja) *</label>
          <input type="text" id="ec-titulo" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Código *</label>
          <input type="text" id="ec-codigo" required maxlength="10" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary uppercase">
        </div>
      </div>

      <div class="grid grid-cols-3 gap-3">
        <div class="col-span-2">
          <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nome para o Certificado</label>
          <input type="text" id="ec-nome-certificado" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Prazo (Dias)</label>
          <input type="number" id="ec-prazo-acesso" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Descrição</label>
        <textarea id="ec-descricao" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Carga Horária (h)</label>
          <input type="number" id="ec-carga" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Preço (R$)</label>
          <input type="number" id="ec-preco" min="0" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Categoria</label>
          <select id="ec-categoria" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Sem categoria</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Instrutor</label>
          <select id="ec-instrutor" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Sem instrutor</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">URL da Imagem de Capa (Thumbnail)</label>
        <input type="url" id="ec-thumb" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
      </div>

      <div class="grid grid-cols-2 gap-3 bg-slate-50 border border-slate-100 p-3 rounded-lg">
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-750">
          <input type="checkbox" id="ec-ativo" class="rounded accent-primary"> Ativo
        </label>
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-750">
          <input type="checkbox" id="ec-publico" class="rounded accent-primary"> Público (Vendas)
        </label>
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-750">
          <input type="checkbox" id="ec-disponivel-loja" class="rounded accent-primary"> Disponível na Loja
        </label>
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-750">
          <input type="checkbox" id="ec-exibir-instrutor" class="rounded accent-primary"> Exibir Instrutor
        </label>
      </div>

      <div id="ec-form-erro" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-2"></div>
      
      <div class="flex gap-3 pt-2">
        <button type="submit" id="btn-salvar-ec" class="flex-1 bg-primary text-white font-medium py-2.5 rounded-lg">Salvar Alterações</button>
        <button type="button" onclick="fecharModalEditarCurso()" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script src="<?= BASE_PATH ?>/assets/js/admin.js?v=4"></script>
<script>
  const cursoAdminId = <?= (int)($_GET['id'] ?? 0) ?>;
  document.addEventListener('DOMContentLoaded', () => carregarCursoAdmin(cursoAdminId));
  
  function toggleCamposTipoAula() {
    const tipo = document.getElementById('aula-tipo').value;
    const eProva = document.getElementById('aula-e-prova').value === '1';
    const blocoConfig = document.getElementById('bloco-quiz-config');
    const blocoProva = document.getElementById('bloco-prova-exclusivo');
    const blocoUrl = document.getElementById('campo-url-bloco');

    if (tipo === 'quiz' || eProva) {
      blocoConfig.classList.remove('hidden');
      if (eProva) {
        blocoProva.classList.remove('hidden');
        document.getElementById('aula-tipo').value = 'quiz'; // Força tipo quiz se for prova
      } else {
        blocoProva.classList.add('hidden');
      }
      blocoUrl.classList.add('hidden');
    } else {
      blocoConfig.classList.add('hidden');
      blocoUrl.classList.remove('hidden');
    }
  }
</script>

    </div></main></div></body></html>
