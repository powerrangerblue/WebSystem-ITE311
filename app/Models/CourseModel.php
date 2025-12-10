<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'course_code',
        'course_name',
        'description',
        'school_year',
        'semester',
        'schedule',
        'teacher_id',
        'status',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];
}
