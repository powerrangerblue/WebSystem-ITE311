<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table = 'announcements';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'title',
        'content',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'content' => 'required|min_length[10]'
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Announcement title is required',
            'min_length' => 'Title must be at least 3 characters long',
            'max_length' => 'Title cannot exceed 255 characters'
        ],
        'content' => [
            'required' => 'Announcement content is required',
            'min_length' => 'Content must be at least 10 characters long'
        ]
    ];

    protected $skipValidation = false;

    /**
     * Get all announcements ordered by creation date (newest first)
     *
     * @return array
     */
    public function getAllAnnouncements()
    {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Get announcement by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getAnnouncementById($id)
    {
        return $this->find($id);
    }

    /**
     * Create new announcement
     *
     * @param array $data
     * @return bool|int
     */
    public function createAnnouncement($data)
    {
        return $this->insert($data);
    }

    /**
     * Update announcement
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateAnnouncement($id, $data)
    {
        return $this->update($id, $data);
    }

    /**
     * Delete announcement
     *
     * @param int $id
     * @return bool
     */
    public function deleteAnnouncement($id)
    {
        return $this->delete($id);
    }
}
