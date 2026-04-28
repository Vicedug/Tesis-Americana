<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\Receta;
use App\Models\Dispensacion;
use App\Models\Lote;
use App\Models\Alerta;
use App\Helpers\Session;
use App\Middleware\AuthMiddleware;

class DashboardController extends Controller
{
    private Paciente $pacienteModel;
    private Consulta $consultaModel;
    private Receta $recetaModel;
    private Dispensacion $dispensacionModel;
    private Lote $loteModel;
    private Alerta $alertaModel;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::check();
        $this->pacienteModel = new Paciente();
        $this->consultaModel = new Consulta();
        $this->recetaModel = new Receta();
        $this->dispensacionModel = new Dispensacion();
        $this->loteModel = new Lote();
        $this->alertaModel = new Alerta();
    }

    public function index(): void
    {
        $establecimientoId = (int)Session::get('establecimiento_id');

        $stats = [
            'pacientes_hoy' => $this->pacienteModel->count(),
            'consultas_hoy' => $this->consultaModel->count("DATE(fecha_consulta) = CURDATE()"),
            'dispensaciones_hoy' => $this->dispensacionModel->countToday($establecimientoId),
            'recetas_pendientes' => $this->recetaModel->count("estado = 'pendiente'"),
            'lotes_por_vencer' => count($this->loteModel->getProximosVencer()),
            'stock_bajo' => count($this->loteModel->getStockBajo()),
        ];

        $recentDispensations = $this->dispensacionModel->getRecent(10);
        $expiringLots = $this->loteModel->getProximosVencer(5);
        $lowStock = $this->loteModel->getStockBajo(5);
        $activeAlertas = $this->alertaModel->getActivas($establecimientoId);

        $title = 'Dashboard';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/dashboard/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }
}