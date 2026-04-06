<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('envv')) {
    function envv(string $key, $default = null) {
        static $env;
        if ($env === null) {
            $env = [];
            $path = __DIR__ . '/../.env';
            if (file_exists($path)) {
                foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
                    [$k, $v] = explode('=', $line, 2);
                    $env[trim($k)] = trim($v);
                }
            }
        }
        return $env[$key] ?? $default;
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string {
        return rtrim(__DIR__ . '/..' . ($path ? '/' . ltrim($path, '/') : ''), '/');
    }
}

if (!function_exists('view')) {
    function view(string $name, array $data = []) {
        extract($data);
        $file = base_path('views/' . $name . '.php');
        if (!file_exists($file)) {
            http_response_code(500);
            echo "View {$name} tidak ditemukan.";
            exit;
        }
        include $file;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path) {
        header("Location: {$path}");
        exit;
    }
}

if (!function_exists('flash')) {
    function flash(string $key, $val = null) {
        if ($val === null) {
            if (isset($_SESSION['flash'][$key])) {
                $msg = $_SESSION['flash'][$key];
                unset($_SESSION['flash'][$key]);
                return $msg;
            }
            return null;
        }
        $_SESSION['flash'][$key] = $val;
    }
}

if (!function_exists('current_user')) {
    function current_user(): ?array {
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('require_login')) {
    function require_login(string $role = null) {
        $u = current_user();
        if (!$u) redirect('/login');
        if ($role && $u['role'] !== $role) redirect('/');
    }
}

if (!function_exists('is_post')) {
    function is_post(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        if (!isset($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(16));
        return $_SESSION['_csrf'];
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_csrf'] ?? '';
            if (!$token || $token !== ($_SESSION['_csrf'] ?? null)) {
                http_response_code(419);
                echo "CSRF token tidak valid. Silakan muat ulang halaman.";
                exit;
            }
        }
    }
}

if (!function_exists('old')) {
    function old($key, $default = '') {
        return $_SESSION['_old'][$key] ?? $default;
    }
}

if (!function_exists('remember_old')) {
    function remember_old() {
        $_SESSION['_old'] = $_POST;
    }
}

if (!function_exists('clear_old')) {
    function clear_old() {
        unset($_SESSION['_old']);
    }
}
