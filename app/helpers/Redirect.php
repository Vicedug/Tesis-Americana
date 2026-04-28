<?php declare(strict_types=1);

class Redirect
{
    public static function to(string $url): never
    {
        header("Location: {$url}");
        exit;
    }

    public static function back(): never
    {
        $url = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: {$url}");
        exit;
    }

    public static function with(string $key, mixed $value): static
    {
        $_SESSION['_flash'][$key] = $value;
        return new static();
    }

    public function __destruct()
    {
    }
}