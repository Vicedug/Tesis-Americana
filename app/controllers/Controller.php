<?php declare(strict_types=1);

use App\Database;

abstract class Controller
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    protected function view(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $module = $data['module'] ?? $this->resolveModule();
        $path = "app/views/{$module}/{$template}.php";

        if (!file_exists($path)) {
            throw new RuntimeException("View not found: {$path}");
        }

        require "app/views/layouts/header.php";
        require $path;
        require "app/views/layouts/footer.php";
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function validateCsrf(): bool
    {
        $token = $_POST['_csrf'] ?? '';
        $sessionToken = $_SESSION['_csrf'] ?? '';

        if (hash_equals($sessionToken, $token)) {
            unset($_SESSION['_csrf']);
            return true;
        }

        return false;
    }

    protected function generateCsrf(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['_csrf'] = $token;
        return $token;
    }

    private function resolveModule(): string
    {
        $class = static::class;
        $short = (new ReflectionClass($class))->getShortName();
        return strtolower(preg_replace('/Controller$/', '', $short));
    }
}