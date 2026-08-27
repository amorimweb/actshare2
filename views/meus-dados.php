<?php
$pageTitle = 'Meus Dados — ActShare';
require __DIR__ . '/layout/header.php';
?>

<div class="max-w-2xl mx-auto px-4 py-12">
  <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-8">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Meus Dados</h1>
      <p class="text-sm text-gray-500 mt-1">Gerencie suas informações pessoais e cadastrais.</p>
    </div>

    <!-- Mensagens de Alerta -->
    <div id="alert-msg" class="hidden mb-6 rounded-xl p-4 border text-sm"></div>

    <!-- Form de Dados -->
    <form id="form-perfil" onsubmit="salvarPerfil(event)" class="space-y-6">
      <section class="space-y-4">
        <h2 class="text-sm font-bold uppercase tracking-wide text-gray-400">Dados de Acesso</h2>
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label for="input-nome" class="block text-sm font-medium text-gray-700 mb-1.5">Nome Completo *</label>
            <input type="text" id="input-nome" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none">
          </div>
          <div>
            <label for="input-email" class="block text-sm font-medium text-gray-700 mb-1.5">Endereço de E-mail *</label>
            <input type="email" id="input-email" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none">
          </div>
        </div>
      </section>

      <section class="space-y-4 pt-2 border-t border-gray-100">
        <h2 class="text-sm font-bold uppercase tracking-wide text-gray-400 pt-4">Dados Fiscais e de Contato</h2>
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">CPF ou CNPJ</label>
            <input type="text" id="input-documento" placeholder="000.000.000-00" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">WhatsApp / Celular</label>
            <input type="text" id="input-telefone" placeholder="(11) 99999-9999" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de pessoa</label>
            <select id="input-tipo-pessoa" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none">
              <option value="">Não informado</option>
              <option value="fisica">Pessoa física</option>
              <option value="juridica">Pessoa jurídica</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Data de nascimento</label>
            <input type="date" id="input-data-nascimento" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none">
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Razão social</label>
            <input type="text" id="input-razao-social" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Inscrição estadual</label>
            <input type="text" id="input-inscricao-estadual" placeholder="Isento, se aplicável" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">CEP</label>
            <input type="text" id="input-cep" placeholder="00000-000" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none">
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Endereço</label>
            <input type="text" id="input-endereco" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Número</label>
            <input type="text" id="input-numero" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Complemento</label>
            <input type="text" id="input-complemento" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Bairro</label>
            <input type="text" id="input-bairro" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Cidade</label>
            <input type="text" id="input-cidade" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado</label>
            <input type="text" id="input-estado" maxlength="2" placeholder="SP" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm uppercase focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none">
          </div>
        </div>
      </section>

      <div class="pt-2 flex flex-col sm:flex-row gap-3">
        <button type="submit" id="btn-salvar"
          class="flex-1 bg-primary text-white font-semibold py-3 px-6 rounded-xl hover:bg-blue-900 transition-colors shadow-sm disabled:opacity-60">
          Salvar Alterações
        </button>
        <a href="<?= BASE_PATH ?>/painel"
          class="flex-1 text-center bg-gray-50 border border-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl hover:bg-gray-100 transition-colors">
          Cancelar
        </a>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const user = authGetUser();
    if (!user) { window.location.href = BASE + '/login'; return; }
    carregarPerfil();
  });

  const CAMPOS_PERFIL = ['documento','telefone','tipo_pessoa','data_nascimento','razao_social','inscricao_estadual','cep','endereco','numero','complemento','bairro','cidade','estado'];

  async function carregarPerfil() {
    const form = document.getElementById('form-perfil');
    const alertEl = document.getElementById('alert-msg');

    const inputs = form.querySelectorAll('input, select, button');
    inputs.forEach(i => i.disabled = true);

    try {
      const data = await apiFetch(BASE + '/api/aluno/perfil');
      document.getElementById('input-nome').value = data.nome || '';
      document.getElementById('input-email').value = data.email || '';
      CAMPOS_PERFIL.forEach(campo => {
        const el = document.getElementById('input-' + campo.replace(/_/g, '-'));
        if (el) el.value = data[campo] || '';
      });
    } catch (err) {
      alertEl.textContent = err.message || 'Erro ao carregar dados do perfil.';
      alertEl.className = 'mb-6 rounded-xl p-4 border text-sm bg-red-50 border-red-200 text-red-700 block';
    } finally {
      inputs.forEach(i => i.disabled = false);
    }
  }

  async function salvarPerfil(event) {
    event.preventDefault();
    const btn = document.getElementById('btn-salvar');
    const alertEl = document.getElementById('alert-msg');

    alertEl.classList.add('hidden');
    btn.disabled = true;
    btn.textContent = 'Salvando...';

    const body = {
      nome: document.getElementById('input-nome').value.trim(),
      email: document.getElementById('input-email').value.trim(),
    };
    CAMPOS_PERFIL.forEach(campo => {
      const el = document.getElementById('input-' + campo.replace(/_/g, '-'));
      if (el) body[campo] = el.value.trim();
    });

    try {
      const res = await apiPost(BASE + '/api/aluno/perfil', body);

      authSetUser(res.user);
      updateHeaderAuth(res.user);

      alertEl.textContent = 'Dados atualizados com sucesso!';
      alertEl.className = 'mb-6 rounded-xl p-4 border text-sm bg-green-50 border-green-200 text-green-700 block';
    } catch (err) {
      alertEl.textContent = err.message || 'Erro ao salvar alterações.';
      alertEl.className = 'mb-6 rounded-xl p-4 border text-sm bg-red-50 border-red-200 text-red-700 block';
    } finally {
      btn.disabled = false;
      btn.textContent = 'Salvar Alterações';
    }
  }
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
