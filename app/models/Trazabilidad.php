<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

class Trazabilidad extends Model
{
    protected string $table = 'trazabilidad';
    protected array $fillable = [
        'dispensacion_id', 'paciente_id', 'medicamento_id', 'lote_id',
        'establecimiento_id', 'consulta_id', 'receta_id', 'observaciones'
    ];

    public function __construct()
    {
        parent::__construct(Database::getConnection());
    }

    public function getByPaciente(int $pacienteId): array
    {
        $sql = "SELECT t.*, p.ci as paciente_ci, p.nombre as paciente_nombre, p.apellido as paciente_apellido,
                       m.nombre as medicamento_nombre, m.forma_farmaceutica, m.concentracion,
                       l.nro_lote, l.fecha_vencimiento,
                       e.nombre as establecimiento_nombre,
                       d.fecha_dispensacion, d.cantidad_dispensada, d.estado as dispensacion_estado,
                       r.codigo_receta, r.estado as receta_estado,
                       c.diagnostico, c.fecha_consulta
                FROM trazabilidad t
                LEFT JOIN pacientes p ON t.paciente_id = p.id
                LEFT JOIN medicamentos m ON t.medicamento_id = m.id
                LEFT JOIN lotes l ON t.lote_id = l.id
                LEFT JOIN establecimientos e ON t.establecimiento_id = e.id
                LEFT JOIN dispensaciones d ON t.dispensacion_id = d.id
                LEFT JOIN recetas r ON t.receta_id = r.id
                LEFT JOIN consultas c ON t.consulta_id = c.id
                WHERE t.paciente_id = :pid AND t.deleted_at IS NULL
                ORDER BY t.fecha_registro DESC";

        return $this->query($sql, ['pid' => $pacienteId]);
    }

    public function getByMedicamento(int $medicamentoId): array
    {
        $sql = "SELECT t.*, p.ci as paciente_ci, p.nombre as paciente_nombre, p.apellido as paciente_apellido,
                       m.nombre as medicamento_nombre, l.nro_lote, l.fecha_vencimiento,
                       d.fecha_dispensacion, d.cantidad_dispensada
                FROM trazabilidad t
                LEFT JOIN pacientes p ON t.paciente_id = p.id
                LEFT JOIN medicamentos m ON t.medicamento_id = m.id
                LEFT JOIN lotes l ON t.lote_id = l.id
                LEFT JOIN dispensaciones d ON t.dispensacion_id = d.id
                WHERE t.medicamento_id = :mid AND t.deleted_at IS NULL
                ORDER BY t.fecha_registro DESC";

        return $this->query($sql, ['mid' => $medicamentoId]);
    }

    public function getByLote(int $loteId): array
    {
        $sql = "SELECT t.*, p.ci as paciente_ci, p.nombre as paciente_nombre, p.apellido as paciente_apellido,
                       m.nombre as medicamento_nombre, l.nro_lote, l.fecha_vencimiento,
                       d.fecha_dispensacion, d.cantidad_dispensada
                FROM trazabilidad t
                LEFT JOIN pacientes p ON t.paciente_id = p.id
                LEFT JOIN medicamentos m ON t.medicamento_id = m.id
                LEFT JOIN lotes l ON t.lote_id = l.id
                LEFT JOIN dispensaciones d ON t.dispensacion_id = d.id
                WHERE t.lote_id = :lid AND t.deleted_at IS NULL
                ORDER BY t.fecha_registro DESC";

        return $this->query($sql, ['lid' => $loteId]);
    }

    public function getByFechaRange(string $desde, string $hasta): array
    {
        $sql = "SELECT t.*, p.ci as paciente_ci, p.nombre as paciente_nombre, p.apellido as paciente_apellido,
                       m.nombre as medicamento_nombre, l.nro_lote, l.fecha_vencimiento,
                       e.nombre as establecimiento_nombre,
                       d.fecha_dispensacion, d.cantidad_dispensada, d.estado as dispensacion_estado,
                       r.codigo_receta
                FROM trazabilidad t
                LEFT JOIN pacientes p ON t.paciente_id = p.id
                LEFT JOIN medicamentos m ON t.medicamento_id = m.id
                LEFT JOIN lotes l ON t.lote_id = l.id
                LEFT JOIN establecimientos e ON t.establecimiento_id = e.id
                LEFT JOIN dispensaciones d ON t.dispensacion_id = d.id
                LEFT JOIN recetas r ON t.receta_id = r.id
                WHERE t.fecha_registro BETWEEN :desde AND :hasta AND t.deleted_at IS NULL
                ORDER BY t.fecha_registro DESC";

        return $this->query($sql, ['desde' => $desde, 'hasta' => $hasta]);
    }

