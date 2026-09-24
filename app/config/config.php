<?php

/**
 * Configurações gerais do sistema
 */

date_default_timezone_set('Africa/Luanda');

// Ambiente
define('APP_NAME', 'Bxpert');
define('APP_ENV', 'development');

// URL do sistema
define('BASE_URL', 'http://localhost/projects/bxpert/fatura-responsive');

// Banco de dados
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'u523793545_sistema_fatura');
define('DB_USER', 'root');
define('DB_PASS', '');

// Charset
define('DB_CHARSET', 'utf8mb4');

// AGT - Consulta de NIF (consultarNIF v5)
// URL de homologação; para produção, troque pelo URL de produção da AGT.
define('AGT_NIF_URL', 'https://sifp.minfin.gov.ao/sigt/contribuinte/consultarNIF/v5/obter');
define('AGT_NIF_USERNAME', 'ws.bx'); // Username do utilizador que invoca o serviço
define('AGT_NIF_PASSWORD', 'mfn374622026'); // Token de acesso ao serviço