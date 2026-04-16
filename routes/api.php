<?php
/** @var Dorguzen\Core\DGZ_Router $router */

// ─── Authentication ───────────────────────────────────────────────────────────
$router->apiPost('/api/v1/auth/register', 'AuthApi@register', 'v1');
$router->apiPost('/api/v1/auth/login',    'AuthApi@login',    'v1');
$router->apiPost('/api/v1/auth/refresh',  'AuthApi@refresh',  'v1');

// ─── API Docs (Swagger UI) — set API_DOCS_ENABLED=true in .env to activate ───
$router->apiGet('/api/v1/docs',      'Docs@index', 'v1');
$router->apiGet('/api/v1/docs/spec', 'Docs@spec',  'v1');

