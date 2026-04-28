<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

class Paciente extends Model
{
    protected string $table = 'pacientes';
    protected array $fillable = [
        'ci', 'nombre', 'apellido', 'fecha_nacimiento', 'sexo',
        'direccion', 'telefono', 'asegurado', 'numero_asegurado',
        'establecimiento_id'
    ];

    public function __construct()
    {
        parent::__construct(Database::getConnection());
    }

    public function findByCI(string $ci): ?array
    {
        return $this->findBy('ci', $ci);
    }

    public function search(string $term, int $page = 1, int $perPage = 15): array
    {
        $sql = "SELECT p.*, e.nombre as establecimiento_nombre 
                FROM {$this->table} p 
                LEFT JOIN establecimientos e ON p.establecimiento_id = e.id 
                WHERE (p.ci LIKE :term OR p.nombre LIKE :term2 OR p.apellido LIKE :term3) 
                AND p.deleted_at IS NULL 
                ORDER BY p.apellido, p.nombre 
                LIMIT :offset, :limit";

        $offset = ($page - 1) * $perPage;
        $searchTerm = "%{$term}%";

        $results = $this->query($sql, [
            'term' => $searchTerm,
            'term2' => $searchTerm,
            'term3' => $searchTerm,
            'offset' => $offset,
            'limit' => $perPage
        ]);

        $countSql = "SELECT COUNT(*) as total FROM {$this->table} 
                     WHERE (ci LIKE :term OR nombre LIKE :term2 OR apellido LIKE :term3) 
                     AND deleted_at IS NULL";

        $countResult = $this->queryOne($countSql, [
            'term' => $searchTerm,
            'term2' => $searchTerm,
            'term3' => $searchTerm
        ]);

        return [
            'data' => $results,
            'total' => (int)($countResult['total'] ?? 0),
            'page' => $page,
            'pages' => (int)ceil(($countResult['total'] ?? 0) / $perPage)
        ];
    }

    public function getWithEstablishment(int $id): ?array
    {
        $sql = "SELECT p.*, e.nombre as establecimiento_nombre, e.ciudad 
                FROM {$this->table} p 
                LEFT JOIN establecimientos e ON p.establecimiento_id = e.id 
                WHERE p.id = :id AND p.deleted_at IS NULL";

        return $this->queryOne($sql, ['id' => $id]);
    }

    public function getHistory(int $pacienteId): array
    {
        $consultas = $this->query(
            "SELECT c.*, u.nombre as profesional_nombre, u.apellido as profesional_apellido 
             FROM consultas c 
             LEFT JOIN usuarios u ON c.profesional_id = u.id 
             WHERE c.paciente_id = :pid 
             ORDER BY c.fecha_consulta DESC",
            ['pid' => $pacienteId]
        );

        $dispensaciones = $this->query(
            "SELECT d.*, m.nombre as medicamento_nombre, l.nro_lote, l.fecha_vencimiento 
             FROM dispensaciones d 
             LEFT JOIN medicamentos m ON d.medicamento_id = m.id 
             LEFT JOIN lotes l ON d.lote_id = l.id 
             WHERE d.paciente_id = :pid 
             ORDER BY d.fecha_dispensacion DESC",
            ['pid' => $pacienteId]
        );

        return [
            'consultas' => $consultas,
            'dispensaciones' => $dispensaciones
        ];
    }

    public function isAsegurado(string $ci): bool
    {
        $paciente = $this->findBy('ci', $ci);
        return $paciente && (bool)$paciente['asegurado'];
    }
}