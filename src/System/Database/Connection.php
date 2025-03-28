<?php
declare(strict_types=1);

namespace App\System\Database;

use KnorkFork\LoadEnvironment\Environment;
use PDO;

final class Connection
{
    private static ?Connection $instance = null;

    public readonly PDO $pdo;

    public function __construct()
    {
        $host = Environment::getStringEnv('DB_HOST');
        $port = Environment::getStringEnv('DB_PORT');
        $name = Environment::getStringEnv('DB_NAME');
        $user = Environment::getStringEnv('DB_USER');
        $password = Environment::getStringEnv('DB_PASSWORD');

        $this->pdo = new PDO(
            "pgsql:host={$host};port={$port};dbname={$name}",
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    public static function getInstance(): self
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        self::$instance = new self();

        return self::$instance;
    }
}
