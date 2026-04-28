<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Receta;
use App\Models\RecetaMedicamento;
use App\Models\Consulta;
use App\Helpers\Session;
use App\Helpers\Flash;
use App\Helpers\Redirect;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

class RecetaController extends Controller
{
    private Receta $recetaModel;
    private RecetaMedicamento $recetaMedicamentoModel;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::check();
        RoleMiddleware::requirePermission('receta');
        $this->recetaModel = new Receta();
        $this->recetaMedicamentoModel = new RecetaMedicamento();
    }

    public function index(): void
    {
        $page = (int)($_GET['page'] ?? 1);
        $search = trim($_GET['search'] ?? '');
        $estado = $_GET['estado'] ?? '';

        $recetas = $this->recetaModel->searchWithPagination($search, $estado, $page);

        $title = 'Gestión de Recetas';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/receta/index.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function crear(): void
    {
        $consultaId = (int)($_GET['consulta_id'] ?? 0);

        if ($consultaId <= 0) {
            Flash::error('Debe especificar una consulta');
            Redirect::to('/consulta');
        }

        $consultaModel = new Consulta();
        $consulta = $consultaModel->getWithDetails($consultaId);

        if (!$consulta) {
            Flash::error('Consulta no encontrada');
            Redirect::to('/consulta');
        }

        if ($consulta['estado'] === 'cerrada') {
            Flash::warning('No se puede crear receta para una consulta cerrada');
            Redirect::to('/consulta/detalle?id=' . $consultaId);
        }

        $existingReceta = $this->recetaModel->findBy('consulta_id', $consultaId);
        if ($existingReceta) {
            Flash::warning('Ya existe una receta para esta consulta');
            Redirect::to('/receta/detalle?id=' . $existingReceta['id']);
        }

        $medicamentos = $this->getMedicamentosList();

        $title = 'Crear Receta Médica';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/receta/crear.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Redirect::to('/receta');
        }

        if (!$this->validateCsrfToken()) {
            Flash::error('Token de seguridad inválido');
            Redirect::to('/receta');
        }

        $consultaId = (int)($_POST['consulta_id'] ?? 0);

        if ($consultaId <= 0) {
            Flash::error('Consulta no especificada');
            Redirect::to('/consulta');
        }

        $consultaModel = new Consulta();
        $consulta = $consultaModel->find($consultaId);

        if (!$consulta) {
            Flash::error('Consulta no encontrada');
            Redirect::to('/consulta');
        }

        $codigoReceta = $this->recetaModel->generarCodigo();

        $recetaData = [
            'consulta_id' => $consultaId,
            'codigo_receta' => $codigoReceta,
            'cobertura_validada' => 0,
            'estado' => 'emitida',
            'fecha_emision' => date('Y-m-d H:i:s'),
        ];

        $receta = $this->recetaModel->create($recetaData);

        if (!$receta) {
            Flash::error('Error al crear la receta');
            Redirect::to('/receta/crear?consulta_id=' . $consultaId);
        }

        $recetaId = (int)$receta['id'];

        $medicamentoIds = $_POST['medicamento_id'] ?? [];
        $cantidades = $_POST['cantidad_prescrita'] ?? [];
        $indicaciones = $_POST['indicaciones'] ?? [];

        if (empty($medicamentoIds) || !is_array($medicamentoIds)) {
            Flash::error('Debe agregar al menos un medicamento');
            $this->recetaModel->delete($recetaId);
            Redirect::to('/receta/crear?consulta_id=' . $consultaId);
        }

        $savedCount = 0;
        foreach ($medicamentoIds as $i => $medId) {
            $medId = (int)$medId;
            $cantidad = (int)($cantidades[$i] ?? 0);
            $indicacion = trim($indicaciones[$i] ?? '');

            if ($medId <= 0 || $cantidad <= 0) {
                continue;
            }

            $itemData = [
                'receta_id' => $recetaId,
                'medicamento_id' => $medId,
                'cantidad_prescrita' => $cantidad,
                'indicaciones' => $indicacion,
            ];

            $result = $this->recetaMedicamentoModel->create($itemData);
            if ($result) {
                $savedCount++;
            }
        }

        if ($savedCount === 0) {
            Flash::error('No se pudo registrar ningún medicamento en la receta');
            $this->recetaModel->delete($recetaId);
            Redirect::to('/receta/crear?consulta_id=' . $consultaId);
        }

        Flash::success("Receta {$codigoReceta} creada exitosamente con {$savedCount} medicamento(s)");
        Redirect::to('/receta/detalle?id=' . $recetaId);
    }

    public function validar(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $receta = $this->recetaModel->getWithDetails($id);

        if (!$receta) {
            Flash::error('Receta no encontrada');
            Redirect::to('/receta');
        }

        $title = 'Validar Cobertura - Receta ' . $receta['codigo_receta'];
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/receta/validar.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function validarCobertura(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Redirect::to('/receta');
        }

        if (!$this->validateCsrfToken()) {
            Flash::error('Token de seguridad inválido');
            Redirect::to('/receta');
        }

        $id = (int)($_POST['id'] ?? 0);
        $receta = $this->recetaModel->find($id);

        if (!$receta) {
            Flash::error('Receta no encontrada');
            Redirect::to('/receta');
        }

        $result = $this->recetaModel->update($id, [
            'cobertura_validada' => 1,
            'estado' => 'validada',
        ]);

        if ($result) {
            Flash::success('Cobertura validada exitosamente');
        } else {
            Flash::error('Error al validar la cobertura');
        }

        Redirect::to('/receta/detalle?id=' . $id);
    }

    public function rechazar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Redirect::to('/receta');
        }

        if (!$this->validateCsrfToken()) {
            Flash::error('Token de seguridad inválido');
            Redirect::to('/receta');
        }

        $id = (int)($_POST['id'] ?? 0);
        $receta = $this->recetaModel->find($id);

        if (!$receta) {
            Flash::error('Receta no encontrada');
            Redirect::to('/receta');
        }

        $result = $this->recetaModel->update($id, [
            'cobertura_validada' => 0,
            'estado' => 'rechazada',
        ]);

        if ($result) {
            Flash::success('Receta rechazada');
        } else {
            Flash::error('Error al rechazar la receta');
        }

        Redirect::to('/receta/detalle?id=' . $id);
    }

    public function detalle(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $receta = $this->recetaModel->getWithDetails($id);

        if (!$receta) {
            Flash::error('Receta no encontrada');
            Redirect::to('/receta');
        }

        $title = 'Detalle de Receta ' . $receta['codigo_receta'];
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/receta/detalle.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function buscarMedicamentosAjax(): void
    {
        $term = trim($_GET['q'] ?? '');

        if (strlen($term) < 2) {
            $this->json(['results' => []]);
        }

        $sql = "SELECT id, nombre, principio_activo, forma_farmaceutica, concentracion, codigo_registro
                FROM medicamentos
                WHERE (nombre LIKE :term OR principio_activo LIKE :term2 OR codigo_registro LIKE :term3)
                AND activo = 1
                ORDER BY nombre
                LIMIT 20";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'term' => "%{$term}%",
            'term2' => "%{$term}%",
            'term3' => "%{$term}%",
        ]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->json(['results' => $results]);
    }

    private function getMedicamentosList(): array
    {
        $sql = "SELECT id, nombre, principio_activo, forma_farmaceutica, concentracion, codigo_registro
                FROM medicamentos
                WHERE activo = 1
                ORDER BY nombre
                LIMIT 200";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
