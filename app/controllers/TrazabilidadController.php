<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Trazabilidad;
use App\Models\Alerta;
use App\Models\Lote;
use App\Models\Dispensacion;
use App\Helpers\Session;
use App\Helpers\Flash;
use App\Helpers\Redirect;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

class TrazabilidadController extends Controller
{
    private Trazabilidad $trazabilidadModel;
    private Alerta $alertaModel;
    private Lote $loteModel;
    private Dispensacion $dispensacionModel;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::check();
        RoleMiddleware::requirePermission('trazabilidad');
        $this->trazabilidadModel = new Trazabilidad();
        $this->alertaModel = new Alerta();
        $this->loteModel = new Lote();
        $this->dispensacionModel = new Dispensacion();
    }

    public function index(): void
    {
        $title = 'Trazabilidad';
        $csrf = $this->generateCsrf();

        $stats = [
            'total_trazabilidades' => $this->trazabilidadModel->count(),
            'total_pacientes' => (new \App\Models\Paciente())->count(),
            'total_dispensaciones_hoy' => $this->dispensacionModel->countToday(),
        ];

        ob_start();
        include __DIR__ . '/../views/trazabilidad/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function seguimiento(): void
    {
        $type = $_GET['type'] ?? 'paciente';
        $value = $_GET['value'] ?? '';
        $results = [];

        if ($value) {
            $results = $this->trazabilidadModel->search($type, $value);
        }

        $title = 'Seguimiento de Trazabilidad';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/trazabilidad/seguimiento.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function historial(): void
    {
        $page = (int)($_GET['page'] ?? 1);
        $desde = $_GET['desde'] ?? '';
        $hasta = $_GET['hasta'] ?? '';
        $results = [];

        if ($desde && $hasta) {
            $results = $this->trazabilidadModel->getByFechaRange($desde, $hasta);
        } else {
            $results = $this->trazabilidadModel->paginate($page);
        }

        $title = 'Historial de Trazabilidad';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/trazabilidad/historial.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function alertas(): void
    {
        $vencimientoAlertas = $this->alertaModel->getVencimientoProximo();
        $stockBajoAlertas = $this->alertaModel->getStockBajo();
        $todasAlertas = $this->alertaModel->getActivas();

        $title = 'Alertas del Sistema';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/trazabilidad/alertas.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function detalle(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $trace = $this->trazabilidadModel->getCompleteChain($id);

        if (!$trace) {
            Flash::error('Registro de trazabilidad no encontrado');
            Redirect::to('/trazabilidad');
        }

        $title = 'Detalle de Trazabilidad';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/trazabilidad/detalle.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function buscarAjax(): void
    {
        $type = $_GET['type'] ?? 'paciente';
        $value = $_GET['value'] ?? '';

        if (empty($value)) {
            $this->json([]);
            return;
        }

        $results = $this->trazabilidadModel->search($type, $value);
        $this->json($results);
    }
}