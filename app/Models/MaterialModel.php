<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $allowedFields = ['course_id', 'file_name', 'file_path'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function insertMaterial($data)
    {
        try {
            $result = $this->insert($data);
            if ($result === false) {
                log_message('error', 'Material insert failed: ' . implode(', ', $this->errors()));
            }
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Material insert exception: ' . $e->getMessage());
            return false;
        }
    }

    public function getMaterialsByCourse($course_id)
    {
        return $this->where('course_id', $course_id)->findAll();
    }

    public function getAllMaterials()
    {
        return $this->select('materials.*, courses.course_name, courses.course_code')
                    ->join('courses', 'courses.id = materials.course_id')
                    ->orderBy('materials.created_at', 'DESC')
                    ->findAll();
    }
}
