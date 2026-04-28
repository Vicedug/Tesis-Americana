<?php

declare(strict_types=1);

class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;

    private function __construct()
    {
        $this->connect();
    }

    private function connect(): void
    {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $name = $_ENV['DB_DATABASE'] ?? '';
        $user = $_ENV['DB_USERNAME'] ?? 'root';
        $pass = $_ENV['DB_PASSWORD'] ?? '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

        try {
            $this->connection = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Error de conexión a la base de datos: ' . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    private function __clone() {}

    public function __wakeup(): void
    {
        throw new RuntimeException('Cannot unserialize singleton');
    }
}
