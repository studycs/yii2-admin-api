<?php
use Symfony\Component\Dotenv\Dotenv;
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');
function value($value, ...$args){
    return $value instanceof Closure ? $value(...$args) : $value;
}

function env($key, $default = null)
{
    $value = $_ENV[$key] ?? '';
    if ($value === false) return value($default);
    switch (strtolower($value)) {
        case 'true':
        case '(true)': return true;
        case 'false':
        case '(false)': return false;
        case 'empty':
        case '(empty)': return '';
        case 'null':
        case '(null)': return null;
    }
    if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
        return substr($value, 1, -1);
    }
    return $value;
}

define('YII_ENV',env('YII_ENV'));
define('YII_DEBUG',env('YII_DEBUG'));

define('DB_DSN',env('DB_DSN'));
define('DB_DATABASE',env('DB_DATABASE'));
define('DB_USERNAME',env('DB_USERNAME'));
define('DB_PASSWORD',env('DB_PASSWORD'));
define('ADMIN_EMAIL',env('ADMIN_EMAIL'));