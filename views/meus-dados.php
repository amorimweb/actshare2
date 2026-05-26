<?php
$pageTitle = 'Meus Dados — ActShare';
require __DIR__ . '/layout/header.php';
?>

<div class="max-w-xl mx-auto px-4 py-12">
  <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-8">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Meus Dados</h1>
      <p class="text-sm text-gray-500 mt-1">Gerencie suas informações pessoais cadastrais.</p>
    </div>

    <!-- Mensagens de Alerta -->
    <div id="alert-msg" class="hidden mb-6 rounded-xl p-4 border text-sm"></div>

    <!-- Form de Dados -->
    <form id="form-perfil" onsubmit="salvarPerfil(event)" class="space-y-5">
      <div>
        <label for="input-nome" class="block text-sm font-medium text-gray-700 mb-1.5">Nome Completo</label>
        <input type="text" id="input-nome" required
          class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none transition-shadow"
          placeholder="Seu nome completo">
      </div>

      <div>
        <label for="input-email" class="block text-sm font-medium text-gray-700 mb-1.5">Endereço de E-mail</label>
        <input type="email" id="input-email" required
          class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none transition-shadow"
          placeholder="seu.email@exemplo.com">
      </div>

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

  async function carregarPerfil() {
    const form = document.getElementById('form-perfil');
    const alertEl = document.getElementById('alert-msg');
    
    // Desabilitar form enquanto carrega
    const inputs = form.querySelectorAll('input, button');
    inputs.forEach(i => i.disabled = true);

    try {
      const data = await apiFetch(BASE + '/api/aluno/perfil');
      document.getElementById('input-nome').value = data.nome || '';
      document.getElementById('input-email').value = data.email || '';
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

    const nome = document.getElementById('input-nome').value.trim();
    const email = document.getElementById('input-email').value.trim();

    try {
      const res = await apiPost(BASE + '/api/aluno/perfil', { nome, email });
      
      // Atualiza o local storage
      authSetUser(res.user);
      
      // Atualiza o header dinamicamente
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
