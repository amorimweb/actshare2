<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/response.php';
require_once __DIR__ . '/../includes/cors.php';

$route  = trim($_GET['_route'] ?? '', '/');
$method = $_SERVER['REQUEST_METHOD'];

// Helper: extrai segmentos e mapeia :id
function matchRoute(string $pattern, string $route): array|false {
    $patternParts = explode('/', trim($pattern, '/'));
    $routeParts   = explode('/', trim($route, '/'));
    if (count($patternParts) !== count($routeParts)) return false;

    $params = [];
    foreach ($patternParts as $i => $seg) {
        if (str_starts_with($seg, ':')) {
            $params[ltrim($seg, ':')] = $routeParts[$i];
        } elseif ($seg !== $routeParts[$i]) {
            return false;
        }
    }
    return $params;
}

// Tabela de rotas: [método|*, padrão, arquivo]
$routes = [
    // auth
    ['POST',   'auth/login',           __DIR__ . '/auth/login.php'],
    ['POST',   'auth/register',        __DIR__ . '/auth/register.php'],
    ['POST',   'auth/logout',          __DIR__ . '/auth/logout.php'],
    ['GET',    'auth/me',              __DIR__ . '/auth/me.php'],
    ['POST',   'auth/reset-password',  __DIR__ . '/auth/reset-password.php'],
    ['POST',   'auth/confirm-reset',   __DIR__ . '/auth/confirm-reset.php'],

    // cursos
    ['GET',    'cursos',               __DIR__ . '/cursos/index.php'],
    ['POST',   'cursos',               __DIR__ . '/cursos/index.php'],
    ['GET',    'cursos/:id',           __DIR__ . '/cursos/item.php'],
    ['PUT',    'cursos/:id',           __DIR__ . '/cursos/item.php'],
    ['DELETE', 'cursos/:id',           __DIR__ . '/cursos/item.php'],
    ['GET',    'admin/cursos/:id/exames', __DIR__ . '/admin/cursos_exames.php'],
    ['PUT',    'admin/cursos/:id/exames', __DIR__ . '/admin/cursos_exames.php'],

    // categorias
    ['GET',    'categorias',           __DIR__ . '/categorias/index.php'],
    ['POST',   'categorias',           __DIR__ . '/categorias/index.php'],
    ['PUT',    'categorias/:id',       __DIR__ . '/categorias/item.php'],
    ['DELETE', 'categorias/:id',       __DIR__ . '/categorias/item.php'],

    // instrutores
    ['GET',    'instrutores',          __DIR__ . '/instrutores/index.php'],
    ['POST',   'instrutores',          __DIR__ . '/instrutores/index.php'],
    ['GET',    'instrutores/:id',      __DIR__ . '/instrutores/item.php'],
    ['PUT',    'instrutores/:id',      __DIR__ . '/instrutores/item.php'],
    ['DELETE', 'instrutores/:id',      __DIR__ . '/instrutores/item.php'],

    // modulos
    ['POST',   'modulos',              __DIR__ . '/modulos/index.php'],
    ['PUT',    'modulos/:id',          __DIR__ . '/modulos/item.php'],
    ['DELETE', 'modulos/:id',          __DIR__ . '/modulos/item.php'],

    // combos
    ['GET',    'combos',               __DIR__ . '/combos/index.php'],
    ['POST',   'combos',               __DIR__ . '/combos/index.php'],
    ['GET',    'combos/:id',           __DIR__ . '/combos/item.php'],
    ['PUT',    'combos/:id',           __DIR__ . '/combos/item.php'],
    ['DELETE', 'combos/:id',           __DIR__ . '/combos/item.php'],

    // aulas
    ['POST',   'aulas',                __DIR__ . '/aulas/index.php'],
    ['PUT',    'aulas/:id',            __DIR__ . '/aulas/item.php'],
    ['DELETE', 'aulas/:id',            __DIR__ . '/aulas/item.php'],
    ['GET',    'aulas/:id/materiais',  __DIR__ . '/aulas/materiais.php'],
    ['POST',   'aulas/:id/materiais',  __DIR__ . '/aulas/materiais.php'],
    ['DELETE', 'aulas/materiais/:id',  __DIR__ . '/aulas/materiais_item.php'],
    ['GET',    'aulas/materiais/:id/download', __DIR__ . '/aulas/materiais_download.php'],

    // aluno
    ['POST',   'aluno/matricular',     __DIR__ . '/aluno/matricular.php'],
    ['GET',    'aluno/matriculas',     __DIR__ . '/aluno/matriculas.php'],
    ['GET',    'aluno/curso/:id',      __DIR__ . '/aluno/curso.php'],
    ['POST',   'aluno/progresso',      __DIR__ . '/aluno/progresso.php'],
    ['GET',    'aluno/quiz/:id',       __DIR__ . '/aluno/quiz/index.php'],
    ['POST',   'aluno/quiz/responder', __DIR__ . '/aluno/quiz/responder.php'],
    ['POST',   'aluno/quiz/proctoring', __DIR__ . '/aluno/quiz/proctoring.php'],
    ['GET',    'aluno/perfil',         __DIR__ . '/aluno/perfil.php'],
    ['POST',   'aluno/perfil',         __DIR__ . '/aluno/perfil.php'],
    ['POST',   'aluno/alterar-senha',  __DIR__ . '/aluno/alterar-senha.php'],

    // master
    ['GET',    'master/alunos',        __DIR__ . '/master/alunos.php'],
    ['POST',   'master/aluno',         __DIR__ . '/master/aluno.php'],
    ['DELETE', 'master/aluno/:id',     __DIR__ . '/master/aluno.php'],
    ['GET',    'master/check-role',    __DIR__ . '/master/check-role.php'],
    ['GET',    'master/cursos',        __DIR__ . '/master/cursos.php'],
    ['GET',    'master/cursos/:id',    __DIR__ . '/master/curso-detalhe.php'],
    ['GET',    'master/cursos/:id/participantes', __DIR__ . '/master/participantes.php'],
    ['POST',   'master/cursos/:id/participante',  __DIR__ . '/master/participantes.php'],
    ['DELETE', 'master/cursos/:id/participante/:aluno_id', __DIR__ . '/master/participantes.php'],
    ['POST',   'master/cursos/:id/participar', __DIR__ . '/master/participar.php'],
    ['POST',   'master/cursos/:id/informar-alunos', __DIR__ . '/master/informar-alunos.php'],
    ['POST',   'master/salvar-certificado-acesso', __DIR__ . '/master/salvar-certificado-acesso.php'],
    ['GET',    'master/relatorio-curso/:id',    __DIR__ . '/master/relatorio-curso.php'],
    ['GET',    'master/alunos/:aluno_id/cursos/:curso_id/avaliacoes', __DIR__ . '/master/avaliacoes.php'],
    // checkout
    ['POST',   'checkout/validar-cupom',     __DIR__ . '/checkout/validar-cupom.php'],
    ['POST',   'checkout/criar-pedido',      __DIR__ . '/checkout/criar-pedido.php'],
    ['POST',   'checkout/simular-pagamento', __DIR__ . '/checkout/simular-pagamento.php'],
    ['POST',   'checkout/asaas-webhook',     __DIR__ . '/checkout/asaas-webhook.php'],

    // admin
    ['GET',    'admin/usuarios',       __DIR__ . '/admin/usuarios.php'],
    ['PUT',    'admin/usuarios',       __DIR__ . '/admin/usuarios.php'],
    ['PATCH',  'admin/usuarios',       __DIR__ . '/admin/usuarios.php'],
    ['GET',    'admin/pedidos',        __DIR__ . '/admin/pedidos.php'],
    ['GET',    'admin/pedidos/:id',    __DIR__ . '/admin/pedidos_item.php'],
    ['GET',    'admin/clientes',       __DIR__ . '/admin/clientes.php'],
    ['GET',    'admin/clientes/:id',   __DIR__ . '/admin/clientes_item.php'],
    ['PUT',    'admin/clientes/:id',   __DIR__ . '/admin/clientes_item.php'],
    ['GET',    'admin/alunos',         __DIR__ . '/admin/alunos.php'],
    ['PUT',    'admin/alunos/:id',     __DIR__ . '/admin/alunos_item.php'],
    ['GET',    'admin/configuracoes',  __DIR__ . '/admin/configuracoes.php'],
    ['PUT',    'admin/configuracoes',  __DIR__ . '/admin/configuracoes.php'],
    ['GET',    'admin/cupons',         __DIR__ . '/admin/cupons.php'],
    ['POST',   'admin/cupons',         __DIR__ . '/admin/cupons.php'],
    ['PUT',    'admin/cupons/:id',     __DIR__ . '/admin/cupons_item.php'],
    ['DELETE', 'admin/cupons/:id',     __DIR__ . '/admin/cupons_item.php'],
    ['GET',    'admin/perguntas',      __DIR__ . '/admin/perguntas.php'],
    ['POST',   'admin/perguntas',      __DIR__ . '/admin/perguntas.php'],
    ['PUT',    'admin/perguntas/:id',  __DIR__ . '/admin/perguntas_item.php'],
    ['DELETE', 'admin/perguntas/:id',  __DIR__ . '/admin/perguntas_item.php'],
    ['GET',    'admin/satisfacao/relatorio', __DIR__ . '/admin/satisfacao.php'],
    ['POST',   'admin/satisfacao/perguntas', __DIR__ . '/admin/satisfacao.php'],
    ['PUT',    'admin/satisfacao/perguntas/:id', __DIR__ . '/admin/satisfacao_item.php'],
    ['DELETE', 'admin/satisfacao/perguntas/:id', __DIR__ . '/admin/satisfacao_item.php'],
    ['GET',    'admin/certificados',   __DIR__ . '/admin/certificados.php'],
    ['POST',   'admin/certificados',   __DIR__ . '/admin/certificados.php'],

    // aluno satisfacao / certificados
    ['GET',    'aluno/satisfacao/status/:matricula_id', __DIR__ . '/aluno/satisfacao.php'],
    ['POST',   'aluno/satisfacao/responder',           __DIR__ . '/aluno/satisfacao.php'],
    ['GET',    'certificados/validar/:codigo',         __DIR__ . '/certificados/validar.php'],

    // debug
    ['GET',    'debug/db',             __DIR__ . '/debug/db.php'],
];

foreach ($routes as [$routeMethod, $pattern, $file]) {
    $params = matchRoute($pattern, $route);
    if ($params === false) continue;
    if ($routeMethod !== '*' && $routeMethod !== $method) continue;

    // Expõe parâmetros de rota como $_ROUTE
    $GLOBALS['_ROUTE'] = $params;

    if (!file_exists($file)) jsonError("Endpoint não implementado: $pattern", 501);
    require $file;
    exit;
}

jsonError('Rota não encontrada.', 404);
