<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

class Lote extends Model
{
    protected string $table = 'lotes';
    protected array $fillable = [
        'medicamento_id', 'nro_lote', 'fecha_vencimiento', 'cantidad',
        'cantidad_original', 'establecimiento_id', 'precio_unitario',
        'proveedor', 'estado'
    ];

    public function __construct()
    {
        parent::__construct(Database::getConnection());
    }

    public function getByMedicamento(int $medicamentoId): array
    {
        $sql = "SELECT l.*, m.nombre as medicamento_nombre, m.principio_activo
                FROM {$this->table} l
                INNER JOIN medicamentos m ON l.medicamento_id = m.id
                WHERE l.medicamento_id = :medicamento_id
                ORDER BY l.fecha_vencimiento ASC";

        return $this->query($sql, ['medicamento_id' => $medicamentoId]);
    }

    public function getByEstablecimiento(int $establecimientoId): array
    {
        $sql = "SELECT l.*, m.nombre as medicamento_nombre, m.principio_activo,
                m.forma_farmaceutica, m.concentracion, m.unidad_medida
                FROM {$this->table} l
                INNER JOIN medicamentos m ON l.medicamento_id = m.id
                WHERE l.establecimiento_id = :establecimiento_id
                ORDER BY l.fecha_vencimiento ASC";

        return $this->query($sql, ['establecimiento_id' => $establecimientoId]);
    }

    public function getDisponibles(int $medicamentoId, int $establecimientoId): array
    {
        $sql = "SELECT l.*, m.nombre as medicamento_nombre, m.principio_activo
                FROM {$this->table} l
                INNER JOIN medicamentos m ON l.medicamento_id = m.id
                WHERE l.medicamento_id = :medicamento_id
                AND l.establecimiento_id = :establecimiento_id
                AND l.estado = 'disponible'
                AND l.fecha_vencimiento > :hoy
                AND l.cantidad > 0
                ORDER BY l.fecha_vencimiento ASC";

        return $this->query($sql, [
            'medicamento_id' => $medicamentoId,
            'establecimiento_id' => $establecimientoId,
            'hoy' => date('Y-m-d')
        ]);
    }

    public function getProximosVencer(int $dias = 90): array
    {
        $limite = date('Y-m-d', strtotime("+{$dias} days"));

        $sql = "SELECT l.*, m.nombre as medicamento_nombre, m.principio_activo,
                m.forma_farmaceutica, e.nombre as establecimiento_nombre
                FROM {$this->table} l
                INNER JOIN medicamentos m ON l.medicamento_id = m.id
                INNER JOIN establecimientos e ON l.establecimiento_id = e.id
                WHERE l.estado = 'disponible'
                AND l.fecha_vencimiento <= :limite
                AND l.fecha_vencimiento > :hoy
                AND l.cantidad > 0
                ORDER BY l.fecha_vencimiento ASC";

        return $this->query($sql, [
            'limite' => $limite,
            'hoy' => date('Y-m-d')
        ]);
    }

    public function updateStock(int $id, int $cantidad): ?array
    {
        $lote = $this->find($id);
        if (!$lote) {
            return null;
        }

        $nuevaCantidad = $lote['cantidad'] + $cantidad;

        $estado = $lote['estado'];
        if ($nuevaCantidad <= 0) {
            $estado = 'agotado';
            $nuevaCantidad = 0;
        } elseif ($nuevaCantidad > 0 && $lote['estado'] === 'agotado') {
            $estado = 'disponible';
        }

        return $this->update($id, [
            'cantidad' => $nuevaCantidad,
            'estado' => $estado
        ]);
    }

    public function getStockBajo(int $umbral = 10): array
    {
        $sql = "SELECT l.*, m.nombre as medicamento_nombre, m.principio_activo,
                m.forma_farmaceutica, e.nombre as establecimiento_nombre
                FROM {$this->table} l
                INNER JOIN medicamentos m ON l.medicamento_id = m.id
                INNER JOIN establecimientos e ON l.establecimiento_id = e.id
                WHERE l.estado = 'disponible'
                AND l.cantidad <= :umbral
                AND l.cantidad > 0
                ORDER BY l.cantidad ASC";

        return $this->query($sql, ['umbral' => $umbral]);
    }
}