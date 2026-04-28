<?php declare(strict_types=1);

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started === false && session_status() === PHP_SESSION_NONE) {
            session_start();
            self::$started = true;
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        self::start();
        return array_key_exists($key, $_SESSION);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        self::$started = false;
    }

    public static function flash(string $key, mixed $value): void
    {
        self::start();
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        self::start();
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function isLoggedIn(): bool
    {
        return self::has('user_id') && self::get('user_id') !== null;
    }

    public static function getUserId(): ?int
    {
        return self::get('user_id');
    }

    public static function getUserRole(): ?string
    {
        return self::get('user_role');
    }

    public static function getUserName(): ?string
    {
        return self::get('user_name');
    }
}