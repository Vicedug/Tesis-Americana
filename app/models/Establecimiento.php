<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

class Establecimiento extends Model
{
    protected string $table = 'establecimientos';
    protected array $fillable = [
        'nombre', 'tipo', 'direccion', 'ciudad', 'region',
        'telefono', 'activo'
    ];

    public function __construct()
    {
        parent::__construct(Database::getConnection());
    }

    public function getByTipo(string $tipo): array
    {
        return $this->where('tipo = :tipo AND activo = 1', ['tipo' => $tipo]);
    }

    public function getByRegion(string $region): array
    {
        return $this->where('region = :region AND activo = 1', ['region' => $region]);
    }

    public function getFarmacias(): array
    {
        return $this->getByTipo('farmacia');
    }
}