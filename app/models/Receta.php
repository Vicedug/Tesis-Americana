<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

class Receta extends Model
{
    protected string $table = 'recetas';
    protected array $fillable = [
        'consulta_id', 'codigo_receta', 'cobertura_validada', 'estado', 'fecha_emision'
    ];

    public function __construct()
    {
        parent::__construct(Database::getConnection());
    }

    public function getWithDetails(int $id): ?array
    {
        $sql = "SELECT r.*,
                    c.diagnostico, c.fecha_consulta, c.observaciones AS consulta_observaciones,
                    c.estado AS consulta_estado,
                    p.id AS paciente_id, p.ci AS paciente_ci, p.nombre AS paciente_nombre,
                    p.apellido AS paciente_apellido, p.asegurado AS paciente_asegurado,
                    p.numero_asegurado,
                    u.nombre AS profesional_nombre, u.apellido AS profesional_apellido,
                    e.nombre AS establecimiento_nombre
                FROM {$this->table} r
                LEFT JOIN consultas c ON r.consulta_id = c.id
                LEFT JOIN pacientes p ON c.paciente_id = p.id
                LEFT JOIN usuarios u ON c.profesional_id = u.id
                LEFT JOIN establecimientos e ON c.establecimiento_id = e.id
                WHERE r.id = :id
                LIMIT 1";

        $receta = $this->queryOne($sql, ['id' => $id]);

        if ($receta) {
            $receta['medicamentos'] = $this->getMedicamentosByReceta($id);
        }

        return $receta;
    }

    public function getByPaciente(int $pacienteId): array
    {
        $sql = "SELECT r.*, c.diagnostico, c.fecha_consulta,
                    p.nombre AS paciente_nombre, p.apellido AS paciente_apellido,
                    u.nombre AS profesional_nombre, u.apellido AS profesional_apellido
                FROM {$this->table} r
                LEFT JOIN consultas c ON r.consulta_id = c.id
                LEFT JOIN pacientes p ON c.paciente_id = p.id
                LEFT JOIN usuarios u ON c.profesional_id = u.id
                WHERE c.paciente_id = :paciente_id
                ORDER BY r.fecha_emision DESC";

        return $this->query($sql, ['paciente_id' => $pacienteId]);
    }

    public function getByEstado(string $estado): array
    {
        return $this->where('estado = :estado', ['estado' => $estado]);
    }

    public function generarCodigo(): string
    {
        $year = date('Y');
        $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE codigo_receta LIKE :pattern";
        $result = $this->queryOne($sql, ['pattern' => "REC-{$year}-%"]);
        $next = ($result['total'] ?? 0) + 1;
        $codigo = sprintf("REC-%s-%05d", $year, $next);

        $existing = $this->findBy('codigo_receta', $codigo);
        while ($existing !== null) {
            $next++;
            $codigo = sprintf("REC-%s-%05d", $year, $next);
            $existing = $this->findBy('codigo_receta', $codigo);
        }

        return $codigo;
    }

    public function getMedicamentosByReceta(int $recetaId): array
    {
        $sql = "SELECT rm.*, m.nombre AS medicamento_nombre, m.principio_activo,
                    m.forma_farmaceutica, m.concentracion
                FROM receta_medicamentos rm
                LEFT JOIN medicamentos m ON rm.medicamento_id = m.id
                WHERE rm.receta_id = :receta_id
                ORDER BY rm.id";

        return $this->query($sql, ['receta_id' => $recetaId]);
    }

    public function searchWithPagination(string $search, string $estado, int $page = 1, int $perPage = 15): array
    {
        $conditions = [];
        $params = [];

        if ($estado !== '') {
            $conditions[] = 'r.estado = :estado';
            $params['estado'] = $estado;
        }

        if ($search !== '') {
            $conditions[] = '(r.codigo_receta LIKE :search OR p.nombre LIKE :search2 OR p.apellido LIKE :search3 OR p.ci LIKE :search4)';
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
            $params['search4'] = "%{$search}%";
        }

        $whereClause = count($conditions) > 0 ? implode(' AND ', $conditions) : '';
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) AS total FROM {$this->table} r
                     LEFT JOIN consultas c ON r.consulta_id = c.id
                     LEFT JOIN pacientes p ON c.paciente_id = p.id";
        if ($whereClause !== '') {
            $countSql .= " WHERE {$whereClause}";
        }
        $countResult = $this->queryOne($countSql, $params);
        $total = (int)($countResult['total'] ?? 0);
        $pages = (int)ceil($total / $perPage);

        $sql = "SELECT r.*, c.diagnostico, c.fecha_consulta,
                    p.ci AS paciente_ci, p.nombre AS paciente_nombre, p.apellido AS paciente_apellido,
                    u.nombre AS profesional_nombre, u.apellido AS profesional_apellido
                FROM {$this->table} r
                LEFT JOIN consultas c ON r.consulta_id = c.id
                LEFT JOIN pacientes p ON c.paciente_id = p.id
                LEFT JOIN usuarios u ON c.profesional_id = u.id";
        if ($whereClause !== '') {
            $sql .= " WHERE {$whereClause}";
        }
        $sql .= " ORDER BY r.fecha_emision DESC LIMIT {$perPage} OFFSET {$offset}";

        $data = $this->query($sql, $params);

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
        ];
    }
}
