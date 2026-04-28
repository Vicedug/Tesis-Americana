<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

class Medicamento extends Model
{
    protected string $table = 'medicamentos';
    protected array $fillable = [
        'nombre', 'principio_activo', 'forma_farmaceutica', 'concentracion',
        'unidad_medida', 'codigo_nacional', 'activo'
    ];

    public function __construct()
    {
        parent::__construct(Database::getConnection());
    }

    public function search(string $term, int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $searchTerm = "%{$term}%";

        $sql = "SELECT * FROM {$this->table}
                WHERE (nombre LIKE :term OR principio_activo LIKE :term2 OR codigo_nacional LIKE :term3)
                ORDER BY nombre ASC
                LIMIT :offset, :limit";

        $results = $this->query($sql, [
            'term' => $searchTerm,
            'term2' => $searchTerm,
            'term3' => $searchTerm,
            'offset' => $offset,
            'limit' => $perPage
        ]);

        $countSql = "SELECT COUNT(*) as total FROM {$this->table}
                     WHERE (nombre LIKE :term OR principio_activo LIKE :term2 OR codigo_nacional LIKE :term3)";

        $countResult = $this->queryOne($countSql, [
            'term' => $searchTerm,
            'term2' => $searchTerm,
            'term3' => $searchTerm
        ]);

        $total = (int)($countResult['total'] ?? 0);

        return [
            'data' => $results,
            'total' => $total,
            'page' => $page,
            'pages' => (int)ceil($total / $perPage)
        ];
    }

    public function getByForma(string $forma): array
    {
        return $this->where('forma_farmaceutica = :forma AND activo = 1', ['forma' => $forma]);
    }

    public function toggleActivo(int $id): ?array
    {
        $medicamento = $this->find($id);
        if (!$medicamento) {
            return null;
        }

        $nuevoEstado = $medicamento['activo'] ? 0 : 1;
        return $this->update($id, ['activo' => $nuevoEstado]);
    }
}