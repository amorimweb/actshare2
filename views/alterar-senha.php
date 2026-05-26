<?php
$pageTitle = 'Alterar Senha — ActShare';
require __DIR__ . '/layout/header.php';
?>

<div class="max-w-xl mx-auto px-4 py-12">
  <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-8">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Alterar Senha</h1>
      <p class="text-sm text-gray-500 mt-1">Crie uma nova senha de acesso segura para sua conta.</p>
    </div>

    <!-- Mensagens de Alerta -->
    <div id="alert-msg" class="hidden mb-6 rounded-xl p-4 border text-sm"></div>

    <!-- Form de Alteração de Senha -->
    <form id="form-senha" onsubmit="alterarSenha(event)" class="space-y-5">
      <div>
        <label for="input-senha-atual" class="block text-sm font-medium text-gray-700 mb-1.5">Senha Atual</label>
        <input type="password" id="input-senha-atual" required minlength="6"
          class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none transition-shadow"
          placeholder="Digite sua senha atual">
      </div>

      <div>
        <label for="input-nova-senha" class="block text-sm font-medium text-gray-700 mb-1.5">Nova Senha</label>
        <input type="password" id="input-nova-senha" required minlength="6"
          class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none transition-shadow"
          placeholder="Mínimo de 6 caracteres">
      </div>

      <div>
        <label for="input-confirmar" class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar Nova Senha</label>
        <input type="password" id="input-confirmar" required minlength="6"
          class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none transition-shadow"
          placeholder="Digite a nova senha novamente">
      </div>

      <div class="pt-2 flex flex-col sm:flex-row gap-3">
        <button type="submit" id="btn-alterar"
          class="flex-1 bg-primary text-white font-semibold py-3 px-6 rounded-xl hover:bg-blue-900 transition-colors shadow-sm disabled:opacity-60">
          Alterar Senha
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
  });

  async function alterarSenha(event) {
    event.preventDefault();
    const btn = document.getElementById('btn-alterar');
    const alertEl = document.getElementById('alert-msg');
    
    alertEl.classList.add('hidden');
    
    const senha_atual = document.getElementById('input-senha-atual').value;
    const nova_senha  = document.getElementById('input-nova-senha').value;
    const confirmar_senha = document.getElementById('input-confirmar').value;

    if (nova_senha !== confirmar_senha) {
      alertEl.textContent = 'A nova senha e a confirmação não coincidem.';
      alertEl.className = 'mb-6 rounded-xl p-4 border text-sm bg-red-50 border-red-200 text-red-700 block';
      return;
    }

    if (nova_senha.length < 6) {
      alertEl.textContent = 'A nova senha deve possuir pelo menos 6 caracteres.';
      alertEl.className = 'mb-6 rounded-xl p-4 border text-sm bg-red-50 border-red-200 text-red-700 block';
      return;
    }

    btn.disabled = true;
    btn.textContent = 'Alterando...';

    try {
      const res = await apiPost(BASE + '/api/aluno/alterar-senha', {
        senha_atual,
        nova_senha,
        confirmar_senha
      });
      
      alertEl.textContent = res.message || 'Senha alterada com sucesso!';
      alertEl.className = 'mb-6 rounded-xl p-4 border text-sm bg-green-50 border-green-200 text-green-700 block';
      
      // Limpa formulário
      document.getElementById('form-senha').reset();
    } catch (err) {
      alertEl.textContent = err.message || 'Erro ao alterar a senha.';
      alertEl.className = 'mb-6 rounded-xl p-4 border text-sm bg-red-50 border-red-200 text-red-700 block';
    } finally {
      btn.disabled = false;
      btn.textContent = 'Alterar Senha';
    }
  }
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
