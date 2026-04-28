<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Usuario;
use App\Helpers\Session;
use App\Helpers\Flash;
use App\Helpers\Redirect;
use App\Middleware\AuthMiddleware;

class AuthController extends Controller
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        parent::__construct();
        $this->usuarioModel = new Usuario();
    }

    public function login(): void
    {
        if (Session::isLoggedIn()) {
            Redirect::to('/dashboard');
        }

        $title = 'Iniciar Sesión';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/auth/login.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/auth_layout.php';
    }

    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Redirect::to('/login');
        }

        if (!$this->validateCsrf()) {
            Flash::error('Token de seguridad inválido');
            Redirect::to('/login');
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            Flash::error('Por favor, complete todos los campos');
            Redirect::to('/login');
        }

        $user = $this->usuarioModel->authenticate($username, $password);

        if (!$user) {
            Flash::error('Credenciales incorrectas');
            Redirect::to('/login');
        }

        Session::set('user_id', $user['id']);
        Session::set('user_role', $user['rol']);
        Session::set('user_name', $user['nombre'] . ' ' . $user['apellido']);
        Session::set('establecimiento_id', $user['establecimiento_id']);
        Session::set('logged_in', true);

        Flash::success('Bienvenido, ' . $user['nombre']);
        Redirect::to('/dashboard');
    }

    public function logout(): void
    {
        Session::destroy();
        Redirect::to('/login');
    }
}