<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Medicamento;
use App\Models\Lote;
use App\Models\MovimientoStock;
use App\Models\Establecimiento;
use App\Helpers\Session;
use App\Helpers\Flash;
use App\Helpers\Redirect;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

class FarmaciaController extends Controller
{
    private Medicamento $medicamentoModel;
    private Lote $loteModel;
    private MovimientoStock $movimientoModel;
    private Establecimiento $establecimientoModel;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::check();
        RoleMiddleware::requirePermission('farmacia');
        $this->medicamentoModel = new Medicamento();
        $this->loteModel = new Lote();
        $this->movimientoModel = new MovimientoStock();
        $this->establecimientoModel = new Establecimiento();
    }

    public function index(): void
    {
        $establecimientoId = (int)Session::get('establecimiento_id', 0);

        $lotesDelEstablecimiento = $this->loteModel->getByEstablecimiento($establecimientoId);
        $totalMedicamentos = count($lotesDelEstablecimiento);
        $lotesDisponibles = array_filter($lotesDelEstablecimiento, fn($l) => $l['estado'] === 'disponible');
        $stockTotal = array_sum(array_column($lotesDisponibles, 'cantidad'));

        $lotesProximosVencer = $this->loteModel->getProximosVencer(90);
        $lotesProximosFiltrados = array_filter($lotesProximosVencer, fn($l) => (int)$l['establecimiento_id'] === $establecimientoId);

        $stockBajo = $this->loteModel->getStockBajo(10);
        $stockBajoFiltrado = array_filter($stockBajo, fn($l) => (int)$l['establecimiento_id'] === $establecimientoId);

        $movimientosRecientes = $this->movimientoModel->getByEstablecimiento($establecimientoId, 1, 5);

        $title = 'Panel de Farmacia';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/farmacia/index.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function stock(): void
    {
        $establecimientoId = (int)Session::get('establecimiento_id', 0);
        $search = $_GET['search'] ?? '';
        $page = (int)($_GET['page'] ?? 1);

        if ($search) {
            $medicamentos = $this->medicamentoModel->search($search, $page);
            $medicamentoIds = array_column($medicamentos['data'], 'id');

            $lotesDelEstablecimiento = [];
            $stockPorMedicamento = [];

            if (!empty($medicamentoIds)) {
                $placeholders = implode(',', array_fill(0, count($medicamentoIds), '?'));
                $sql = "SELECT l.*, m.nombre as medicamento_nombre, m.principio_activo,
                        m.forma_farmaceutica, m.concentracion, m.unidad_medida
                        FROM lotes l
                        INNER JOIN medicamentos m ON l.medicamento_id = m.id
                        WHERE l.establecimiento_id = ? AND l.medicamento_id IN ({$placeholders})
                        AND l.estado = 'disponible' AND l.cantidad > 0
                        ORDER BY m.nombre ASC, l.fecha_vencimiento ASC";
                $params = array_merge([$establecimientoId], $medicamentoIds);
                $lotesDelEstablecimiento = $this->loteModel->query($sql, $params);

                foreach ($lotesDelEstablecimiento as $lote) {
                    $mid = $lote['medicamento_id'];
                    if (!isset($stockPorMedicamento[$mid])) {
                        $stockPorMedicamento[$mid] = [
                            'medicamento_id' => $mid,
                            'medicamento_nombre' => $lote['medicamento_nombre'],
                            'principio_activo' => $lote['principio_activo'],
                            'forma_farmaceutica' => $lote['forma_farmaceutica'],
                            'concentracion' => $lote['concentracion'],
                            'unidad_medida' => $lote['unidad_medida'],
                            'lotes' => [],
                            'stock_total' => 0
                        ];
                    }
                    $stockPorMedicamento[$mid]['lotes'][] = $lote;
                    $stockPorMedicamento[$mid]['stock_total'] += (int)$lote['cantidad'];
                }
            }
        } else {
            $sql = "SELECT l.*, m.nombre as medicamento_nombre, m.principio_activo,
                    m.forma_farmaceutica, m.concentracion, m.unidad_medida
                    FROM lotes l
                    INNER JOIN medicamentos m ON l.medicamento_id = m.id
                    WHERE l.establecimiento_id = :eid AND l.estado = 'disponible' AND l.cantidad > 0
                    ORDER BY m.nombre ASC, l.fecha_vencimiento ASC";
            $lotesDelEstablecimiento = $this->loteModel->query($sql, ['eid' => $establecimientoId]);

            $stockPorMedicamento = [];
            foreach ($lotesDelEstablecimiento as $lote) {
                $mid = $lote['medicamento_id'];
                if (!isset($stockPorMedicamento[$mid])) {
                    $stockPorMedicamento[$mid] = [
                        'medicamento_id' => $mid,
                        'medicamento_nombre' => $lote['medicamento_nombre'],
                        'principio_activo' => $lote['principio_activo'],
                        'forma_farmaceutica' => $lote['forma_farmaceutica'],
                        'concentracion' => $lote['concentracion'],
                        'unidad_medida' => $lote['unidad_medida'],
                        'lotes' => [],
                        'stock_total' => 0
                    ];
                }
                $stockPorMedicamento[$mid]['lotes'][] = $lote;
                $stockPorMedicamento[$mid]['stock_total'] += (int)$lote['cantidad'];
            }
        }

        $title = 'Stock de Medicamentos';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/farmacia/stock.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function ingreso(): void
    {
        $medicamentos = $this->medicamentoModel->where('activo = 1', []);
        $proveedores = $this->movimientoModel->query("SELECT DISTINCT proveedor FROM lotes WHERE proveedor IS NOT NULL AND proveedor != '' ORDER BY proveedor", []);

        $title = 'Registrar Ingreso de Stock';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/farmacia/ingreso.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function guardarIngreso(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Redirect::to('/farmacia/ingreso');
        }

        if (!$this->validateCsrf()) {
            Flash::error('Token de seguridad inválido');
            Redirect::to('/farmacia/ingreso');
        }

        $medicamentoId = (int)($_POST['medicamento_id'] ?? 0);
        $nroLote = trim($_POST['nro_lote'] ?? '');
        $fechaVencimiento = $_POST['fecha_vencimiento'] ?? '';
        $cantidad = (int)($_POST['cantidad'] ?? 0);
        $proveedor = trim($_POST['proveedor'] ?? '');
        $precioUnitario = (float)($_POST['precio_unitario'] ?? 0);
        $establecimientoId = (int)Session::get('establecimiento_id', 0);
        $usuarioId = (int)Session::getUserId();

        if ($medicamentoId <= 0 || empty($nroLote) || empty($fechaVencimiento) || $cantidad <= 0) {
            Flash::error('Todos los campos obligatorios deben ser completados');
            Redirect::to('/farmacia/ingreso');
        }

        if (strtotime($fechaVencimiento) <= strtotime(date('Y-m-d'))) {
            Flash::error('La fecha de vencimiento debe ser posterior a la fecha actual');
            Redirect::to('/farmacia/ingreso');
        }

        $loteExistente = $this->loteModel->queryOne(
            "SELECT * FROM lotes WHERE nro_lote = :nro AND establecimiento_id = :eid",
            ['nro' => $nroLote, 'eid' => $establecimientoId]
        );

        if ($loteExistente) {
            $this->loteModel->updateStock((int)$loteExistente['id'], $cantidad);

            $movimientoData = [
                'medicamento_id' => $medicamentoId,
                'lote_id' => (int)$loteExistente['id'],
                'cantidad' => $cantidad,
                'establecimiento_origen_id' => $establecimientoId,
                'establecimiento_destino_id' => $establecimientoId,
                'motivo' => 'ingreso_stock',
                'fecha_movimiento' => date('Y-m-d H:i:s'),
                'usuario_id' => $usuarioId
            ];

            $this->movimientoModel->registrarEntrada($movimientoData);
        } else {
            $loteNuevo = $this->loteModel->create([
                'medicamento_id' => $medicamentoId,
                'nro_lote' => $nroLote,
                'fecha_vencimiento' => $fechaVencimiento,
                'cantidad' => $cantidad,
                'cantidad_original' => $cantidad,
                'establecimiento_id' => $establecimientoId,
                'precio_unitario' => $precioUnitario,
                'proveedor' => $proveedor,
                'estado' => 'disponible'
            ]);

            $movimientoData = [
                'medicamento_id' => $medicamentoId,
                'lote_id' => (int)$loteNuevo['id'],
                'cantidad' => $cantidad,
                'establecimiento_origen_id' => $establecimientoId,
                'establecimiento_destino_id' => $establecimientoId,
                'motivo' => 'ingreso_stock',
                'fecha_movimiento' => date('Y-m-d H:i:s'),
                'usuario_id' => $usuarioId
            ];

            $this->movimientoModel->registrarEntrada($movimientoData);
        }

        Flash::success('Ingreso de stock registrado exitosamente');
        Redirect::to('/farmacia/stock');
    }

    public function salida(): void
    {
        $establecimientoId = (int)Session::get('establecimiento_id', 0);

        $lotes = $this->loteModel->query(
            "SELECT l.*, m.nombre as medicamento_nombre, m.principio_activo,
             m.forma_farmaceutica, m.concentracion, m.unidad_medida
             FROM lotes l
             INNER JOIN medicamentos m ON l.medicamento_id = m.id
             WHERE l.establecimiento_id = :eid AND l.estado = 'disponible' AND l.cantidad > 0
             ORDER BY m.nombre ASC, l.fecha_vencimiento ASC",
            ['eid' => $establecimientoId]
        );

        $title = 'Registrar Salida de Stock';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/farmacia/salida.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function guardarSalida(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Redirect::to('/farmacia/salida');
        }

        if (!$this->validateCsrf()) {
            Flash::error('Token de seguridad inválido');
            Redirect::to('/farmacia/salida');
        }

        $loteId = (int)($_POST['lote_id'] ?? 0);
        $cantidad = (int)($_POST['cantidad'] ?? 0);
        $motivo = trim($_POST['motivo'] ?? '');
        $referenciaId = (int)($_POST['referencia_id'] ?? 0) ?: null;
        $establecimientoId = (int)Session::get('establecimiento_id', 0);
        $usuarioId = (int)Session::getUserId();

        $lote = $this->loteModel->find($loteId);
        if (!$lote) {
            Flash::error('Lote no encontrado');
            Redirect::to('/farmacia/salida');
        }

        if ($cantidad <= 0) {
            Flash::error('La cantidad debe ser mayor a cero');
            Redirect::to('/farmacia/salida');
        }

        if ($cantidad > (int)$lote['cantidad']) {
            Flash::error('La cantidad excede el stock disponible del lote');
            Redirect::to('/farmacia/salida');
        }

        $movimientoData = [
            'medicamento_id' => (int)$lote['medicamento_id'],
            'lote_id' => $loteId,
            'cantidad' => $cantidad,
            'establecimiento_origen_id' => $establecimientoId,
            'establecimiento_destino_id' => null,
            'motivo' => $motivo,
            'referencia_id' => $referenciaId,
            'fecha_movimiento' => date('Y-m-d H:i:s'),
            'usuario_id' => $usuarioId
        ];

        $this->movimientoModel->registrarSalida($movimientoData);

        Flash::success('Salida de stock registrada exitosamente');
        Redirect::to('/farmacia/stock');
    }

    public function transferencia(): void
    {
        $establecimientoId = (int)Session::get('establecimiento_id', 0);

        $establecimientos = $this->establecimientoModel->where('activo = 1 AND id != :eid', ['eid' => $establecimientoId]);

        $lotes = $this->loteModel->query(
            "SELECT l.*, m.nombre as medicamento_nombre, m.principio_activo
             FROM lotes l
             INNER JOIN medicamentos m ON l.medicamento_id = m.id
             WHERE l.establecimiento_id = :eid AND l.estado = 'disponible' AND l.cantidad > 0
             ORDER BY m.nombre ASC",
            ['eid' => $establecimientoId]
        );

        $title = 'Transferencia entre Establecimientos';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/farmacia/transferencia.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function guardarTransferencia(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Redirect::to('/farmacia/transferencia');
        }

        if (!$this->validateCsrf()) {
            Flash::error('Token de seguridad inválido');
            Redirect::to('/farmacia/transferencia');
        }

        $loteId = (int)($_POST['lote_id'] ?? 0);
        $destinoId = (int)($_POST['establecimiento_destino_id'] ?? 0);
        $cantidad = (int)($_POST['cantidad'] ?? 0);
        $motivo = trim($_POST['motivo'] ?? 'transferencia');
        $establecimientoId = (int)Session::get('establecimiento_id', 0);
        $usuarioId = (int)Session::getUserId();

        if ($destinoId <= 0) {
            Flash::error('Debe seleccionar un establecimiento de destino');
            Redirect::to('/farmacia/transferencia');
        }

        $lote = $this->loteModel->find($loteId);
        if (!$lote) {
            Flash::error('Lote no encontrado');
            Redirect::to('/farmacia/transferencia');
        }

        if ($cantidad <= 0 || $cantidad > (int)$lote['cantidad']) {
            Flash::error('La cantidad debe ser mayor a cero y no exceder el stock disponible');
            Redirect::to('/farmacia/transferencia');
        }

        $movimientoData = [
            'medicamento_id' => (int)$lote['medicamento_id'],
            'lote_id' => $loteId,
            'cantidad' => $cantidad,
            'establecimiento_origen_id' => $establecimientoId,
            'establecimiento_destino_id' => $destinoId,
            'motivo' => $motivo,
            'fecha_movimiento' => date('Y-m-d H:i:s'),
            'usuario_id' => $usuarioId
        ];

        $this->movimientoModel->registrarTransferencia($movimientoData);

        Flash::success('Transferencia registrada exitosamente');
        Redirect::to('/farmacia/stock');
    }

    public function medicamentos(): void
    {
        $search = $_GET['search'] ?? '';
        $forma = $_GET['forma'] ?? '';
        $page = (int)($_GET['page'] ?? 1);

        if ($search) {
            $medicamentos = $this->medicamentoModel->search($search, $page);
        } elseif ($forma) {
            $resultados = $this->medicamentoModel->getByForma($forma);
            $medicamentos = [
                'data' => $resultados,
                'total' => count($resultados),
                'page' => 1,
                'pages' => 1
            ];
        } else {
            $medicamentos = $this->medicamentoModel->paginate($page);
        }

        $formas = $this->medicamentoModel->query("SELECT DISTINCT forma_farmaceutica FROM medicamentos WHERE activo = 1 ORDER BY forma_farmaceutica", []);

        $title = 'Medicamentos';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/farmacia/medicamentos.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function buscarAjax(): void
    {
        $term = $_GET['term'] ?? '';
        $forma = $_GET['forma'] ?? '';

        if (empty($term)) {
            $this->json(['results' => []]);
        }

        $searchTerm = "%{$term}%";
        $params = ['term' => $searchTerm, 'term2' => $searchTerm, 'term3' => $searchTerm];

        $sql = "SELECT id, nombre, principio_activo, forma_farmaceutica, concentracion, unidad_medida, codigo_nacional
                FROM medicamentos
                WHERE activo = 1 AND (nombre LIKE :term OR principio_activo LIKE :term2 OR codigo_nacional LIKE :term3)";

        if (!empty($forma)) {
            $sql .= " AND forma_farmaceutica = :forma";
            $params['forma'] = $forma;
        }

        $sql .= " ORDER BY nombre ASC LIMIT 20";

        $results = $this->medicamentoModel->query($sql, $params);

        $this->json(['results' => $results]);
    }

    public function lotes(): void
    {
        $establecimientoId = (int)Session::get('establecimiento_id', 0);
        $filtro = $_GET['filtro'] ?? 'todos';
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 20;

        switch ($filtro) {
            case 'vencidos':
                $sql = "SELECT l.*, m.nombre as medicamento_nombre, m.principio_activo,
                        m.forma_farmaceutica, m.concentracion
                        FROM lotes l
                        INNER JOIN medicamentos m ON l.medicamento_id = m.id
                        WHERE l.establecimiento_id = :eid AND l.fecha_vencimiento < :hoy AND l.cantidad > 0
                        ORDER BY l.fecha_vencimiento ASC";
                $params = ['eid' => $establecimientoId, 'hoy' => date('Y-m-d')];
                break;
            case 'proximo_vencer':
                $limite = date('Y-m-d', strtotime('+90 days'));
                $sql = "SELECT l.*, m.nombre as medicamento_nombre, m.principio_activo,
                        m.forma_farmaceutica, m.concentracion
                        FROM lotes l
                        INNER JOIN medicamentos m ON l.medicamento_id = m.id
                        WHERE l.establecimiento_id = :eid AND l.fecha_vencimiento BETWEEN :hoy AND :limite AND l.cantidad > 0
                        ORDER BY l.fecha_vencimiento ASC";
                $params = ['eid' => $establecimientoId, 'hoy' => date('Y-m-d'), 'limite' => $limite];
                break;
            case 'disponibles':
                $sql = "SELECT l.*, m.nombre as medicamento_nombre, m.principio_activo,
                        m.forma_farmaceutica, m.concentracion
                        FROM lotes l
                        INNER JOIN medicamentos m ON l.medicamento_id = m.id
                        WHERE l.establecimiento_id = :eid AND l.estado = 'disponible' AND l.cantidad > 0
                        ORDER BY l.fecha_vencimiento ASC";
                $params = ['eid' => $establecimientoId];
                break;
            case 'agotados':
                $sql = "SELECT l.*, m.nombre as medicamento_nombre, m.principio_activo,
                        m.forma_farmaceutica, m.concentracion
                        FROM lotes l
                        INNER JOIN medicamentos m ON l.medicamento_id = m.id
                        WHERE l.establecimiento_id = :eid AND (l.estado = 'agotado' OR l.cantidad <= 0)
                        ORDER BY l.fecha_vencimiento ASC";
                $params = ['eid' => $establecimientoId];
                break;
            default:
                $sql = "SELECT l.*, m.nombre as medicamento_nombre, m.principio_activo,
                        m.forma_farmaceutica, m.concentracion
                        FROM lotes l
                        INNER JOIN medicamentos m ON l.medicamento_id = m.id
                        WHERE l.establecimiento_id = :eid
                        ORDER BY l.fecha_vencimiento ASC";
                $params = ['eid' => $establecimientoId];
                break;
        }

        $todosLosLotes = $this->loteModel->query($sql, $params);

        $total = count($todosLosLotes);
        $pages = (int)ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $lotes = array_slice($todosLosLotes, $offset, $perPage);

        $lotesPaginados = [
            'data' => $lotes,
            'total' => $total,
            'page' => $page,
            'pages' => $pages
        ];

        $title = 'Gestión de Lotes';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/farmacia/lotes.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }

    public function alertas(): void
    {
        $establecimientoId = (int)Session::get('establecimiento_id', 0);
        $umbralStock = (int)($_GET['umbral'] ?? 10);
        $diasVencimiento = (int)($_GET['dias'] ?? 90);

        $stockBajo = $this->loteModel->getStockBajo($umbralStock);
        $stockBajoFiltrado = array_filter($stockBajo, fn($l) => (int)$l['establecimiento_id'] === $establecimientoId);

        $lotesProximosVencer = $this->loteModel->getProximosVencer($diasVencimiento);
        $lotesProximosFiltrados = array_filter($lotesProximosVencer, fn($l) => (int)$l['establecimiento_id'] === $establecimientoId);

        $lotesVencidos = $this->loteModel->query(
            "SELECT l.*, m.nombre as medicamento_nombre, m.principio_activo,
             m.forma_farmaceutica, m.concentracion
             FROM lotes l
             INNER JOIN medicamentos m ON l.medicamento_id = m.id
             WHERE l.establecimiento_id = :eid AND l.fecha_vencimiento < :hoy AND l.cantidad > 0
             ORDER BY l.fecha_vencimiento ASC",
            ['eid' => $establecimientoId, 'hoy' => date('Y-m-d')]
        );

        $title = 'Alertas de Farmacia';
        $csrf = $this->generateCsrf();

        ob_start();
        include __DIR__ . '/../views/farmacia/alertas.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/app_layout.php';
    }
}