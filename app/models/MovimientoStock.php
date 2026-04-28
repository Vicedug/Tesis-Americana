<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

class MovimientoStock extends Model
{
    protected string $table = 'movimientos_stock';
    protected array $fillable = [
        'tipo', 'medicamento_id', 'lote_id', 'cantidad',
        'establecimiento_origen_id', 'establecimiento_destino_id',
        'motivo', 'referencia_id', 'fecha_movimiento', 'usuario_id'
    ];

    public function __construct()
    {
        parent::__construct(Database::getConnection());
    }

    public function getByEstablecimiento(int $establecimientoId, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT ms.*, m.nombre as medicamento_nombre, l.nro_lote,
                eo.nombre as origen_nombre, ed.nombre as destino_nombre,
                u.nombre as usuario_nombre, u.apellido as usuario_apellido
                FROM {$this->table} ms
                INNER JOIN medicamentos m ON ms.medicamento_id = m.id
                LEFT JOIN lotes l ON ms.lote_id = l.id
                LEFT JOIN establecimientos eo ON ms.establecimiento_origen_id = eo.id
                LEFT JOIN establecimientos ed ON ms.establecimiento_destino_id = ed.id
                LEFT JOIN usuarios u ON ms.usuario_id = u.id
                WHERE (ms.establecimiento_origen_id = :eid1 OR ms.establecimiento_destino_id = :eid2)
                ORDER BY ms.fecha_movimiento DESC
                LIMIT :offset, :limit";

        $results = $this->query($sql, [
            'eid1' => $establecimientoId,
            'eid2' => $establecimientoId,
            'offset' => $offset,
            'limit' => $perPage
        ]);

        $countSql = "SELECT COUNT(*) as total FROM {$this->table}
                     WHERE establecimiento_origen_id = :eid1 OR establecimiento_destino_id = :eid2";
        $countResult = $this->queryOne($countSql, [
            'eid1' => $establecimientoId,
            'eid2' => $establecimientoId
        ]);

        $total = (int)($countResult['total'] ?? 0);

        return [
            'data' => $results,
            'total' => $total,
            'page' => $page,
            'pages' => (int)ceil($total / $perPage)
        ];
    }

    public function getByTipo(string $tipo, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT ms.*, m.nombre as medicamento_nombre, l.nro_lote,
                eo.nombre as origen_nombre, ed.nombre as destino_nombre,
                u.nombre as usuario_nombre, u.apellido as usuario_apellido
                FROM {$this->table} ms
                INNER JOIN medicamentos m ON ms.medicamento_id = m.id
                LEFT JOIN lotes l ON ms.lote_id = l.id
                LEFT JOIN establecimientos eo ON ms.establecimiento_origen_id = eo.id
                LEFT JOIN establecimientos ed ON ms.establecimiento_destino_id = ed.id
                LEFT JOIN usuarios u ON ms.usuario_id = u.id
                WHERE ms.tipo = :tipo
                ORDER BY ms.fecha_movimiento DESC
                LIMIT :offset, :limit";

        $results = $this->query($sql, [
            'tipo' => $tipo,
            'offset' => $offset,
            'limit' => $perPage
        ]);

        $countResult = $this->queryOne(
            "SELECT COUNT(*) as total FROM {$this->table} WHERE tipo = :tipo",
            ['tipo' => $tipo]
        );

        $total = (int)($countResult['total'] ?? 0);

        return [
            'data' => $results,
            'total' => $total,
            'page' => $page,
            'pages' => (int)ceil($total / $perPage)
        ];
    }

    public function registrarEntrada(array $data): array
    {
        $data['tipo'] = 'entrada';
        $data['fecha_movimiento'] = date('Y-m-d H:i:s');

        $movimiento = $this->create($data);

        if ($movimiento && isset($data['lote_id'])) {
            $loteModel = new Lote();
            $loteModel->updateStock((int)$data['lote_id'], (int)$data['cantidad']);
        }

        return $movimiento;
    }

    public function registrarSalida(array $data): array
    {
        $data['tipo'] = 'salida';
        $data['fecha_movimiento'] = date('Y-m-d H:i:s');
        $data['cantidad'] = abs((int)$data['cantidad']);

        $movimiento = $this->create($data);

        if ($movimiento && isset($data['lote_id'])) {
            $loteModel = new Lote();
            $loteModel->updateStock((int)$data['lote_id'], -(int)$data['cantidad']);
        }

        return $movimiento;
    }

    public function registrarTransferencia(array $data): array
    {
        $this->pdo->beginTransaction();

        try {
            $data['tipo'] = 'transferencia';
            $data['fecha_movimiento'] = date('Y-m-d H:i:s');

            $movimiento = $this->create($data);

            if ($movimiento && isset($data['lote_id'])) {
                $loteModel = new Lote();
                $loteModel->updateStock((int)$data['lote_id'], -(int)$data['cantidad']);
            }

            $loteModel = new Lote();
            $loteOrigen = $loteModel->find((int)$data['lote_id']);
            if ($loteOrigen) {
                $nuevoLoteData = [
                    'medicamento_id' => $loteOrigen['medicamento_id'],
                    'nro_lote' => $loteOrigen['nro_lote'],
                    'fecha_vencimiento' => $loteOrigen['fecha_vencimiento'],
                    'cantidad' => $data['cantidad'],
                    'cantidad_original' => $data['cantidad'],
                    'establecimiento_id' => $data['establecimiento_destino_id'],
                    'precio_unitario' => $loteOrigen['precio_unitario'],
                    'proveedor' => $loteOrigen['proveedor'],
                    'estado' => 'disponible'
                ];

                $lotesDisponibles = $loteModel->getDisponibles(
                    (int)$loteOrigen['medicamento_id'],
                    (int)$data['establecimiento_destino_id']
                );

                $loteEncontrado = null;
                foreach ($lotesDisponibles as $ld) {
                    if ($ld['nro_lote'] === $loteOrigen['nro_lote']
                        && $ld['fecha_vencimiento'] === $loteOrigen['fecha_vencimiento']) {
                        $loteEncontrado = $ld;
                        break;
                    }
                }

                if ($loteEncontrado) {
                    $loteModel->updateStock((int)$loteEncontrado['id'], (int)$data['cantidad']);
                } else {
                    $loteModel->create($nuevoLoteData);
                }
            }

            $this->pdo->commit();
            return $movimiento;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}