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

    // categorias
    ['GET',    'categorias',           __DIR__ . '/categorias/index.php'],
    ['POST',   'categorias',           __DIR__ . '/categorias/index.php'],
    ['DELETE', 'categorias/:id',       __DIR__ . '/categorias/item.php'],

    // instrutores
    ['GET',    'instrutores',          __DIR__ . '/instrutores/index.php'],
    ['POST',   'instrutores',          __DIR__ . '/instrutores/index.php'],

    // modulos
    ['POST',   'modulos',              __DIR__ . '/modulos/index.php'],
    ['PUT',    'modulos/:id',          __DIR__ . '/modulos/item.php'],
    ['DELETE', 'modulos/:id',          __DIR__ . '/modulos/item.php'],

    // aulas
    ['POST',   'aulas',                __DIR__ . '/aulas/index.php'],
    ['PUT',    'aulas/:id',            __DIR__ . '/aulas/item.php'],
    ['DELETE', 'aulas/:id',            __DIR__ . '/aulas/item.php'],

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
    ['GET',    'master/check-role',    __DIR__ . '/master/check-role.php'],
    ['GET',    'master/cursos',        __DIR__ . '/master/cursos.php'],
    ['GET',    'master/cursos/:id',    __DIR__ . '/master/curso-detalhe.php'],
    ['GET',    'master/cursos/:id/participantes', __DIR__ . '/master/participantes.php'],
    ['POST',   'master/cursos/:id/participante',  __DIR__ . '/master/participantes.php'],
    ['DELETE', 'master/cursos/:id/participante/:aluno_id', __DIR__ . '/master/participantes.php'],
    ['POST',   'master/cursos/:id/participar', __DIR__ . '/master/participar.php'],
    // checkout
    ['POST',   'checkout/validar-cupom',     __DIR__ . '/checkout/validar-cupom.php'],
    ['POST',   'checkout/criar-pedido',      __DIR__ . '/checkout/criar-pedido.php'],
    ['POST',   'checkout/simular-pagamento', __DIR__ . '/checkout/simular-pagamento.php'],

    // admin
    ['GET',    'admin/usuarios',       __DIR__ . '/admin/usuarios.php'],
    ['PATCH',  'admin/usuarios',       __DIR__ . '/admin/usuarios.php'],

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
