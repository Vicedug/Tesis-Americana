<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Consulta;
use App\Models\Receta;
use App\Helpers\Session;
use App\Helpers\Flash;
use App\Helpers\Redirect;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

class ConsultaController extends Controller
{
    private Consulta $consultaModel;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::check();
        RoleMiddleware::requirePermission('consulta');
        $this->consultaModel = new Consulta();
    }

    public function index(): void
    {
        $page = (int)($_GET['page'] ?? 1);
        $search = trim($_GET['search'] ?? '');
        $estado = $_GET['estado'] ?? '';

        if ($search !== '' || $estado !== '') {
            $conditions = [];
            $params = [];

            if ($estado !== '') {
                $conditions[] = 'c.estado = :estado';
                $params['estado'] = $estado;
            }

            if ($search !== '') {
                $conditions[] = '(p.ci LIKE :search OR p.nombre LIKE :search2 OR p.apellido LIKE :search3 OR c.diagnostico LIKE :search4)';
                $params['search'] = "%{$search}%";
                $params['search2'] = "%{$search}%";
                $params['search3'] = "%{$search}%";
                $params['search4'] = "%{$search}%";
            }

            $whereClause = implode(' AND ', $conditions);
            $offset = ($page - 1) * 15;

            $countSql = "SELECT COUNT(*) AS total FROM consultas c
                         LEFT JOIN pacientes p ON c.paciente_id = p.id
                         WHERE {$whereClause}";
            $countResult = $this->consultaModel->queryOne($countSql, $params);
            $total = (int)($countResult['total'] ?? 0);
            $pages = (int)ceil($total / 15);

            $sql = "SELECT c.*, p.ci AS paciente_ci, p.nombre AS paciente_nombre, p.apellido AS paciente_apellido,
                        u.nombre AS profesional_nombre, u.apellido AS profesional_apellido,
                        e.nombre AS establecimiento_nombre
                    FROM consultas c
                    LEFT JOIN pacientes p ON c.paciente_id = p.id
                    LEFT JOIN usuarios u ON c.profesional_id = u.id
                    LEFT JOIN establecimientos e ON c.establecimiento_id = e.id
                    WHERE {$whereClause}
                    ORDER BY c.fecha_consulta DESC
                    LIMIT 15 OFFSET {$offset}";
            $data = $this->consultaModel->query($sql, $params);

            $consultas = [
                'data' => $data,
                'total' => $total,
                'page' => $page,
                'pages' => $pages,
            ];
        } else {
            $consultas = $this->consultaModel->paginate($page);
        }

        $title = 'Gestión de Consultas';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/consulta/index.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function crear(): void
    {
        $pacienteId = (int)($_GET['paciente_id'] ?? 0);
        $paciente = null;

        if ($pacienteId > 0) {
            $pacienteModel = new \App\Models\Paciente();
            $paciente = $pacienteModel->find($pacienteId);
        }

        $profesionalNombre = Session::get('user_name', '');
        $establecimientoId = Session::get('establecimiento_id', 0);

        $title = 'Registrar Consulta';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/consulta/registrar.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Redirect::to('/consulta');
        }

        if (!$this->validateCsrfToken()) {
            Flash::error('Token de seguridad inválido');
            Redirect::to('/consulta/crear');
        }

        $data = [
            'paciente_id' => (int)($_POST['paciente_id'] ?? 0),
            'profesional_id' => (int)(Session::get('user_id', 0)),
            'establecimiento_id' => (int)(Session::get('establecimiento_id', 0)),
            'diagnostico' => trim($_POST['diagnostico'] ?? ''),
            'observaciones' => trim($_POST['observaciones'] ?? ''),
            'fecha_consulta' => $_POST['fecha_consulta'] ?? date('Y-m-d H:i:s'),
            'estado' => 'activa',
        ];

        if ($data['paciente_id'] <= 0) {
            Flash::error('Debe seleccionar un paciente');
            Redirect::to('/consulta/crear');
        }

        if (empty($data['diagnostico'])) {
            Flash::error('El diagnóstico es obligatorio');
            Redirect::to('/consulta/crear');
        }

        if (empty($data['fecha_consulta'])) {
            $data['fecha_consulta'] = date('Y-m-d H:i:s');
        }

        $result = $this->consultaModel->create($data);

        if ($result) {
            Flash::success('Consulta registrada exitosamente');
            Redirect::to('/consulta/detalle?id=' . $result['id']);
        } else {
            Flash::error('Error al registrar la consulta');
            Redirect::to('/consulta/crear');
        }
    }

    public function detalle(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $consulta = $this->consultaModel->getWithDetails($id);

        if (!$consulta) {
            Flash::error('Consulta no encontrada');
            Redirect::to('/consulta');
        }

        $recetaModel = new Receta();
        $recetas = $recetaModel->where('consulta_id = :consulta_id', ['consulta_id' => $id]);

        $title = 'Detalle de Consulta';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/consulta/detalle.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function cerrar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Redirect::to('/consulta');
        }

        if (!$this->validateCsrfToken()) {
            Flash::error('Token de seguridad inválido');
            Redirect::to('/consulta');
        }

        $id = (int)($_POST['id'] ?? 0);
        $consulta = $this->consultaModel->find($id);

        if (!$consulta) {
            Flash::error('Consulta no encontrada');
            Redirect::to('/consulta');
        }

        if ($consulta['estado'] === 'cerrada') {
            Flash::warning('La consulta ya se encuentra cerrada');
            Redirect::to('/consulta/detalle?id=' . $id);
        }

        $result = $this->consultaModel->update($id, ['estado' => 'cerrada']);

        if ($result) {
            Flash::success('Consulta cerrada exitosamente');
        } else {
            Flash::error('Error al cerrar la consulta');
        }

        Redirect::to('/consulta/detalle?id=' . $id);
    }

    private function validateCsrfToken(): bool
    {
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['_csrf'] ?? '';

        if (hash_equals($sessionToken, $token)) {
            unset($_SESSION['_csrf']);
            return true;
        }

        return false;
    }
}
