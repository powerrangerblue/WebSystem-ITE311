<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'message',
        'is_read',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'message' => 'required|string',
        'is_read' => 'required|integer|in_list[0,1]'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required.',
            'integer' => 'User ID must be an integer.'
        ],
        'message' => [
            'required' => 'Message is required.',
            'string' => 'Message must be a string.'
        ],
        'is_read' => [
            'required' => 'Read status is required.',
            'integer' => 'Read status must be an integer.',
            'in_list' => 'Read status must be 0 or 1.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['setCreatedAt'];

    /**
     * Set created_at before insert
     */
    protected function setCreatedAt(array $data)
    {
        if (!isset($data['data']['created_at'])) {
            $data['data']['created_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Get notifications for a user
     *
     * @param int $user_id User ID
     * @param int $limit Number of notifications to retrieve
     * @return array Array of notifications
     */
    public function getUserNotifications($user_id, $limit = 10)
    {
        return $this->where('user_id', $user_id)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Mark a notification as read
     *
     * @param int $notification_id Notification ID
     * @param int $user_id User ID (for security)
     * @return bool True on success, false on failure
     */
    public function markAsRead($notification_id, $user_id)
    {
        return $this->where('id', $notification_id)
                    ->where('user_id', $user_id)
                    ->set(['is_read' => 1])
                    ->update();
    }

    /**
     * Get unread notification count for a user
     *
     * @param int $user_id User ID
     * @return int Number of unread notifications
     */
    public function getUnreadCount($user_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('is_read', 0)
                    ->countAllResults();
    }
}
