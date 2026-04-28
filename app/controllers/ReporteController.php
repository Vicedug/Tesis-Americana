<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Dispensacion;
use App\Models\Lote;
use App\Models\MovimientoStock;
use App\Models\Trazabilidad;
use App\Helpers\Session;
use App\Helpers\Flash;
use App\Helpers\Redirect;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

class ReporteController extends Controller
{
    private Dispensacion $dispensacionModel;
    private Lote $loteModel;
    private MovimientoStock $movimientoModel;
    private Trazabilidad $trazabilidadModel;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::check();
        RoleMiddleware::requirePermission('reportes');
        $this->dispensacionModel = new Dispensacion();
        $this->loteModel = new Lote();
        $this->movimientoModel = new MovimientoStock();
        $this->trazabilidadModel = new Trazabilidad();
    }

    public function index(): void
    {
        $title = 'Reportes';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/reportes/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function consumo(): void
    {
        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-d');
        $results = $this->dispensacionModel->getByFechaRange($desde, $hasta);

        $title = 'Reporte de Consumo';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/reportes/consumo.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function stock(): void
    {
        $establecimientoId = (int)($_GET['establecimiento_id'] ?? Session::get('establecimiento_id') ?? 0);
        $lotes = $establecimientoId > 0
            ? $this->loteModel->getByEstablecimiento($establecimientoId)
            : $this->loteModel->all();

        $establecimientoModel = new \App\Models\Establecimiento();
        $establecimientos = $establecimientoModel->all();

        $title = 'Reporte de Stock';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/reportes/stock.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function auditoria(): void
    {
        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-d');
        $movimientos = $this->movimientoModel->getByFechaRange($desde, $hasta);

        $title = 'Reporte de Auditoría';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/reportes/auditoria.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function trazabilidad(): void
    {
        $pacienteId = (int)($_GET['paciente_id'] ?? 0);
        $desde = $_GET['desde'] ?? '';
        $hasta = $_GET['hasta'] ?? '';
        $results = [];

        if ($pacienteId > 0) {
            $results = $this->trazabilidadModel->getByPaciente($pacienteId);
        } elseif ($desde && $hasta) {
            $results = $this->trazabilidadModel->getByFechaRange($desde, $hasta);
        }

        $title = 'Reporte de Trazabilidad';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/reportes/trazabilidad.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function exportarExcel(): void
    {
        Flash::info('Exportación Excel en desarrollo. Se habilitará próximamente.');
        Redirect::back();
    }

    public function exportarPdf(): void
    {
        Flash::info('Exportación PDF en desarrollo. Se habilitará próximamente.');
        Redirect::back();
    }
}