    public function getCompleteChain(int $dispensacionId): ?array
    {
        $sql = "SELECT t.*,
                       d.fecha_dispensacion, d.cantidad_dispensada, d.estado as dispensacion_estado, d.observaciones as dispensacion_obs,
                       p.ci as paciente_ci, p.nombre as paciente_nombre, p.apellido as paciente_apellido, p.asegurado,
                       m.nombre as medicamento_nombre, m.forma_farmaceutica, m.concentracion,
                       l.nro_lote, l.fecha_vencimiento, l.cantidad as lote_cantidad,
                       e.nombre as establecimiento_nombre,
                       c.diagnostico, c.fecha_consulta, c.observaciones as consulta_obs,
                       concat(cp.nombre, ' ', cp.apellido) as profesional_nombre,
                       r.codigo_receta, r.cobertura_validada, r.estado as receta_estado,
                       concat(fp.nombre, ' ', fp.apellido) as farmaceutico_nombre
                FROM trazabilidad t
                LEFT JOIN dispensaciones d ON t.dispensacion_id = d.id
                LEFT JOIN pacientes p ON t.paciente_id = p.id
                LEFT JOIN medicamentos m ON t.medicamento_id = m.id
                LEFT JOIN lotes l ON t.lote_id = l.id
                LEFT JOIN establecimientos e ON t.establecimiento_id = e.id
                LEFT JOIN consultas c ON t.consulta_id = c.id
                LEFT JOIN usuarios cp ON c.profesional_id = cp.id
                LEFT JOIN recetas r ON t.receta_id = r.id
                LEFT JOIN usuarios fp ON d.farmaceutico_id = fp.id
                WHERE t.dispensacion_id = :did AND t.deleted_at IS NULL";

        return $this->queryOne($sql, ['did' => $dispensacionId]);
    }

    public function search(string $type, string $value): array
    {
        switch ($type) {
            case 'paciente':
                $sql = "SELECT t.*, p.ci, p.nombre as paciente_nombre, p.apellido as paciente_apellido,
                               m.nombre as medicamento_nombre, l.nro_lote, l.fecha_vencimiento,
                               d.fecha_dispensacion, d.estado as dispensacion_estado
                        FROM trazabilidad t
                        LEFT JOIN pacientes p ON t.paciente_id = p.id
                        LEFT JOIN medicamentos m ON t.medicamento_id = m.id
                        LEFT JOIN lotes l ON t.lote_id = l.id
                        LEFT JOIN dispensaciones d ON t.dispensacion_id = d.id
                        WHERE (p.ci LIKE :val OR p.nombre LIKE :val2 OR p.apellido LIKE :val3) AND t.deleted_at IS NULL
                        ORDER BY t.fecha_registro DESC";
                $searchVal = "%{$value}%";
                return $this->query($sql, ['val' => $searchVal, 'val2' => $searchVal, 'val3' => $searchVal]);

            case 'medicamento':
                $sql = "SELECT t.*, p.ci, p.nombre as paciente_nombre, p.apellido as paciente_apellido,
                               m.nombre as medicamento_nombre, l.nro_lote, l.fecha_vencimiento,
                               d.fecha_dispensacion, d.estado as dispensacion_estado
                        FROM trazabilidad t
                        LEFT JOIN pacientes p ON t.paciente_id = p.id
                        LEFT JOIN medicamentos m ON t.medicamento_id = m.id
                        LEFT JOIN lotes l ON t.lote_id = l.id
                        LEFT JOIN dispensaciones d ON t.dispensacion_id = d.id
                        WHERE m.nombre LIKE :val AND t.deleted_at IS NULL
                        ORDER BY t.fecha_registro DESC";
                return $this->query($sql, ['val' => "%{$value}%"]);

            case 'lote':
                $sql = "SELECT t.*, p.ci, p.nombre as paciente_nombre, p.apellido as paciente_apellido,
                               m.nombre as medicamento_nombre, l.nro_lote, l.fecha_vencimiento,
                               d.fecha_dispensacion, d.estado as dispensacion_estado
                        FROM trazabilidad t
                        LEFT JOIN pacientes p ON t.paciente_id = p.id
                        LEFT JOIN medicamentos m ON t.medicamento_id = m.id
                        LEFT JOIN lotes l ON t.lote_id = l.id
                        LEFT JOIN dispensaciones d ON t.dispensacion_id = d.id
                        WHERE l.nro_lote LIKE :val AND t.deleted_at IS NULL
                        ORDER BY t.fecha_registro DESC";
                return $this->query($sql, ['val' => "%{$value}%"]);

            default:
                return [];
        }
    }
}