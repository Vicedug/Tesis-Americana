<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

class Dispensacion extends Model
{
    protected string $table = 'dispensaciones';
    protected array $fillable = [
        'receta_id', 'paciente_id', 'medicamento_id', 'lote_id',
        'establecimiento_id', 'farmaceutico_id', 'cantidad_dispensada',
        'fecha_dispensacion', 'observaciones', 'estado'
    ];

    public function __construct()
    {
        parent::__construct(Database::getConnection());
    }

    public function getWithDetails(int $id): ?array
    {
        $sql = "SELECT d.*, 
                    p.ci as paciente_ci, p.nombre as paciente_nombre, p.apellido as paciente_apellido,
                    m.nombre as medicamento_nombre, m.forma_farmaceutica, m.concentracion,
                    l.nro_lote, l.fecha_vencimiento,
                    r.codigo_receta, r.estado as receta_estado,
                    e.nombre as establecimiento_nombre,
                    u.nombre as farmaceutico_nombre, u.apellido as farmaceutico_apellido
                FROM dispensaciones d
                LEFT JOIN pacientes p ON d.paciente_id = p.id
                LEFT JOIN medicamentos m ON d.medicamento_id = m.id
                LEFT JOIN lotes l ON d.lote_id = l.id
                LEFT JOIN recetas r ON d.receta_id = r.id
                LEFT JOIN establecimientos e ON d.establecimiento_id = e.id
                LEFT JOIN usuarios u ON d.farmaceutico_id = u.id
                WHERE d.id = :id AND d.deleted_at IS NULL";

        return $this->queryOne($sql, ['id' => $id]);
    }

    public function getByPaciente(int $pacienteId): array
    {
        $sql = "SELECT d.*, m.nombre as medicamento_nombre, l.nro_lote, l.fecha_vencimiento
                FROM dispensaciones d
                LEFT JOIN medicamentos m ON d.medicamento_id = m.id
                LEFT JOIN lotes l ON d.lote_id = l.id
                WHERE d.paciente_id = :pid AND d.deleted_at IS NULL
                ORDER BY d.fecha_dispensacion DESC";

        return $this->query($sql, ['pid' => $pacienteId]);
    }

    public function getByReceta(int $recetaId): array
    {
        $sql = "SELECT d.*, m.nombre as medicamento_nombre, l.nro_lote
                FROM dispensaciones d
                LEFT JOIN medicamentos m ON d.medicamento_id = m.id
                LEFT JOIN lotes l ON d.lote_id = l.id
                WHERE d.receta_id = :rid AND d.deleted_at IS NULL
                ORDER BY d.fecha_dispensacion DESC";

        return $this->query($sql, ['rid' => $recetaId]);
    }

    public function getByEstablecimiento(int $establecimientoId, int $page = 1, int $perPage = 15): array
    {
        return $this->paginate($page, $perPage, 'establecimiento_id = :eid AND deleted_at IS NULL', ['eid' => $establecimientoId]);
    }

    public function getByFechaRange(string $desde, string $hasta): array
    {
        $sql = "SELECT d.*, p.ci as paciente_ci, p.nombre as paciente_nombre, p.apellido as paciente_apellido,
                       m.nombre as medicamento_nombre, l.nro_lote
                FROM dispensaciones d
                LEFT JOIN pacientes p ON d.paciente_id = p.id
                LEFT JOIN medicamentos m ON d.medicamento_id = m.id
                LEFT JOIN lotes l ON d.lote_id = l.id
                WHERE d.fecha_dispensacion BETWEEN :desde AND :hasta AND d.deleted_at IS NULL
                ORDER BY d.fecha_dispensacion DESC";

        return $this->query($sql, ['desde' => $desde, 'hasta' => $hasta]);
    }

    public function countToday(int $establecimientoId = 0): int
    {
        $today = date('Y-m-d');
        if ($establecimientoId > 0) {
            return (int)$this->queryOne(
                "SELECT COUNT(*) as total FROM {$this->table} WHERE DATE(fecha_dispensacion) = :today AND establecimiento_id = :eid AND deleted_at IS NULL",
                ['today' => $today, 'eid' => $establecimientoId]
            )['total'] ?? 0;
        }
        return (int)$this->queryOne(
            "SELECT COUNT(*) as total FROM {$this->table} WHERE DATE(fecha_dispensacion) = :today AND deleted_at IS NULL",
            ['today' => $today]
        )['total'] ?? 0;
    }

    public function getRecent(int $limit = 10): array
    {
        $sql = "SELECT d.*, p.ci as paciente_ci, p.nombre as paciente_nombre, p.apellido as paciente_apellido,
                       m.nombre as medicamento_nombre, l.nro_lote
                FROM dispensaciones d
                LEFT JOIN pacientes p ON d.paciente_id = p.id
                LEFT JOIN medicamentos m ON d.medicamento_id = m.id
                LEFT JOIN lotes l ON d.lote_id = l.id
                WHERE d.deleted_at IS NULL
                ORDER BY d.fecha_dispensacion DESC
                LIMIT :limit";

        return $this->query($sql, ['limit' => $limit]);
    }
}