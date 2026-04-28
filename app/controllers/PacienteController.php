<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Paciente;
use App\Helpers\Session;
use App\Helpers\Flash;
use App\Helpers\Redirect;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

class PacienteController extends Controller
{
    private Paciente $pacienteModel;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::check();
        RoleMiddleware::requirePermission('paciente');
        $this->pacienteModel = new Paciente();
    }

    public function index(): void
    {
        $page = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';

        if ($search) {
            $pacientes = $this->pacienteModel->search($search, $page);
        } else {
            $pacientes = $this->pacienteModel->paginate($page);
        }

        $title = 'Gestión de Pacientes';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/paciente/index.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function buscar(): void
    {
        $ci = $_GET['ci'] ?? '';
        $title = 'Buscar Paciente';
        $csrf = $this->generateCsrf();

        $paciente = null;
        if ($ci) {
            $paciente = $this->pacienteModel->findByCI($ci);
        }

        ob_start();
        include __DIR__ . '/../views/paciente/buscar.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function buscarAjax(): void
    {
        $ci = $_GET['ci'] ?? '';
        $paciente = $this->pacienteModel->findByCI($ci);

        if ($paciente) {
            $this->json([
                'found' => true,
                'id' => $paciente['id'],
                'ci' => $paciente['ci'],
                'nombre' => $paciente['nombre'],
                'apellido' => $paciente['apellido'],
                'asegurado' => (bool)$paciente['asegurado']
            ]);
        } else {
            $this->json(['found' => false]);
        }
    }

    public function perfil(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $paciente = $this->pacienteModel->getWithEstablishment($id);

        if (!$paciente) {
            Flash::error('Paciente no encontrado');
            Redirect::to('/paciente');
        }

        $history = $this->pacienteModel->getHistory($id);
        $title = 'Perfil del Paciente';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/paciente/perfil.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function crear(): void
    {
        $title = 'Registrar Paciente';
        $csrf = $this->generateCsrf();

        $preCI = $_GET['ci'] ?? '';

        ob_start();
        include __DIR__ . '/../views/paciente/crear.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Redirect::to('/paciente');
        }

        if (!$this->validateCsrf()) {
            Flash::error('Token de seguridad inválido');
            Redirect::to('/paciente/crear');
        }

        $data = [
            'ci' => trim($_POST['ci'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
            'sexo' => $_POST['sexo'] ?? '',
            'direccion' => trim($_POST['direccion'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'asegurado' => isset($_POST['asegurado']) ? 1 : 0,
            'numero_asegurado' => trim($_POST['numero_asegurado'] ?? ''),
            'establecimiento_id' => (int)($_POST['establecimiento_id'] ?? Session::get('establecimiento_id'))
        ];

        if (empty($data['ci']) || empty($data['nombre']) || empty($data['apellido'])) {
            Flash::error('Los campos CI, nombre y apellido son obligatorios');
            Redirect::to('/paciente/crear');
        }

        $existing = $this->pacienteModel->findByCI($data['ci']);
        if ($existing) {
            Flash::error('Ya existe un paciente con ese CI');
            Redirect::to('/paciente/crear');
        }

        $id = $this->pacienteModel->create($data);

        if ($id) {
            Flash::success('Paciente registrado exitosamente');
            Redirect::to('/paciente/perfil?id=' . $id);
        } else {
            Flash::error('Error al registrar el paciente');
            Redirect::to('/paciente/crear');
        }
    }

    public function editar(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $paciente = $this->pacienteModel->find($id);

        if (!$paciente) {
            Flash::error('Paciente no encontrado');
            Redirect::to('/paciente');
        }

        $title = 'Editar Paciente';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/paciente/editar.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function actualizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Redirect::to('/paciente');
        }

        if (!$this->validateCsrf()) {
            Flash::error('Token de seguridad inválido');
            Redirect::to('/paciente');
        }

        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
            'sexo' => $_POST['sexo'] ?? '',
            'direccion' => trim($_POST['direccion'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'asegurado' => isset($_POST['asegurado']) ? 1 : 0,
            'numero_asegurado' => trim($_POST['numero_asegurado'] ?? ''),
            'establecimiento_id' => (int)($_POST['establecimiento_id'] ?? Session::get('establecimiento_id'))
        ];

        $result = $this->pacienteModel->update($id, $data);

        if ($result) {
            Flash::success('Paciente actualizado exitosamente');
        } else {
            Flash::error('Error al actualizar el paciente');
        }

        Redirect::to('/paciente/perfil?id=' . $id);
    }
}