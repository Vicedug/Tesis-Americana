<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

class Usuario extends Model
{
    protected string $table = 'usuarios';
    protected array $fillable = [
        'username', 'password', 'nombre', 'apellido', 'email',
        'rol', 'establecimiento_id', 'activo'
    ];

    public function __construct()
    {
        parent::__construct(Database::getConnection());
    }

    public function authenticate(string $username, string $password): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username AND activo = 1 LIMIT 1";
        $stmt = $this->queryOne($sql, ['username' => $username]);

        if ($stmt && password_verify($password, $stmt['password'])) {
            unset($stmt['password']);
            return $stmt;
        }

        return null;
    }

    public function getByEstablishment(int $establecimientoId): array
    {
        return $this->where(
            'establecimiento_id = :eid AND activo = 1',
            ['eid' => $establecimientoId]
        );
    }

    public function getByRole(string $role): array
    {
        return $this->where('rol = :rol AND activo = 1', ['rol' => $role]);
    }

    public function createWithHash(array $data): int
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        return $this->create($data);
    }

    public function updatePassword(int $id, string $newPassword): bool
    {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        return $this->update($id, ['password' => $hashed]);
    }
}