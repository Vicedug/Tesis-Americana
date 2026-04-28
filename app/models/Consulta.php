<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

class Consulta extends Model
{
    protected string $table = 'consultas';
    protected array $fillable = [
        'paciente_id', 'profesional_id', 'establecimiento_id',
        'diagnostico', 'observaciones', 'fecha_consulta', 'estado'
    ];

    public function __construct()
    {
        parent::__construct(Database::getConnection());
    }

    public function getWithDetails(int $id): ?array
    {
        $sql = "SELECT c.*,
                    p.ci AS paciente_ci, p.nombre AS paciente_nombre, p.apellido AS paciente_apellido,
                    p.asegurado AS paciente_asegurado, p.numero_asegurado,
                    u.nombre AS profesional_nombre, u.apellido AS profesional_apellido,
                    e.nombre AS establecimiento_nombre
                FROM {$this->table} c
                LEFT JOIN pacientes p ON c.paciente_id = p.id
                LEFT JOIN usuarios u ON c.profesional_id = u.id
                LEFT JOIN establecimientos e ON c.establecimiento_id = e.id
                WHERE c.id = :id
                LIMIT 1";

        return $this->queryOne($sql, ['id' => $id]);
    }

    public function getByPaciente(int $pacienteId): array
    {
        $sql = "SELECT c.*, u.nombre AS profesional_nombre, u.apellido AS profesional_apellido,
                    e.nombre AS establecimiento_nombre
                FROM {$this->table} c
                LEFT JOIN usuarios u ON c.profesional_id = u.id
                LEFT JOIN establecimientos e ON c.establecimiento_id = e.id
                WHERE c.paciente_id = :paciente_id
                ORDER BY c.fecha_consulta DESC";

        return $this->query($sql, ['paciente_id' => $pacienteId]);
    }

    public function getByProfesional(int $profesionalId): array
    {
        $sql = "SELECT c.*, p.ci AS paciente_ci, p.nombre AS paciente_nombre, p.apellido AS paciente_apellido,
                    e.nombre AS establecimiento_nombre
                FROM {$this->table} c
                LEFT JOIN pacientes p ON c.paciente_id = p.id
                LEFT JOIN establecimientos e ON c.establecimiento_id = e.id
                WHERE c.profesional_id = :profesional_id
                ORDER BY c.fecha_consulta DESC";

        return $this->query($sql, ['profesional_id' => $profesionalId]);
    }
}
