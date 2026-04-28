<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

class Alerta extends Model
{
    protected string $table = 'alertas';
    protected array $fillable = [
        'tipo', 'establecimiento_id', 'medicamento_id', 'lote_id',
        'mensaje', 'prioridad', 'estado', 'fecha_alerta', 'atendida_por'
    ];

    public function __construct()
    {
        parent::__construct(Database::getConnection());
    }

    public function getActivas(int $establecimientoId = 0): array
    {
        if ($establecimientoId > 0) {
            return $this->where('estado = :estado AND establecimiento_id = :eid', ['estado' => 'activa', 'eid' => $establecimientoId]);
        }
        return $this->where('estado = :estado', ['estado' => 'activa']);
    }

    public function getByTipo(string $tipo): array
    {
        return $this->where('tipo = :tipo AND estado = :estado', ['tipo' => $tipo, 'estado' => 'activa']);
    }

    public function getVencimientoProximo(int $dias = 90): array
    {
        $sql = "SELECT a.*, m.nombre as medicamento_nombre, l.nro_lote, l.fecha_vencimiento, l.cantidad,
                       e.nombre as establecimiento_nombre
                FROM alertas a
                LEFT JOIN medicamentos m ON a.medicamento_id = m.id
                LEFT JOIN lotes l ON a.lote_id = l.id
                LEFT JOIN establecimientos e ON a.establecimiento_id = e.id
                WHERE a.tipo = 'vencimiento_proximo' AND a.estado = 'activa'
                ORDER BY a.prioridad ASC, l.fecha_vencimiento ASC";

        return $this->query($sql);
    }

    public function getStockBajo(): array
    {
        $sql = "SELECT a.*, m.nombre as medicamento_nombre, e.nombre as establecimiento_nombre
                FROM alertas a
                LEFT JOIN medicamentos m ON a.medicamento_id = m.id
                LEFT JOIN establecimientos e ON a.establecimiento_id = e.id
                WHERE a.tipo = 'stock_bajo' AND a.estado = 'activa'
                ORDER BY a.prioridad ASC";

        return $this->query($sql);
    }

    public function marcarAtendida(int $id, int $usuarioId): bool
    {
        return $this->update($id, [
            'estado' => 'atendida',
            'atendida_por' => $usuarioId
        ]);
    }
}