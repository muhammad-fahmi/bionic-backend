<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'm_users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nama',
        'jabatan',
        'username',
        'password',
    ];

    public function getAllUser(): array
    {
        return $this->select('*')->findAll();
    }

    public function getUserById($id): ?array
    {
        return $this->select('id, nama, jabatan, username')->find($id);
    }

    public function updateUser($id, array $data): bool
    {
        return $this->update($id, $data);
    }

    public function isUsernameUnique(string $username, int $excludeId): bool
    {
        return $this->where('username', $username)
                    ->where('id !=', $excludeId)
                    ->countAllResults() === 0;
    }

    public function deleteUser($id): bool
    {
        return $this->delete($id);
    }

    public function createUser(array $data): bool
    {
        return $this->insert($data) !== false;
    }

    public function isUsernameExists(string $username): bool
    {
        return $this->where('username', $username)->countAllResults() > 0;
    }
}
