<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

class RecetaMedicamento extends Model
{
    protected string $table = 'receta_medicamentos';
    protected array $fillable = [
        'receta_id', 'medicamento_id', 'cantidad_prescrita', 'indicaciones'
    ];

    public function __construct()
    {
        parent::__construct(Database::getConnection());
    }

    public function getByReceta(int $recetaId): array
    {
        $sql = "SELECT rm.*, m.nombre AS medicamento_nombre, m.principio_activo,
                    m.forma_farmaceutica, m.concentracion, m.codigo_registro
                FROM {$this->table} rm
                LEFT JOIN medicamentos m ON rm.medicamento_id = m.id
                WHERE rm.receta_id = :receta_id
                ORDER BY rm.id";

        return $this->query($sql, ['receta_id' => $recetaId]);
    }
}
