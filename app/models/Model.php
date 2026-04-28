<?php declare(strict_types=1);

abstract class Model
{
    protected PDO $pdo;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected bool $timestamps = true;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(array $columns = ['*'], string $orderBy = 'id DESC'): array
    {
        $cols = implode(', ', $columns);
        $sql = "SELECT {$cols} FROM {$this->table} ORDER BY {$orderBy}";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int|string $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function findBy(string $column, mixed $value): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['value' => $value]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function where(string $conditions, array $params = []): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$conditions}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): array
    {
        $data = $this->filterFillable($data);

        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($k) => ":{$k}", array_keys($data)));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $this->find($this->lastInsertId());
    }

    public function update(int|string $id, array $data): array
    {
        $data = $this->filterFillable($data);

        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $setClause = implode(', ', array_map(fn($k) => "{$k} = :{$k}", array_keys($data)));
        $data[$this->primaryKey] = $id;

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :{$this->primaryKey}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $this->find($id);
    }

    public function delete(int|string $id): bool
    {
        if ($this->timestamps && $this->columnExists('deleted_at')) {
            $sql = "UPDATE {$this->table} SET deleted_at = :deleted_at WHERE {$this->primaryKey} = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['deleted_at' => date('Y-m-d H:i:s'), 'id' => $id]);
        }

        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function count(string $conditions = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        if ($conditions) {
            $sql .= " WHERE {$conditions}";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function paginate(int $page = 1, int $perPage = 15, string $conditions = '', array $params = []): array
    {
        $total = $this->count($conditions, $params);
        $pages = (int) ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM {$this->table}";
        if ($conditions) {
            $sql .= " WHERE {$conditions}";
        }
        $sql .= " ORDER BY {$this->primaryKey} DESC LIMIT {$perPage} OFFSET {$offset}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return [
            'data'  => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'page'  => $page,
            'pages' => $pages,
        ];
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->fillable));
    }

    protected function columnExists(string $column): bool
    {
        $stmt = $this->pdo->query("DESCRIBE {$this->table}");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return in_array($column, $columns, true);
    }
}