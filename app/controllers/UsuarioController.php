<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Usuario;
use App\Models\Establecimiento;
use App\Helpers\Session;
use App\Helpers\Flash;
use App\Helpers\Redirect;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

class UsuarioController extends Controller
{
    private Usuario $usuarioModel;
    private Establecimiento $establecimientoModel;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::check();
        $this->usuarioModel = new Usuario();
        $this->establecimientoModel = new Establecimiento();
    }

    public function index(): void
    {
        RoleMiddleware::requirePermission('usuarios');
        $page = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $usuarios = $search
            ? $this->usuarioModel->where("nombre LIKE :s OR apellido LIKE :s2 OR username LIKE :s3", ['s' => "%$search%", 's2' => "%$search%", 's3' => "%$search%"])
            : $this->usuarioModel->paginate($page);

        $title = 'Gestión de Usuarios';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/usuarios/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function crear(): void
    {
        RoleMiddleware::requirePermission('usuarios');
        $establecimientos = $this->establecimientoModel->all();
        $title = 'Crear Usuario';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/usuarios/crear.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function guardar(): void
    {
        RoleMiddleware::requirePermission('usuarios');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->validateCsrf()) {
            Flash::error('Solicitud inválida');
            Redirect::to('/usuarios');
        }

        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'rol' => $_POST['rol'] ?? '',
            'establecimiento_id' => (int)($_POST['establecimiento_id'] ?? 0),
            'activo' => 1
        ];

        if (empty($data['username']) || empty($data['password']) || empty($data['nombre']) || empty($data['rol'])) {
            Flash::error('Todos los campos obligatorios deben completarse');
            Redirect::to('/usuarios/crear');
        }

        $id = $this->usuarioModel->createWithHash($data);
        if ($id) {
            Flash::success('Usuario creado exitosamente');
            Redirect::to('/usuarios');
        } else {
            Flash::error('Error al crear el usuario');
            Redirect::to('/usuarios/crear');
        }
    }

    public function editar(): void
    {
        RoleMiddleware::requirePermission('usuarios');
        $id = (int)($_GET['id'] ?? 0);
        $usuario = $this->usuarioModel->find($id);

        if (!$usuario) {
            Flash::error('Usuario no encontrado');
            Redirect::to('/usuarios');
        }

        $establecimientos = $this->establecimientoModel->all();
        $title = 'Editar Usuario';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/usuarios/editar.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function actualizar(): void
    {
        RoleMiddleware::requirePermission('usuarios');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->validateCsrf()) {
            Flash::error('Solicitud inválida');
            Redirect::to('/usuarios');
        }

        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'rol' => $_POST['rol'] ?? '',
            'establecimiento_id' => (int)($_POST['establecimiento_id'] ?? 0),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        if ($this->usuarioModel->update($id, $data)) {
            Flash::success('Usuario actualizado exitosamente');
        } else {
            Flash::error('Error al actualizar el usuario');
        }
        Redirect::to('/usuarios');
    }

    public function perfil(): void
    {
        $userId = (int)Session::getUserId();
        $usuario = $this->usuarioModel->find($userId);

        $title = 'Mi Perfil';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/usuarios/perfil.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function cambiarPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->validateCsrf()) {
            Flash::error('Solicitud inválida');
            Redirect::to('/usuarios/perfil');
        }

        $userId = (int)Session::getUserId();
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword !== $confirmPassword) {
            Flash::error('Las contraseñas no coinciden');
            Redirect::to('/usuarios/perfil');
        }

        $user = $this->usuarioModel->find($userId);
        if (!password_verify($currentPassword, $user['password'])) {
            Flash::error('Contraseña actual incorrecta');
            Redirect::to('/usuarios/profil');
        }

        if ($this->usuarioModel->updatePassword($userId, $newPassword)) {
            Flash::success('Contraseña actualizada exitosamente');
        } else {
            Flash::error('Error al actualizar la contraseña');
        }
        Redirect::to('/usuarios/perfil');
    }
}