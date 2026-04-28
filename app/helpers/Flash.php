<?php declare(strict_types=1);

class Flash
{
    public static function success(string $message): void
    {
        self::add('success', $message);
    }

    public static function error(string $message): void
    {
        self::add('error', $message);
    }

    public static function warning(string $message): void
    {
        self::add('warning', $message);
    }

    public static function info(string $message): void
    {
        self::add('info', $message);
    }

    public static function get(): array
    {
        $messages = $_SESSION['_flash_messages'] ?? [];
        unset($_SESSION['_flash_messages']);
        return $messages;
    }

    public static function clear(): void
    {
        unset($_SESSION['_flash_messages']);
    }

    private static function add(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['_flash_messages'][] = [
            'type' => $type,
            'text' => $message,
        ];
    }
}