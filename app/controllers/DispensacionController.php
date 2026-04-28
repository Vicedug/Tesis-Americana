<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Dispensacion;
use App\Models\Receta;
use App\Models\Lote;
use App\Models\Trazabilidad;
use App\Models\Paciente;
use App\Helpers\Session;
use App\Helpers\Flash;
use App\Helpers\Redirect;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

class DispensacionController extends Controller
{
    private Dispensacion $dispensacionModel;
    private Receta $recetaModel;
    private Lote $loteModel;
    private Trazabilidad $trazabilidadModel;
    private Paciente $pacienteModel;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::check();
        RoleMiddleware::requirePermission('dispensacion');
        $this->dispensacionModel = new Dispensacion();
        $this->recetaModel = new Receta();
        $this->loteModel = new Lote();
        $this->trazabilidadModel = new Trazabilidad();
        $this->pacienteModel = new Paciente();
    }

    public function index(): void
    {
        $page = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $estado = $_GET['estado'] ?? '';

        $conditions = '1=1 AND deleted_at IS NULL';
        $params = [];

        if ($estado) {
            $conditions .= ' AND estado = :estado';
            $params['estado'] = $estado;
        }

        $dispensaciones = $this->dispensacionModel->paginate($page, 15, $conditions, $params);
        $title = 'Dispensaciones';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/dispensacion/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function procesar(): void
    {
        $recetaId = $_GET['receta_id'] ?? null;
        $receta = null;
        $medicamentosReceta = [];

        if ($recetaId) {
            $receta = $this->recetaModel->getWithDetails((int)$recetaId);
            if ($receta) {
                $recetaMedModel = new \App\Models\RecetaMedicamento();
                $medicamentosReceta = $recetaMedModel->getByReceta((int)$recetaId);
            }
        }

        $title = 'Procesar Dispensación';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/dispensacion/procesar.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Redirect::to('/dispensacion');
        }

        if (!$this->validateCsrf()) {
            Flash::error('Token de seguridad inválido');
            Redirect::to('/dispensacion/procesar');
        }

        $recetaId = (int)($_POST['receta_id'] ?? 0);
        $pacienteId = (int)($_POST['paciente_id'] ?? 0);
        $medicamentosData = $_POST['medicamentos'] ?? [];

        if (empty($medicamentosData)) {
            Flash::error('No se han seleccionado medicamentos para dispensar');
            Redirect::to('/dispensacion/procesar?receta_id=' . $recetaId);
        }

        $establecimientoId = (int)Session::get('establecimiento_id');
        $farmaceuticoId = (int)Session::getUserId();

        try {
            $pdo = $this->pdo;
            $pdo->beginTransaction();

            foreach ($medicamentosData as $med) {
                $medicamentoId = (int)($med['medicamento_id'] ?? 0);
                $loteId = (int)($med['lote_id'] ?? 0);
                $cantidad = (int)($med['cantidad'] ?? 0);

                $lote = $this->loteModel->find($loteId);
                if (!$lote || $lote['cantidad'] < $cantidad) {
                    throw new \Exception('Stock insuficiente para el lote seleccionado');
                }

                if (strtotime($lote['fecha_vencimiento']) < time()) {
                    throw new \Exception('El lote ' . $lote['nro_lote'] . ' está vencido');
                }

                $dispensacionId = $this->dispensacionModel->create([
                    'receta_id' => $recetaId,
                    'paciente_id' => $pacienteId,
                    'medicamento_id' => $medicamentoId,
                    'lote_id' => $loteId,
                    'establecimiento_id' => $establecimientoId,
                    'farmaceutico_id' => $farmaceuticoId,
                    'cantidad_dispensada' => $cantidad,
                    'fecha_dispensacion' => date('Y-m-d H:i:s'),
                    'observaciones' => trim($med['observaciones'] ?? ''),
                    'estado' => 'completada'
                ]);

                $nuevaCantidad = $lote['cantidad'] - $cantidad;
                $this->loteModel->update($loteId, [
                    'cantidad' => $nuevaCantidad,
                    'estado' => $nuevaCantidad <= 0 ? 'agotado' : 'disponible'
                ]);

                $this->trazabilidadModel->create([
                    'dispensacion_id' => $dispensacionId,
                    'paciente_id' => $pacienteId,
                    'medicamento_id' => $medicamentoId,
                    'lote_id' => $loteId,
                    'establecimiento_id' => $establecimientoId,
                    'consulta_id' => null,
                    'receta_id' => $recetaId,
                    'observaciones' => 'Trazabilidad generada automáticamente'
                ]);
            }

            $this->recetaModel->update($recetaId, ['estado' => 'dispensada']);
            $pdo->commit();

            Flash::success('Dispensación registrada exitosamente');
            Redirect::to('/dispensacion/confirmacion?id=' . $dispensacionId);
        } catch (\Exception $e) {
            $pdo->rollBack();
            Flash::error('Error en la dispensación: ' . $e->getMessage());
            Redirect::to('/dispensacion/procesar?receta_id=' . $recetaId);
        }
    }

    public function confirmacion(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $dispensacion = $this->dispensacionModel->getWithDetails($id);

        if (!$dispensacion) {
            Flash::error('Dispensación no encontrada');
            Redirect::to('/dispensacion');
        }

        $dispensaciones = $this->dispensacionModel->getByReceta($dispensacion['receta_id']);

        $title = 'Confirmación de Dispensación';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/dispensacion/confirmacion.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function getByRecetaAjax(): void
    {
        $recetaId = (int)($_GET['receta_id'] ?? 0);
        $receta = $this->recetaModel->getWithDetails($recetaId);

        if ($receta) {
            $recetaMedModel = new \App\Models\RecetaMedicamento();
            $medicamentos = $recetaMedModel->getByReceta($recetaId);
            $this->json([
                'found' => true,
                'receta' => $receta,
                'medicamentos' => $medicamentos
            ]);
        } else {
            $this->json(['found' => false]);
        }
    }

    public function getLotesAjax(): void
    {
        $medicamentoId = (int)($_GET['medicamento_id'] ?? 0);
        $establecimientoId = (int)Session::get('establecimiento_id');
        $lotes = $this->loteModel->getDisponibles($medicamentoId, $establecimientoId);
        $this->json($lotes);
    }
}