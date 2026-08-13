<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/**
 * phpunit.xml forca DB_CONNECTION=sqlite/DB_DATABASE=:memory: pra TODAS as
 * conexoes (config/database.php usa a mesma env('DB_DATABASE') pra sqlite e
 * mysql). Testes que precisam do banco mysql real de dev (empresa/usuario
 * seedados, sem recriar tudo via RefreshDatabase) chamam isso no inicio -
 * le o .env direto do disco e sobrescreve a conexao mysql em runtime.
 */
function usarMysqlRealDeDev(): void
{
    $env = [];
    foreach (file(base_path('.env')) as $linha) {
        if (preg_match('/^(DB_HOST|DB_PORT|DB_DATABASE|DB_USERNAME|DB_PASSWORD)=(.*)$/', trim($linha), $m)) {
            $env[$m[1]] = trim($m[2], " \t\n\r\0\x0B\"");
        }
    }

    config([
        'database.default' => 'mysql',
        'database.connections.mysql.host' => $env['DB_HOST'] ?? '127.0.0.1',
        'database.connections.mysql.port' => $env['DB_PORT'] ?? '3306',
        'database.connections.mysql.database' => $env['DB_DATABASE'],
        'database.connections.mysql.username' => $env['DB_USERNAME'],
        'database.connections.mysql.password' => $env['DB_PASSWORD'] ?? '',
    ]);

    \Illuminate\Support\Facades\DB::purge('mysql');
}
