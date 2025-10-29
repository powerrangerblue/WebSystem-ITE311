<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'course_id',
        'file_name',
        'file_path',
        'created_at',
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';

    public function insertMaterial(array $data)
    {
        return $this->insert($data);
    }

    public function getMaterialsByCourse(int $courseId): array
    {
        return $this->where('course_id', $courseId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}


